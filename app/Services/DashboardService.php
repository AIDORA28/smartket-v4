<?php

namespace App\Services;

use App\Models\Sales\Venta;
use App\Models\Inventory\Producto;
use App\Models\Cliente;
use App\Models\CajaSesion;
use App\Models\Compra;
use App\Models\Lote;
use App\Models\Inventory\ProductoStock;
use App\Models\Inventory\InventarioMovimiento;
use App\Models\AnalyticsEvento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardService
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Obtener datos completos del dashboard
     */
    public function obtenerDatosDashboard(int $empresaId, array $filtros = []): array
    {
        $cacheKey = "dashboard_data_{$empresaId}_" . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 300, function () use ($empresaId, $filtros) {
            return [
                'resumen_general' => $this->obtenerResumenGeneral($empresaId, $filtros),
                'widgets' => $this->obtenerWidgets($empresaId, $filtros),
                'graficos' => $this->obtenerDatosGraficos($empresaId, $filtros),
                'alertas' => $this->obtenerAlertas($empresaId, $filtros),
                'actividad_reciente' => $this->obtenerActividadReciente($empresaId, $filtros),
                'kpis' => $this->calcularKPIs($empresaId, $filtros)
            ];
        });
    }

    /**
     * Resumen general de métricas principales
     */
    private function obtenerResumenGeneral(int $empresaId, array $filtros): array
    {
        $fechaInicio = $filtros['fecha_inicio'] ?? Carbon::now()->startOfMonth();
        $fechaFin = $filtros['fecha_fin'] ?? Carbon::now()->endOfMonth();
        $sucursalId = $filtros['sucursal_id'] ?? null;

        // Ventas del período
        $ventasQuery = Venta::where('empresa_id', $empresaId)
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        
        if ($sucursalId) {
            $ventasQuery->where('sucursal_id', $sucursalId);
        }

        $ventas = $ventasQuery->get();
        $totalVentas = $ventas->sum('total');
        $cantidadVentas = $ventas->count();

        // Comparación con período anterior
        $diasPeriodo = Carbon::parse($fechaInicio)->diffInDays($fechaFin) + 1;
        $fechaInicioAnterior = Carbon::parse($fechaInicio)->subDays($diasPeriodo);
        $fechaFinAnterior = Carbon::parse($fechaInicio)->subDay();

        $ventasAnteriores = Venta::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha_venta', [$fechaInicioAnterior, $fechaFinAnterior])
            ->sum('total');

        $crecimientoVentas = $ventasAnteriores > 0 
            ? (($totalVentas - $ventasAnteriores) / $ventasAnteriores) * 100 
            : 0;

        // Productos vendidos
        $productosVendidos = DB::table('venta_detalles as vd')
            ->join('ventas as v', 'vd.venta_id', '=', 'v.id')
            ->where('v.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('v.sucursal_id', $sucursalId))
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->sum('vd.cantidad');

        // Nuevos clientes
        $nuevosClientes = Cliente::where('empresa_id', $empresaId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();

        return [
            'total_ventas' => $totalVentas,
            'cantidad_ventas' => $cantidadVentas,
            'crecimiento_ventas' => round($crecimientoVentas, 2),
            'productos_vendidos' => $productosVendidos,
            'nuevos_clientes' => $nuevosClientes,
            'ticket_promedio' => $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0
        ];
    }

    /**
     * Widgets para el dashboard
     */
    private function obtenerWidgets(int $empresaId, array $filtros): array
    {
        $sucursalId = $filtros['sucursal_id'] ?? null;

        return [
            'inventario' => [
                'valor_total' => $this->calcularValorInventario($empresaId, $sucursalId),
                'productos_stock_bajo' => $this->contarProductosStockBajo($empresaId, $sucursalId),
                'productos_sin_stock' => $this->contarProductosSinStock($empresaId, $sucursalId)
            ],
            'caja' => [
                'sesiones_abiertas' => $this->contarSesionesAbiertas($empresaId, $sucursalId),
                'efectivo_disponible' => $this->calcularEfectivoDisponible($empresaId, $sucursalId)
            ],
            'lotes' => [
                'proximos_vencer' => $this->contarLotesProximosVencer($empresaId, $sucursalId),
                'vencidos' => $this->contarLotesVencidos($empresaId, $sucursalId)
            ],
            'compras' => [
                'pendientes' => $this->contarComprasPendientes($empresaId, $sucursalId)
            ]
        ];
    }

    /**
     * Datos para gráficos
     */
    private function obtenerDatosGraficos(int $empresaId, array $filtros): array
    {
        $fechaInicio = $filtros['fecha_inicio'] ?? Carbon::now()->subDays(30);
        $fechaFin = $filtros['fecha_fin'] ?? Carbon::now();
        $sucursalId = $filtros['sucursal_id'] ?? null;

        return [
            'ventas_diarias' => $this->obtenerVentasDiarias($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'productos_mas_vendidos' => $this->obtenerProductosMasVendidos($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'ventas_por_categoria' => $this->obtenerVentasPorCategoria($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'movimientos_inventario' => $this->obtenerMovimientosInventario($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'clientes_frecuentes' => $this->obtenerClientesFrecuentes($empresaId, $fechaInicio, $fechaFin, $sucursalId)
        ];
    }

    /**
     * Alertas del sistema
     */
    private function obtenerAlertas(int $empresaId, array $filtros): array
    {
        $sucursalId = $filtros['sucursal_id'] ?? null;
        $alertas = [];

        // Stock bajo
        $stockBajo = ProductoStock::join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
            ->where('productos.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('producto_stocks.sucursal_id', $sucursalId))
            ->whereRaw('producto_stocks.cantidad_actual <= productos.stock_minimo')
            ->with('producto')
            ->get();

        if ($stockBajo->count() > 0) {
            $alertas[] = [
                'tipo' => 'stock_bajo',
                'nivel' => 'warning',
                'mensaje' => "Hay {$stockBajo->count()} productos con stock bajo",
                'datos' => $stockBajo->take(5)
            ];
        }

        // Productos vencidos
        $lotesVencidos = Lote::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('fecha_vencimiento', '<', Carbon::now())
            ->with('producto')
            ->get();

        if ($lotesVencidos->count() > 0) {
            $alertas[] = [
                'tipo' => 'productos_vencidos',
                'nivel' => 'danger',
                'mensaje' => "Hay {$lotesVencidos->count()} lotes vencidos",
                'datos' => $lotesVencidos->take(5)
            ];
        }

        // Próximos a vencer (7 días)
        $proximosVencer = Lote::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha_vencimiento', [Carbon::now(), Carbon::now()->addDays(7)])
            ->with('producto')
            ->get();

        if ($proximosVencer->count() > 0) {
            $alertas[] = [
                'tipo' => 'proximos_vencer',
                'nivel' => 'warning',
                'mensaje' => "Hay {$proximosVencer->count()} lotes que vencen en los próximos 7 días",
                'datos' => $proximosVencer->take(5)
            ];
        }

        // Sesiones de caja abiertas por mucho tiempo
        $sesionesLargas = CajaSesion::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereNull('cierre_at')
            ->where('apertura_at', '<', Carbon::now()->subHours(12))
            ->with(['sucursal'])
            ->get();

        if ($sesionesLargas->count() > 0) {
            $alertas[] = [
                'tipo' => 'sesiones_largas',
                'nivel' => 'info',
                'mensaje' => "Hay {$sesionesLargas->count()} sesiones de caja abiertas por más de 12 horas",
                'datos' => $sesionesLargas
            ];
        }

        return $alertas;
    }

    /**
     * Actividad reciente del sistema
     */
    private function obtenerActividadReciente(int $empresaId, array $filtros): array
    {
        $limite = $filtros['limite_actividad'] ?? 20;

        return [
            'ventas_recientes' => Venta::where('empresa_id', $empresaId)
                ->with(['cliente', 'sucursal'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            
            'movimientos_inventario' => InventarioMovimiento::where('empresa_id', $empresaId)
                ->with(['producto'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),

            'eventos_analytics' => AnalyticsEvento::where('empresa_id', $empresaId)
                ->orderBy('created_at', 'desc')
                ->limit($limite)
                ->get()
        ];
    }

    /**
     * Calcular KPIs principales
     */
    private function calcularKPIs(int $empresaId, array $filtros): array
    {
        $fechaInicio = $filtros['fecha_inicio'] ?? Carbon::now()->startOfMonth();
        $fechaFin = $filtros['fecha_fin'] ?? Carbon::now()->endOfMonth();
        $sucursalId = $filtros['sucursal_id'] ?? null;

        // Obtener métricas completas
        $metricas = $this->analyticsService->obtenerMetricasDashboard($empresaId, $filtros);

        // Calcular KPIs específicos
        $totalProductos = Producto::where('empresa_id', $empresaId)->count();
        $totalClientes = Cliente::where('empresa_id', $empresaId)->count();

        return [
            'rotacion_inventario' => $metricas['productos']['rotacion_inventario'] ?? 0,
            'margen_promedio' => $this->calcularMargenPromedio($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'satisfaccion_clientes' => $this->calcularSatisfaccionClientes($empresaId, $sucursalId),
            'eficiencia_inventario' => $this->calcularEficienciaInventario($empresaId, $sucursalId),
            'crecimiento_cliente' => $this->calcularCrecimientoClientes($empresaId, $fechaInicio, $fechaFin),
            'productividad_ventas' => $this->calcularProductividadVentas($empresaId, $fechaInicio, $fechaFin, $sucursalId)
        ];
    }

    // Métodos auxiliares privados

    private function calcularValorInventario(int $empresaId, ?int $sucursalId): float
    {
        return DB::table('producto_stocks as ps')
            ->join('productos as p', 'ps.producto_id', '=', 'p.id')
            ->where('p.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('ps.sucursal_id', $sucursalId))
            ->sum(DB::raw('ps.cantidad_actual * p.precio_costo'));
    }

    private function contarProductosStockBajo(int $empresaId, ?int $sucursalId): int
    {
        return ProductoStock::join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
            ->where('productos.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('producto_stocks.sucursal_id', $sucursalId))
            ->whereRaw('producto_stocks.cantidad_actual <= productos.stock_minimo')
            ->count();
    }

    private function contarProductosSinStock(int $empresaId, ?int $sucursalId): int
    {
        return ProductoStock::join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
            ->where('productos.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('producto_stocks.sucursal_id', $sucursalId))
            ->where('producto_stocks.cantidad_actual', 0)
            ->count();
    }

    private function contarSesionesAbiertas(int $empresaId, ?int $sucursalId): int
    {
        return CajaSesion::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereNull('cierre_at')
            ->count();
    }

    private function calcularEfectivoDisponible(int $empresaId, ?int $sucursalId): float
    {
        return CajaSesion::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereNull('cierre_at')
            ->sum('monto_inicial');
    }

    private function contarLotesProximosVencer(int $empresaId, ?int $sucursalId): int
    {
        return Lote::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha_vencimiento', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();
    }

    private function contarLotesVencidos(int $empresaId, ?int $sucursalId): int
    {
        return Lote::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('fecha_vencimiento', '<', Carbon::now())
            ->count();
    }

    private function contarComprasPendientes(int $empresaId, ?int $sucursalId): int
    {
        return Compra::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_destino_id', $sucursalId))
            ->where('estado', 'PENDIENTE')
            ->count();
    }

    private function obtenerVentasDiarias(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        return Venta::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as monto')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    private function obtenerProductosMasVendidos(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        return DB::table('venta_detalles as vd')
            ->join('ventas as v', 'vd.venta_id', '=', 'v.id')
            ->join('productos as p', 'vd.producto_id', '=', 'p.id')
            ->where('v.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('v.sucursal_id', $sucursalId))
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->selectRaw('p.nombre, SUM(vd.cantidad) as cantidad_vendida')
            ->groupBy('p.id', 'p.nombre')
            ->orderByDesc('cantidad_vendida')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function obtenerVentasPorCategoria(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        return DB::table('venta_detalles as vd')
            ->join('ventas as v', 'vd.venta_id', '=', 'v.id')
            ->join('productos as p', 'vd.producto_id', '=', 'p.id')
            ->join('categorias as c', 'p.categoria_id', '=', 'c.id')
            ->where('v.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('v.sucursal_id', $sucursalId))
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->selectRaw('c.nombre as categoria, SUM(vd.subtotal) as total_ventas')
            ->groupBy('c.id', 'c.nombre')
            ->orderByDesc('total_ventas')
            ->get()
            ->toArray();
    }

    private function obtenerMovimientosInventario(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        return InventarioMovimiento::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('tipo_movimiento, COUNT(*) as cantidad')
            ->groupBy('tipo_movimiento')
            ->get()
            ->toArray();
    }

    private function obtenerClientesFrecuentes(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        return Cliente::where('empresa_id', $empresaId)
            ->withCount(['ventas' => function($q) use ($fechaInicio, $fechaFin, $sucursalId) {
                $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
                if ($sucursalId) {
                    $q->where('sucursal_id', $sucursalId);
                }
            }])
            ->orderByDesc('ventas_count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function calcularMargenPromedio(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): float
    {
        $resultado = DB::table('venta_detalles as vd')
            ->join('ventas as v', 'vd.venta_id', '=', 'v.id')
            ->join('productos as p', 'vd.producto_id', '=', 'p.id')
            ->where('v.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('v.sucursal_id', $sucursalId))
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->selectRaw('
                SUM(vd.subtotal) as ingresos_totales,
                SUM(vd.cantidad * p.precio_costo) as costos_totales
            ')
            ->first();

        if ($resultado && $resultado->ingresos_totales > 0) {
            return (($resultado->ingresos_totales - $resultado->costos_totales) / $resultado->ingresos_totales) * 100;
        }

        return 0;
    }

    private function calcularSatisfaccionClientes(int $empresaId, ?int $sucursalId): float
    {
        // Implementar lógica de satisfacción basada en frecuencia de compra, devoluciones, etc.
        // Por ahora retornamos un valor fijo
        return 85.5;
    }

    private function calcularEficienciaInventario(int $empresaId, ?int $sucursalId): float
    {
        $totalProductos = Producto::where('empresa_id', $empresaId)->count();
        $productosSinStock = $this->contarProductosSinStock($empresaId, $sucursalId);

        if ($totalProductos > 0) {
            return (($totalProductos - $productosSinStock) / $totalProductos) * 100;
        }

        return 100;
    }

    private function calcularCrecimientoClientes(int $empresaId, $fechaInicio, $fechaFin): float
    {
        $nuevosClientes = Cliente::where('empresa_id', $empresaId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();

        $totalClientes = Cliente::where('empresa_id', $empresaId)->count();

        if ($totalClientes > 0) {
            return ($nuevosClientes / $totalClientes) * 100;
        }

        return 0;
    }

    private function calcularProductividadVentas(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): float
    {
        $totalVentas = Venta::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->sum('total');

        $diasPeriodo = Carbon::parse($fechaInicio)->diffInDays($fechaFin) + 1;

        return $diasPeriodo > 0 ? $totalVentas / $diasPeriodo : 0;
    }

    /**
     * Limpiar caché del dashboard
     */
    public function limpiarCache(int $empresaId): void
    {
        $pattern = "dashboard_data_{$empresaId}_*";
        Cache::forget($pattern);
    }
}

