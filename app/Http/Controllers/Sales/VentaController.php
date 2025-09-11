<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Venta;
use App\Models\Sales\VentaDetalle;
use App\Models\Sales\VentaPago;
use App\Models\Inventory\Producto;
use App\Models\Cliente;
use App\Models\MetodoPago;
use App\Services\TenantService;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class VentaController extends Controller
{
    use AuthorizesRequests;

    protected TenantService $tenantService;
    protected ?VentaService $ventaService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
        // VentaService es opcional por si no existe aún
        $this->ventaService = app()->bound(VentaService::class) ? app(VentaService::class) : null;
    }

    /**
     * Display a listing of sales
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $empresaId = $this->tenantService->getEmpresaId();
            $sucursalId = $this->tenantService->getSucursalId();

            // Filtros
            $perPage = $request->get('per_page', 15);
            $estado = $request->get('estado');
            $fechaInicio = $request->get('fecha_inicio');
            $fechaFin = $request->get('fecha_fin');
            $clienteId = $request->get('cliente_id');
            $search = $request->get('search');

            // Query base
            $query = Venta::with(['cliente', 'usuario', 'detalles.producto', 'pagos.metodoPago'])
                ->forEmpresa($empresaId);

            // Aplicar filtros
            if ($sucursalId) {
                $query->forSucursal($sucursalId);
            }

            if ($estado) {
                $query->byEstado($estado);
            }

            if ($fechaInicio) {
                $fechaFin = $fechaFin ?: $fechaInicio;
                $query->byFecha($fechaInicio, $fechaFin);
            }

            if ($clienteId) {
                $query->where('cliente_id', $clienteId);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('numero_venta', 'like', "%{$search}%")
                      ->orWhere('numero_comprobante', 'like', "%{$search}%")
                      ->orWhereHas('cliente', function ($q2) use ($search) {
                          $q2->where('nombre', 'like', "%{$search}%")
                             ->orWhere('documento', 'like', "%{$search}%");
                      });
                });
            }

            // Ordenar por fecha más reciente
            $query->orderByDesc('fecha_venta');

            $ventas = $query->paginate($perPage);

            // Estadísticas del día
            $estadisticasHoy = [
                'total_ventas' => Venta::ventasDelDia($empresaId, $sucursalId)->count(),
                'total_ingresos' => Venta::ventasDelDia($empresaId, $sucursalId)->sum('total'),
                'ventas_pendientes' => Venta::ventasDelDia($empresaId, $sucursalId)->pendientes()->count(),
                'ticket_promedio' => Venta::ventasDelDia($empresaId, $sucursalId)->avg('total') ?? 0,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'ventas' => $ventas,
                    'estadisticas' => $estadisticasHoy,
                    'filtros' => [
                        'estados' => Venta::ESTADOS,
                        'tipos_comprobante' => Venta::TIPOS_COMPROBANTE,
                    ]
                ]);
            }

            return Inertia::render('Sales/Ventas/Index', [
                'ventas' => $ventas,
                'estadisticas' => $estadisticasHoy,
                'filtros' => [
                    'estados' => Venta::ESTADOS,
                    'tipos_comprobante' => Venta::TIPOS_COMPROBANTE,
                ],
                'clientes' => Cliente::forEmpresa($empresaId)->activos()->get(['id', 'nombre', 'documento']),
            ]);

        } catch (\Exception $e) {
            Log::error('Error en VentaController@index: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al cargar ventas'], 500);
            }
            
            return Inertia::render('Sales/Ventas/Index', [
                'ventas' => [],
                'error' => 'Error al cargar las ventas'
            ]);
        }
    }

    /**
     * Show the form for creating a new sale
     */
    public function create(): Response
    {
        $empresaId = $this->tenantService->getEmpresaId();
        $sucursalId = $this->tenantService->getSucursalId();

        return Inertia::render('Sales/Ventas/Create', [
            'productos' => Producto::forEmpresa($empresaId)
                ->with(['categoria', 'marca', 'unidadMedida'])
                ->activos()
                ->get(),
            'clientes' => Cliente::forEmpresa($empresaId)->activos()->get(['id', 'nombre', 'documento']),
            'metodos_pago' => MetodoPago::forEmpresa($empresaId)->activos()->get(),
            'numero_venta' => Venta::generarNumeroVenta($empresaId, $sucursalId),
        ]);
    }

    /**
     * Store a newly created sale
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'nullable|exists:clientes,id',
            'tipo_comprobante' => 'required|in:' . implode(',', array_keys(Venta::TIPOS_COMPROBANTE)),
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.001',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'pagos' => 'required|array|min:1',
            'pagos.*.metodo_pago_id' => 'required|exists:metodos_pago,id',
            'pagos.*.monto' => 'required|numeric|min:0.01',
            'pagos.*.referencia' => 'nullable|string|max:100',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $empresaId = $this->tenantService->getEmpresaId();
            $sucursalId = $this->tenantService->getSucursalId();

            // Crear la venta
            $venta = Venta::create([
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalId,
                'usuario_id' => $user->id,
                'cliente_id' => $request->cliente_id,
                'numero_venta' => Venta::generarNumeroVenta($empresaId, $sucursalId),
                'tipo_comprobante' => $request->tipo_comprobante,
                'estado' => Venta::ESTADO_PENDIENTE,
                'fecha_venta' => now(),
                'descuento_porcentaje' => $request->descuento_porcentaje ?? 0,
                'impuesto_porcentaje' => 18, // IGV por defecto
                'observaciones' => $request->observaciones,
            ]);

            // Agregar detalles
            foreach ($request->detalles as $detalle) {
                $venta->agregarDetalle(
                    $detalle['producto_id'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario'],
                    $detalle['descuento_porcentaje'] ?? 0,
                    $detalle['observaciones'] ?? null
                );
            }

            // Agregar pagos
            foreach ($request->pagos as $pago) {
                $venta->agregarPago(
                    $pago['metodo_pago_id'],
                    $pago['monto'],
                    $pago['referencia'] ?? null,
                    $pago['observaciones'] ?? null
                );
            }

            // Calcular totales y procesar
            $venta->calcularTotales();
            $venta->actualizarTotalPagado();

            // Procesar la venta (inventario, etc.)
            $venta->procesar();

            DB::commit();

            return response()->json([
                'message' => 'Venta registrada exitosamente',
                'venta' => $venta->load(['detalles.producto', 'pagos.metodoPago'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear venta: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified sale
     */
    public function show(Venta $venta): Response|JsonResponse
    {
        try {
            $this->authorize('view', $venta);

            $venta->load([
                'empresa',
                'sucursal', 
                'usuario',
                'cliente',
                'detalles.producto.categoria',
                'detalles.producto.marca',
                'detalles.producto.unidadMedida',
                'pagos.metodoPago'
            ]);

            if (request()->expectsJson()) {
                return response()->json(['venta' => $venta]);
            }

            return Inertia::render('Sales/Ventas/Show', [
                'venta' => $venta
            ]);

        } catch (\Exception $e) {
            Log::error('Error en VentaController@show: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }
            
            return Inertia::render('Sales/Show', [
                'error' => 'Venta no encontrada'
            ]);
        }
    }

    /**
     * Show the form for editing the specified sale
     */
    public function edit(Venta $venta): Response
    {
        $this->authorize('update', $venta);

        if ($venta->estado === Venta::ESTADO_PAGADA) {
            return Inertia::render('Sales/Edit', [
                'error' => 'No se puede editar una venta pagada'
            ]);
        }

        $empresaId = $this->tenantService->getEmpresaId();

        $venta->load([
            'detalles.producto.categoria',
            'detalles.producto.marca', 
            'detalles.producto.unidadMedida',
            'pagos.metodoPago'
        ]);

        return Inertia::render('Sales/Ventas/Edit', [
            'venta' => $venta,
            'productos' => Producto::forEmpresa($empresaId)
                ->with(['categoria', 'marca', 'unidadMedida'])
                ->activos()
                ->get(),
            'clientes' => Cliente::forEmpresa($empresaId)->activos()->get(['id', 'nombre', 'documento']),
            'metodos_pago' => MetodoPago::forEmpresa($empresaId)->activos()->get(),
        ]);
    }

    /**
     * Update the specified sale
     */
    public function update(Request $request, Venta $venta): JsonResponse
    {
        $this->authorize('update', $venta);

        if ($venta->estado === Venta::ESTADO_PAGADA) {
            return response()->json([
                'error' => 'No se puede modificar una venta pagada'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'cliente_id' => 'nullable|exists:clientes,id',
            'tipo_comprobante' => 'required|in:' . implode(',', array_keys(Venta::TIPOS_COMPROBANTE)),
            'observaciones' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $venta->update($request->only([
                'cliente_id',
                'tipo_comprobante', 
                'observaciones'
            ]));

            return response()->json([
                'message' => 'Venta actualizada exitosamente',
                'venta' => $venta->load(['detalles.producto', 'pagos.metodoPago'])
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar venta: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al actualizar la venta'
            ], 500);
        }
    }

    /**
     * Remove the specified sale (anular)
     */
    public function destroy(Venta $venta): JsonResponse
    {
        $this->authorize('delete', $venta);

        if ($venta->estado === Venta::ESTADO_ANULADA) {
            return response()->json([
                'error' => 'La venta ya está anulada'
            ], 422);
        }

        try {
            $motivo = request('motivo', 'Anulación desde sistema');
            $venta->anular($motivo);

            return response()->json([
                'message' => 'Venta anulada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al anular venta: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al anular la venta'
            ], 500);
        }
    }

    /**
     * Mark sale as paid
     */
    public function marcarPagada(Venta $venta): JsonResponse
    {
        $this->authorize('update', $venta);

        try {
            if ($venta->esta_pagada) {
                return response()->json([
                    'message' => 'La venta ya está pagada'
                ]);
            }

            $venta->marcarComoPagada();

            return response()->json([
                'message' => 'Venta marcada como pagada',
                'venta' => $venta->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al marcar venta como pagada: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al marcar la venta como pagada'
            ], 500);
        }
    }

    /**
     * Duplicate sale
     */
    public function duplicar(Venta $venta): JsonResponse
    {
        $this->authorize('create', Venta::class);

        try {
            $nuevaVenta = $venta->duplicar();

            return response()->json([
                'message' => 'Venta duplicada exitosamente',
                'venta' => $nuevaVenta->load(['detalles.producto'])
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al duplicar venta: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al duplicar la venta'
            ], 500);
        }
    }

    /**
     * Get sales statistics
     */
    public function estadisticas(Request $request): JsonResponse
    {
        try {
            $empresaId = $this->tenantService->getEmpresaId();
            $sucursalId = $this->tenantService->getSucursalId();
            $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
            $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

            $ventas = Venta::forEmpresa($empresaId)
                ->when($sucursalId, fn($q) => $q->forSucursal($sucursalId))
                ->byFecha($fechaInicio, $fechaFin)
                ->activas();

            $estadisticas = [
                'resumen' => [
                    'total_ventas' => $ventas->count(),
                    'total_ingresos' => $ventas->sum('total'),
                    'ticket_promedio' => $ventas->avg('total') ?? 0,
                    'margen_total' => $ventas->sum('margen_total'),
                ],
                'por_estado' => [
                    'pendientes' => $ventas->clone()->pendientes()->count(),
                    'pagadas' => $ventas->clone()->pagadas()->count(),
                ],
                'por_comprobante' => Venta::forEmpresa($empresaId)
                    ->byFecha($fechaInicio, $fechaFin)
                    ->activas()
                    ->selectRaw('tipo_comprobante, COUNT(*) as cantidad, SUM(total) as total')
                    ->groupBy('tipo_comprobante')
                    ->get(),
                'top_productos' => Venta::topProductos($empresaId, 10, $sucursalId),
            ];

            return response()->json($estadisticas);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al obtener estadísticas'
            ], 500);
        }
    }
}
