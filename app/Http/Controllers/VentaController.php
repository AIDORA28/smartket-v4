<?php

namespace App\Http\Controllers;

use App\Models\Sales\Venta;
use App\Services\VentaService;
use App\Services\TenantService;
use App\Services\ReporteVentasService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    protected VentaService $ventaService;
    protected TenantService $tenantService;
    protected ReporteVentasService $reporteService;

    public function __construct(
        VentaService $ventaService,
        TenantService $tenantService,
        ReporteVentasService $reporteService
    ) {
        $this->ventaService = $ventaService;
        $this->tenantService = $tenantService;
        $this->reporteService = $reporteService;
    }

    /**
     * Obtener lista de ventas
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $sucursal = $this->tenantService->getSucursal();

            $query = Venta::forEmpresa($empresa->id);

            // Filtrar por sucursal si está especificada
            if ($request->filled('sucursal_id')) {
                $query->forSucursal($request->sucursal_id);
            } else {
                $query->forSucursal($sucursal->id);
            }

            // Filtros de fecha
            if ($request->filled('fecha_inicio')) {
                $fechaFin = $request->get('fecha_fin', $request->fecha_inicio);
                $query->byFecha($request->fecha_inicio, $fechaFin);
            } else {
                // Por defecto, ventas del día actual
                $query->hoy();
            }

            // Filtros adicionales
            if ($request->filled('estado')) {
                $query->byEstado($request->estado);
            }

            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }

            if ($request->filled('usuario_id')) {
                $query->where('usuario_id', $request->usuario_id);
            }

            // Búsqueda por número de venta
            if ($request->filled('numero_venta')) {
                $query->where('numero_venta', 'like', '%' . $request->numero_venta . '%');
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'fecha_venta');
            $sortDir = $request->get('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);

            // Cargar relaciones
            $query->with(['cliente', 'usuario', 'detalles.producto', 'pagos.metodoPago']);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $ventas = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $ventas,
                'message' => 'Ventas obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener ventas', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener ventas'
            ], 500);
        }
    }

    /**
     * Crear nueva venta
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $sucursal = $this->tenantService->getSucursal();
            $usuario = $request->user();

            $validator = Validator::make($request->all(), [
                'cliente_id' => 'nullable|exists:clientes,id',
                'tipo_comprobante' => 'sometimes|in:boleta,factura,nota_credito,nota_debito,ticket',
                'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
                'impuesto_porcentaje' => 'nullable|numeric|min:0|max:100',
                'observaciones' => 'nullable|string|max:1000',
                'requiere_facturacion' => 'sometimes|boolean',
                'productos' => 'required|array|min:1',
                'productos.*.producto_id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|numeric|min:0.001',
                'productos.*.precio_unitario' => 'nullable|numeric|min:0',
                'productos.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
                'pagos' => 'required|array|min:1',
                'pagos.*.metodo_pago_id' => 'required|exists:metodos_pago,id',
                'pagos.*.monto' => 'required|numeric|min:0.01',
                'pagos.*.referencia' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Preparar datos para la venta
            $datosVenta = [
                'empresa_id' => $empresa->id,
                'sucursal_id' => $sucursal->id,
                'usuario_id' => $usuario->id,
                'cliente_id' => $request->cliente_id,
                'tipo_comprobante' => $request->get('tipo_comprobante', Venta::TIPO_TICKET),
                'descuento_porcentaje' => $request->get('descuento_porcentaje', 0),
                'impuesto_porcentaje' => $request->get('impuesto_porcentaje', 0),
                'observaciones' => $request->observaciones,
                'requiere_facturacion' => $request->get('requiere_facturacion', false),
                'productos' => $request->productos,
                'pagos' => $request->pagos,
            ];

            // Crear venta completa
            $venta = $this->ventaService->completarVenta($datosVenta);

            return response()->json([
                'success' => true,
                'data' => $venta,
                'message' => 'Venta creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear venta', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar venta específica
     */
    public function show(Venta $venta): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que la venta pertenece a la empresa
            if ($venta->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            // Cargar todas las relaciones necesarias
            $venta->load([
                'cliente',
                'usuario',
                'sucursal',
                'detalles.producto.categoria',
                'pagos.metodoPago'
            ]);

            return response()->json([
                'success' => true,
                'data' => $venta,
                'message' => 'Venta obtenida exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener venta', [
                'error' => $e->getMessage(),
                'venta_id' => $venta->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener venta'
            ], 500);
        }
    }

    /**
     * Anular venta
     */
    public function anular(Request $request, Venta $venta): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que la venta pertenece a la empresa
            if ($venta->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'motivo' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motivo de anulación requerido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->ventaService->anularVenta($venta, $request->motivo);

            return response()->json([
                'success' => true,
                'data' => $venta->fresh(),
                'message' => 'Venta anulada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al anular venta', [
                'error' => $e->getMessage(),
                'venta_id' => $venta->id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener ventas del día
     */
    public function ventasDelDia(): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $sucursal = $this->tenantService->getSucursal();

            $ventas = $this->ventaService->ventasDelDia($empresa->id, $sucursal->id);

            return response()->json([
                'success' => true,
                'data' => $ventas,
                'message' => 'Ventas del día obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener ventas del día', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener ventas del día'
            ], 500);
        }
    }

    /**
     * Obtener resumen de ventas
     */
    public function resumen(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $sucursal = $this->tenantService->getSucursal();

            $fechaInicio = $request->get('fecha_inicio', today()->toDateString());
            $fechaFin = $request->get('fecha_fin');
            $sucursalId = $request->get('sucursal_id', $sucursal->id);

            $resumen = $this->ventaService->resumenVentas(
                $empresa->id,
                $fechaInicio,
                $fechaFin,
                $sucursalId
            );

            return response()->json([
                'success' => true,
                'data' => $resumen,
                'message' => 'Resumen obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener resumen de ventas', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen'
            ], 500);
        }
    }

    /**
     * Obtener productos más vendidos
     */
    public function productosMasVendidos(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $limite = $request->get('limite', 10);
            $fechaInicio = $request->get('fecha_inicio', today()->subDays(30)->toDateString());
            $fechaFin = $request->get('fecha_fin', today()->toDateString());

            $productos = $this->ventaService->productosMasVendidos(
                $empresa->id,
                $limite,
                $fechaInicio,
                $fechaFin
            );

            return response()->json([
                'success' => true,
                'data' => $productos,
                'message' => 'Productos más vendidos obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener productos más vendidos', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos'
            ], 500);
        }
    }

    /**
     * Generar reporte de ventas
     */
    public function reporte(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $sucursal = $this->tenantService->getSucursal();

            $validator = Validator::make($request->all(), [
                'tipo' => 'required|in:periodo,productos,vendedores,clientes,metodos_pago,horas',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'sucursal_id' => 'nullable|exists:sucursales,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fechaInicio = $request->fecha_inicio;
            $fechaFin = $request->get('fecha_fin', $fechaInicio);
            $sucursalId = $request->get('sucursal_id', $sucursal->id);

            $reporte = match ($request->tipo) {
                'periodo' => $this->reporteService->ventasPorPeriodo($empresa->id, $fechaInicio, $fechaFin, $sucursalId),
                'productos' => $this->reporteService->productosMasVendidos($empresa->id, $fechaInicio, $fechaFin),
                'vendedores' => $this->reporteService->ventasPorVendedor($empresa->id, $fechaInicio, $fechaFin),
                'clientes' => $this->reporteService->clientesFrecuentes($empresa->id, $fechaInicio, $fechaFin),
                'metodos_pago' => $this->reporteService->metodosPago($empresa->id, $fechaInicio, $fechaFin),
                'horas' => $this->reporteService->ventasPorHoras($empresa->id, $fechaInicio, $sucursalId),
                default => throw new \Exception('Tipo de reporte no válido')
            };

            return response()->json([
                'success' => true,
                'data' => $reporte,
                'message' => 'Reporte generado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al generar reporte', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard ejecutivo
     */
    public function dashboard(): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $sucursal = $this->tenantService->getSucursal();

            $dashboard = $this->reporteService->dashboardEjecutivo($empresa->id, $sucursal->id);

            return response()->json([
                'success' => true,
                'data' => $dashboard,
                'message' => 'Dashboard obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener dashboard', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener dashboard'
            ], 500);
        }
    }
}
