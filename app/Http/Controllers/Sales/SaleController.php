<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Venta;
use App\Models\Sales\VentaDetalle;
use App\Models\Cliente;
use App\Models\Inventory\Producto;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class SaleController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display sales dashboard
     */
    public function index(Request $request): Response
    {
        try {
            $empresaId = $this->tenantService->getEmpresaId();
            $sucursalId = $this->tenantService->getSucursalId();
            
            if (!$empresaId) {
                return Inertia::render('Sales/Index', [
                    'error' => 'No hay empresa configurada'
                ]);
            }

            // Estadísticas principales
            $estadisticas = $this->obtenerEstadisticas($empresaId, $sucursalId);
            
            // Ventas recientes
            $ventasRecientes = Venta::with(['cliente', 'usuario'])
                ->forEmpresa($empresaId)
                ->when($sucursalId, fn($q) => $q->forSucursal($sucursalId))
                ->orderByDesc('fecha_venta')
                ->limit(10)
                ->get();

            // Top productos
            $topProductos = Venta::topProductos($empresaId, 5, $sucursalId);

            return Inertia::render('Sales/Index', [
                'estadisticas' => $estadisticas,
                'ventas_recientes' => $ventasRecientes,
                'top_productos' => $topProductos,
                'empresa' => $this->tenantService->getEmpresa(),
                'sucursal' => $this->tenantService->getSucursal(),
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Sales/Index', [
                'error' => 'Error al cargar datos de ventas'
            ]);
        }
    }

    /**
     * Show specific sale
     */
    public function show($id): Response|JsonResponse
    {
        try {
            $venta = Venta::with([
                'empresa',
                'sucursal',
                'usuario',
                'cliente',
                'detalles.producto.categoria',
                'detalles.producto.marca',
                'detalles.producto.unidadMedida',
                'pagos.metodoPago'
            ])->findOrFail($id);

            if (request()->expectsJson()) {
                return response()->json(['venta' => $venta]);
            }

            return Inertia::render('Sales/Show', [
                'venta' => $venta
            ]);

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }

            return Inertia::render('Sales/Show', [
                'error' => 'Venta no encontrada'
            ]);
        }
    }

    /**
     * Show edit form
     */
    public function edit($id): Response
    {
        try {
            $venta = Venta::with([
                'detalles.producto',
                'pagos.metodoPago'
            ])->findOrFail($id);

            if ($venta->estado === Venta::ESTADO_PAGADA) {
                return Inertia::render('Sales/Edit', [
                    'error' => 'No se puede editar una venta pagada'
                ]);
            }

            $empresaId = $this->tenantService->getEmpresaId();

            return Inertia::render('Sales/Edit', [
                'venta' => $venta,
                'productos' => Producto::forEmpresa($empresaId)->activos()->get(),
                'clientes' => Cliente::forEmpresa($empresaId)->activos()->get(),
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Sales/Edit', [
                'error' => 'Venta no encontrada'
            ]);
        }
    }

    /**
     * Update sale
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $venta = Venta::findOrFail($id);

            if ($venta->estado === Venta::ESTADO_PAGADA) {
                return response()->json([
                    'error' => 'No se puede modificar una venta pagada'
                ], 422);
            }

            $request->validate([
                'cliente_id' => 'nullable|exists:clientes,id',
                'observaciones' => 'nullable|string|max:500',
            ]);

            $venta->update($request->only([
                'cliente_id',
                'observaciones'
            ]));

            return response()->json([
                'message' => 'Venta actualizada exitosamente',
                'venta' => $venta->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar la venta'
            ], 500);
        }
    }

    /**
     * Cancel/Delete sale
     */
    public function destroy($id): JsonResponse
    {
        try {
            $venta = Venta::findOrFail($id);

            if ($venta->estado === Venta::ESTADO_ANULADA) {
                return response()->json([
                    'error' => 'La venta ya está anulada'
                ], 422);
            }

            $motivo = request('motivo', 'Anulación desde sistema');
            $venta->anular($motivo);

            return response()->json([
                'message' => 'Venta anulada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al anular la venta'
            ], 500);
        }
    }

    /**
     * Cancel sale
     */
    public function cancel($id): JsonResponse
    {
        return $this->destroy($id);
    }

    /**
     * Complete sale (mark as paid)
     */
    public function complete($id): JsonResponse
    {
        try {
            $venta = Venta::findOrFail($id);

            if ($venta->esta_pagada) {
                return response()->json([
                    'message' => 'La venta ya está pagada'
                ]);
            }

            $venta->marcarComoPagada();

            return response()->json([
                'message' => 'Venta completada exitosamente',
                'venta' => $venta->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al completar la venta'
            ], 500);
        }
    }

    /**
     * Get sales statistics
     */
    private function obtenerEstadisticas(int $empresaId, ?int $sucursalId = null): array
    {
        $base = Venta::forEmpresa($empresaId)
            ->when($sucursalId, fn($q) => $q->forSucursal($sucursalId));

        return [
            'hoy' => [
                'total_ventas' => $base->clone()->hoy()->count(),
                'total_ingresos' => $base->clone()->hoy()->sum('total'),
                'ticket_promedio' => $base->clone()->hoy()->avg('total') ?? 0,
                'ventas_pendientes' => $base->clone()->hoy()->pendientes()->count(),
            ],
            'mes' => [
                'total_ventas' => $base->clone()->whereMonth('fecha_venta', now()->month)->count(),
                'total_ingresos' => $base->clone()->whereMonth('fecha_venta', now()->month)->sum('total'),
                'ticket_promedio' => $base->clone()->whereMonth('fecha_venta', now()->month)->avg('total') ?? 0,
            ],
            'comparacion' => [
                'ventas_ayer' => $base->clone()->whereDate('fecha_venta', now()->subDay())->count(),
                'ingresos_ayer' => $base->clone()->whereDate('fecha_venta', now()->subDay())->sum('total'),
                'ventas_mes_anterior' => $base->clone()->whereMonth('fecha_venta', now()->subMonth()->month)->count(),
                'ingresos_mes_anterior' => $base->clone()->whereMonth('fecha_venta', now()->subMonth()->month)->sum('total'),
            ]
        ];
    }

    /**
     * Get sales for API
     */
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $empresaId = $this->tenantService->getEmpresaId();
            $sucursalId = $this->tenantService->getSucursalId();

            $query = Venta::with(['cliente', 'usuario'])
                ->forEmpresa($empresaId)
                ->when($sucursalId, fn($q) => $q->forSucursal($sucursalId));

            // Filtros
            if ($request->has('estado')) {
                $query->byEstado($request->estado);
            }

            if ($request->has('fecha_inicio')) {
                $fechaFin = $request->get('fecha_fin', $request->fecha_inicio);
                $query->byFecha($request->fecha_inicio, $fechaFin);
            }

            // Paginación
            $perPage = $request->get('per_page', 15);
            $ventas = $query->orderByDesc('fecha_venta')->paginate($perPage);

            return response()->json([
                'ventas' => $ventas,
                'estadisticas' => $this->obtenerEstadisticas($empresaId, $sucursalId)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar ventas'
            ], 500);
        }
    }
}
