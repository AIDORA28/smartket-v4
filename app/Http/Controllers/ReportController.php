<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantService;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index(Request $request): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if (!$empresa) {
            return Inertia::render('Reports', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        // Filtros por defecto
        $filters = [
            'periodo' => $request->get('periodo', 'last_30_days'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'tipo_reporte' => $request->get('tipo_reporte', 'ventas'),
        ];

        // Obtener fechas según el período
        $fechas = $this->getFechasPorPeriodo($filters['periodo'], $filters['fecha_inicio'], $filters['fecha_fin']);

        $data = [];

        switch ($filters['tipo_reporte']) {
            case 'ventas':
                $data = $this->getReporteVentas($empresa->id, $fechas);
                break;
            case 'productos':
                $data = $this->getReporteProductos($empresa->id, $fechas);
                break;
            case 'clientes':
                $data = $this->getReporteClientes($empresa->id, $fechas);
                break;
            case 'inventario':
                $data = $this->getReporteInventario($empresa->id);
                break;
            default:
                $data = $this->getReporteVentas($empresa->id, $fechas);
        }

        return Inertia::render('Reports', [
            'filters' => $filters,
            'data' => $data,
            'fechas' => $fechas,
        ]);
    }

    private function getFechasPorPeriodo($periodo, $fechaInicio = null, $fechaFin = null)
    {
        $fechas = [];
        
        switch ($periodo) {
            case 'today':
                $fechas['inicio'] = Carbon::today();
                $fechas['fin'] = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $fechas['inicio'] = Carbon::yesterday();
                $fechas['fin'] = Carbon::yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $fechas['inicio'] = Carbon::now()->subDays(7);
                $fechas['fin'] = Carbon::now();
                break;
            case 'last_30_days':
                $fechas['inicio'] = Carbon::now()->subDays(30);
                $fechas['fin'] = Carbon::now();
                break;
            case 'this_month':
                $fechas['inicio'] = Carbon::now()->startOfMonth();
                $fechas['fin'] = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $fechas['inicio'] = Carbon::now()->subMonth()->startOfMonth();
                $fechas['fin'] = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'custom':
                $fechas['inicio'] = $fechaInicio ? Carbon::parse($fechaInicio) : Carbon::now()->subDays(30);
                $fechas['fin'] = $fechaFin ? Carbon::parse($fechaFin) : Carbon::now();
                break;
            default:
                $fechas['inicio'] = Carbon::now()->subDays(30);
                $fechas['fin'] = Carbon::now();
        }

        return $fechas;
    }

    private function getReporteVentas($empresaId, $fechas)
    {
        $cacheKey = "reporte_ventas_{$empresaId}_" . $fechas['inicio']->format('Y-m-d') . "_" . $fechas['fin']->format('Y-m-d');
        
        return Cache::remember($cacheKey, 300, function () use ($empresaId, $fechas) {
            // Resumen general
            $resumen = Venta::where('empresa_id', $empresaId)
                ->where('estado', 'completed')
                ->whereBetween('fecha_venta', [$fechas['inicio'], $fechas['fin']])
                ->selectRaw('
                    COUNT(*) as total_ventas,
                    SUM(total) as total_ingresos,
                    AVG(total) as ticket_promedio,
                    SUM(CASE WHEN metodo_pago = "cash" THEN total ELSE 0 END) as total_efectivo,
                    SUM(CASE WHEN metodo_pago = "card" THEN total ELSE 0 END) as total_tarjeta
                ')
                ->first();

            // Ventas por día
            $ventasPorDia = Venta::where('empresa_id', $empresaId)
                ->where('estado', 'completed')
                ->whereBetween('fecha_venta', [$fechas['inicio'], $fechas['fin']])
                ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            // Productos más vendidos
            $productosMasVendidos = VentaDetalle::join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
                ->where('ventas.empresa_id', $empresaId)
                ->where('ventas.estado', 'completed')
                ->whereBetween('ventas.fecha_venta', [$fechas['inicio'], $fechas['fin']])
                ->selectRaw('
                    productos.nombre,
                    SUM(venta_detalles.cantidad) as cantidad_vendida,
                    SUM(venta_detalles.total) as total_vendido
                ')
                ->groupBy('productos.id', 'productos.nombre')
                ->orderBy('cantidad_vendida', 'desc')
                ->limit(10)
                ->get();

            // Ventas por método de pago
            $ventasPorMetodo = Venta::where('empresa_id', $empresaId)
                ->where('estado', 'completed')
                ->whereBetween('fecha_venta', [$fechas['inicio'], $fechas['fin']])
                ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(total) as total')
                ->groupBy('metodo_pago')
                ->get();

            return [
                'resumen' => $resumen,
                'ventas_por_dia' => $ventasPorDia,
                'productos_mas_vendidos' => $productosMasVendidos,
                'ventas_por_metodo' => $ventasPorMetodo,
            ];
        });
    }

    private function getReporteProductos($empresaId, $fechas)
    {
        $cacheKey = "reporte_productos_{$empresaId}_" . $fechas['inicio']->format('Y-m-d') . "_" . $fechas['fin']->format('Y-m-d');
        
        return Cache::remember($cacheKey, 300, function () use ($empresaId, $fechas) {
            // Productos con ventas
            $productosConVentas = VentaDetalle::join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
                ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->where('ventas.empresa_id', $empresaId)
                ->where('ventas.estado', 'completed')
                ->whereBetween('ventas.fecha_venta', [$fechas['inicio'], $fechas['fin']])
                ->selectRaw('
                    productos.id,
                    productos.nombre,
                    productos.precio_venta,
                    categorias.nombre as categoria,
                    SUM(venta_detalles.cantidad) as cantidad_vendida,
                    SUM(venta_detalles.total) as total_vendido,
                    AVG(venta_detalles.precio) as precio_promedio
                ')
                ->groupBy('productos.id', 'productos.nombre', 'productos.precio_venta', 'categorias.nombre')
                ->orderBy('cantidad_vendida', 'desc')
                ->get();

            // Productos sin ventas
            $productosSinVentas = Producto::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->whereNotIn('id', function($query) use ($fechas) {
                    $query->select('producto_id')
                        ->from('venta_detalles')
                        ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                        ->where('ventas.estado', 'completed')
                        ->whereBetween('ventas.fecha_venta', [$fechas['inicio'], $fechas['fin']]);
                })
                ->with(['categoria', 'stocks'])
                ->get()
                ->map(function($producto) {
                    return [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'precio_venta' => $producto->precio_venta,
                        'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                        'stock_actual' => $producto->stocks->sum('cantidad_actual') ?? 0,
                    ];
                });

            // Ventas por categoría
            $ventasPorCategoria = VentaDetalle::join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
                ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->where('ventas.empresa_id', $empresaId)
                ->where('ventas.estado', 'completed')
                ->whereBetween('ventas.fecha_venta', [$fechas['inicio'], $fechas['fin']])
                ->selectRaw('
                    COALESCE(categorias.nombre, "Sin categoría") as categoria,
                    SUM(venta_detalles.cantidad) as cantidad_vendida,
                    SUM(venta_detalles.total) as total_vendido
                ')
                ->groupBy('categorias.nombre')
                ->orderBy('total_vendido', 'desc')
                ->get();

            return [
                'productos_con_ventas' => $productosConVentas,
                'productos_sin_ventas' => $productosSinVentas,
                'ventas_por_categoria' => $ventasPorCategoria,
            ];
        });
    }

    private function getReporteClientes($empresaId, $fechas)
    {
        $cacheKey = "reporte_clientes_{$empresaId}_" . $fechas['inicio']->format('Y-m-d') . "_" . $fechas['fin']->format('Y-m-d');
        
        return Cache::remember($cacheKey, 300, function () use ($empresaId, $fechas) {
            // Clientes con ventas
            $clientesConVentas = Venta::where('empresa_id', $empresaId)
                ->where('estado', 'completed')
                ->whereBetween('fecha_venta', [$fechas['inicio'], $fechas['fin']])
                ->whereNotNull('cliente_id')
                ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                ->selectRaw('
                    clientes.id,
                    clientes.nombre,
                    clientes.email,
                    clientes.telefono,
                    COUNT(ventas.id) as total_compras,
                    SUM(ventas.total) as total_gastado,
                    AVG(ventas.total) as ticket_promedio,
                    MAX(ventas.fecha_venta) as ultima_compra
                ')
                ->groupBy('clientes.id', 'clientes.nombre', 'clientes.email', 'clientes.telefono')
                ->orderBy('total_gastado', 'desc')
                ->get();

            // Ventas sin cliente asignado
            $ventasSinCliente = Venta::where('empresa_id', $empresaId)
                ->where('estado', 'completed')
                ->whereBetween('fecha_venta', [$fechas['inicio'], $fechas['fin']])
                ->whereNull('cliente_id')
                ->selectRaw('COUNT(*) as cantidad, SUM(total) as total')
                ->first();

            // Clientes nuevos en el período
            $clientesNuevos = Cliente::where('empresa_id', $empresaId)
                ->whereBetween('created_at', [$fechas['inicio'], $fechas['fin']])
                ->count();

            return [
                'clientes_con_ventas' => $clientesConVentas,
                'ventas_sin_cliente' => $ventasSinCliente,
                'clientes_nuevos' => $clientesNuevos,
            ];
        });
    }

    private function getReporteInventario($empresaId)
    {
        $cacheKey = "reporte_inventario_{$empresaId}";
        
        return Cache::remember($cacheKey, 300, function () use ($empresaId) {
            // Productos con stock
            $productosConStock = Producto::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->with(['categoria', 'stocks.lote'])
                ->get()
                ->map(function($producto) {
                    $stockTotal = $producto->stocks->sum('cantidad_actual') ?? 0;
                    $stockMinimo = $producto->stock_minimo ?? 0;
                    
                    // Calcular valor del inventario
                    $valorInventario = $producto->stocks->sum(function($stock) use ($producto) {
                        return $stock->cantidad_actual * $producto->costo_compra;
                    });

                    // Productos próximos a vencer (30 días)
                    $proximosVencer = $producto->stocks->filter(function($stock) {
                        return $stock->lote && 
                               Carbon::parse($stock->lote->fecha_vencimiento)->lte(Carbon::now()->addDays(30)) &&
                               $stock->cantidad_actual > 0;
                    })->sum('cantidad_actual');

                    return [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                        'stock_actual' => $stockTotal,
                        'stock_minimo' => $stockMinimo,
                        'estado_stock' => $stockTotal <= $stockMinimo ? 'bajo' : 'normal',
                        'costo_compra' => $producto->costo_compra,
                        'precio_venta' => $producto->precio_venta,
                        'valor_inventario' => $valorInventario,
                        'proximos_vencer' => $proximosVencer,
                    ];
                });

            // Resumen del inventario
            $resumenInventario = [
                'total_productos' => $productosConStock->count(),
                'productos_con_stock' => $productosConStock->where('stock_actual', '>', 0)->count(),
                'productos_sin_stock' => $productosConStock->where('stock_actual', '<=', 0)->count(),
                'productos_stock_bajo' => $productosConStock->where('estado_stock', 'bajo')->count(),
                'valor_total_inventario' => $productosConStock->sum('valor_inventario'),
                'productos_proximos_vencer' => $productosConStock->where('proximos_vencer', '>', 0)->count(),
            ];

            return [
                'productos' => $productosConStock,
                'resumen' => $resumenInventario,
            ];
        });
    }

    public function export(Request $request)
    {
        // Esta función se puede implementar para exportar reportes en Excel/PDF
        // Por ahora retornamos un JSON con los datos
        
        $empresa = $this->tenantService->getEmpresa();
        $filters = [
            'periodo' => $request->get('periodo', 'last_30_days'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'tipo_reporte' => $request->get('tipo_reporte', 'ventas'),
        ];

        $fechas = $this->getFechasPorPeriodo($filters['periodo'], $filters['fecha_inicio'], $filters['fecha_fin']);
        
        $data = [];
        switch ($filters['tipo_reporte']) {
            case 'ventas':
                $data = $this->getReporteVentas($empresa->id, $fechas);
                break;
            case 'productos':
                $data = $this->getReporteProductos($empresa->id, $fechas);
                break;
            case 'clientes':
                $data = $this->getReporteClientes($empresa->id, $fechas);
                break;
            case 'inventario':
                $data = $this->getReporteInventario($empresa->id);
                break;
        }

        return response()->json([
            'data' => $data,
            'filters' => $filters,
            'fechas' => $fechas,
        ]);
    }
}
