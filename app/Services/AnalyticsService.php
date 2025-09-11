<?php

namespace App\Services;

use App\Models\AnalyticsEvento;
use App\Models\Sales\Venta;
use App\Models\Inventory\Producto;
use App\Models\Inventory\ProductoStock;
use App\Models\Cliente;
use App\Models\CajaSesion;
use App\Models\Inventory\InventarioMovimiento;
use App\Models\Compra;
use App\Models\Lote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Registrar evento de analytics
     */
    public function registrarEvento(
        string $eventoTipo,
        string $objeto,
        int $objetoId,
        array $datos = [],
        ?int $userId = null,
        ?int $sucursalId = null
    ): AnalyticsEvento {
        return AnalyticsEvento::create([
            'evento_tipo' => $eventoTipo,
            'objeto' => $objeto,
            'objeto_id' => $objetoId,
            'datos' => json_encode($datos),
            'user_id' => $userId ?? Auth::id(),
            'sucursal_id' => $sucursalId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'empresa_id' => Auth::user()?->empresa_id
        ]);
    }

    /**
     * Obtener métricas del dashboard
     */
    public function obtenerMetricasDashboard(int $empresaId, array $filtros = []): array
    {
        $fechaInicio = $filtros['fecha_inicio'] ?? Carbon::now()->startOfMonth();
        $fechaFin = $filtros['fecha_fin'] ?? Carbon::now()->endOfMonth();
        $sucursalId = $filtros['sucursal_id'] ?? null;

        return [
            'ventas' => $this->obtenerMetricasVentas($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'productos' => $this->obtenerMetricasProductos($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'clientes' => $this->obtenerMetricasClientes($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'inventario' => $this->obtenerMetricasInventario($empresaId, $sucursalId),
            'caja' => $this->obtenerMetricasCaja($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'compras' => $this->obtenerMetricasCompras($empresaId, $fechaInicio, $fechaFin, $sucursalId),
            'lotes' => $this->obtenerMetricasLotes($empresaId, $sucursalId)
        ];
    }

    /**
     * Métricas de ventas
     */
    private function obtenerMetricasVentas(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        $query = Venta::where('empresa_id', $empresaId)
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);

        if ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }

        $ventas = $query->get();
        $totalVentas = $ventas->count();
        $montoTotal = $ventas->sum('total');
        $promedioVenta = $totalVentas > 0 ? $montoTotal / $totalVentas : 0;

        // Ventas por día (últimos 30 días)
        $ventasPorDia = Venta::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('fecha_venta', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as monto')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Top productos vendidos
        $topProductos = DB::table('venta_detalles as vd')
            ->join('ventas as v', 'vd.venta_id', '=', 'v.id')
            ->join('productos as p', 'vd.producto_id', '=', 'p.id')
            ->where('v.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('v.sucursal_id', $sucursalId))
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->selectRaw('p.nombre, SUM(vd.cantidad) as cantidad_vendida, SUM(vd.subtotal) as ingresos')
            ->groupBy('p.id', 'p.nombre')
            ->orderByDesc('cantidad_vendida')
            ->limit(10)
            ->get();

        return [
            'total_ventas' => $totalVentas,
            'monto_total' => $montoTotal,
            'promedio_venta' => $promedioVenta,
            'ventas_por_dia' => $ventasPorDia,
            'top_productos' => $topProductos,
            'crecimiento' => $this->calcularCrecimientoVentas($empresaId, $fechaInicio, $fechaFin, $sucursalId)
        ];
    }

    /**
     * Métricas de productos
     */
    private function obtenerMetricasProductos(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        $totalProductos = Producto::where('empresa_id', $empresaId)->count();
        
        $stockBajo = ProductoStock::join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
            ->where('productos.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('producto_stocks.sucursal_id', $sucursalId))
            ->whereRaw('producto_stocks.cantidad_actual <= productos.stock_minimo')
            ->count();

        $sinStock = ProductoStock::join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
            ->where('productos.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('producto_stocks.sucursal_id', $sucursalId))
            ->where('producto_stocks.cantidad_actual', 0)
            ->count();

        // Productos más rentables
        $productosRentables = DB::table('venta_detalles as vd')
            ->join('ventas as v', 'vd.venta_id', '=', 'v.id')
            ->join('productos as p', 'vd.producto_id', '=', 'p.id')
            ->where('v.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('v.sucursal_id', $sucursalId))
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->selectRaw('
                p.nombre,
                SUM(vd.cantidad) as cantidad_vendida,
                SUM(vd.subtotal) as ingresos,
                SUM(vd.cantidad * p.precio_costo) as costos,
                SUM(vd.subtotal) - SUM(vd.cantidad * p.precio_costo) as utilidad
            ')
            ->groupBy('p.id', 'p.nombre', 'p.precio_costo')
            ->orderByDesc('utilidad')
            ->limit(10)
            ->get();

        return [
            'total_productos' => $totalProductos,
            'stock_bajo' => $stockBajo,
            'sin_stock' => $sinStock,
            'productos_rentables' => $productosRentables,
            'rotacion_inventario' => $this->calcularRotacionInventario($empresaId, $fechaInicio, $fechaFin, $sucursalId)
        ];
    }

    /**
     * Métricas de clientes
     */
    private function obtenerMetricasClientes(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        $totalClientes = Cliente::where('empresa_id', $empresaId)->count();
        $clientesActivos = Cliente::where('empresa_id', $empresaId)
            ->whereHas('ventas', function($q) use ($fechaInicio, $fechaFin, $sucursalId) {
                $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
                if ($sucursalId) {
                    $q->where('sucursal_id', $sucursalId);
                }
            })
            ->count();

        // Top clientes por compras
        $topClientes = Cliente::where('empresa_id', $empresaId)
            ->withSum(['ventas' => function($q) use ($fechaInicio, $fechaFin, $sucursalId) {
                $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
                if ($sucursalId) {
                    $q->where('sucursal_id', $sucursalId);
                }
            }], 'total')
            ->orderByDesc('ventas_sum_total')
            ->limit(10)
            ->get();

        return [
            'total_clientes' => $totalClientes,
            'clientes_activos' => $clientesActivos,
            'top_clientes' => $topClientes,
            'nuevos_clientes' => Cliente::where('empresa_id', $empresaId)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->count()
        ];
    }

    /**
     * Métricas de inventario
     */
    private function obtenerMetricasInventario(int $empresaId, ?int $sucursalId): array
    {
        $valorInventario = DB::table('producto_stocks as ps')
            ->join('productos as p', 'ps.producto_id', '=', 'p.id')
            ->where('p.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('ps.sucursal_id', $sucursalId))
            ->sum(DB::raw('ps.cantidad_actual * p.precio_costo'));

        $movimientosRecientes = InventarioMovimiento::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        return [
            'valor_inventario' => $valorInventario,
            'movimientos_recientes' => $movimientosRecientes,
            'productos_vencidos' => $this->obtenerProductosVencidos($empresaId, $sucursalId),
            'alertas_stock' => $this->obtenerAlertasStock($empresaId, $sucursalId)
        ];
    }

    /**
     * Métricas de caja
     */
    private function obtenerMetricasCaja(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        $sesiones = CajaSesion::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('apertura_at', [$fechaInicio, $fechaFin])
            ->get();

        $totalIngresos = $sesiones->sum('monto_declarado_cierre');
        $promedioSesion = $sesiones->count() > 0 ? $totalIngresos / $sesiones->count() : 0;

        return [
            'total_sesiones' => $sesiones->count(),
            'total_ingresos' => $totalIngresos,
            'promedio_sesion' => $promedioSesion,
            'sesiones_abiertas' => CajaSesion::where('empresa_id', $empresaId)
                ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
                ->whereNull('cierre_at')
                ->count()
        ];
    }

    /**
     * Métricas de compras
     */
    private function obtenerMetricasCompras(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        $compras = Compra::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_destino_id', $sucursalId))
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get();

        return [
            'total_compras' => $compras->count(),
            'monto_total_compras' => $compras->sum('total'),
            'promedio_compra' => $compras->count() > 0 ? $compras->sum('total') / $compras->count() : 0,
            'compras_pendientes' => Compra::where('empresa_id', $empresaId)
                ->when($sucursalId, fn($q) => $q->where('sucursal_destino_id', $sucursalId))
                ->where('estado', 'PENDIENTE')
                ->count()
        ];
    }

    /**
     * Métricas de lotes
     */
    private function obtenerMetricasLotes(int $empresaId, ?int $sucursalId): array
    {
        $lotesProximosVencer = Lote::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->count();

        $lotesVencidos = Lote::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('fecha_vencimiento', '<', Carbon::now())
            ->count();

        return [
            'lotes_proximos_vencer' => $lotesProximosVencer,
            'lotes_vencidos' => $lotesVencidos,
            'total_lotes_activos' => Lote::where('empresa_id', $empresaId)
                ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
                ->where('estado_lote', 'activo')
                ->count()
        ];
    }

    /**
     * Calcular crecimiento de ventas comparado con período anterior
     */
    private function calcularCrecimientoVentas(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): array
    {
        $dias = Carbon::parse($fechaInicio)->diffInDays(Carbon::parse($fechaFin)) + 1;
        $fechaInicioAnterior = Carbon::parse($fechaInicio)->subDays($dias);
        $fechaFinAnterior = Carbon::parse($fechaInicio)->subDay();

        $ventasActuales = Venta::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->sum('total');

        $ventasAnteriores = Venta::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->whereBetween('fecha_venta', [$fechaInicioAnterior, $fechaFinAnterior])
            ->sum('total');

        $crecimiento = $ventasAnteriores > 0 
            ? (($ventasActuales - $ventasAnteriores) / $ventasAnteriores) * 100 
            : 0;

        return [
            'ventas_actuales' => $ventasActuales,
            'ventas_anteriores' => $ventasAnteriores,
            'porcentaje_crecimiento' => round($crecimiento, 2)
        ];
    }

    /**
     * Calcular rotación de inventario
     */
    private function calcularRotacionInventario(int $empresaId, $fechaInicio, $fechaFin, ?int $sucursalId): float
    {
        $costoVentas = DB::table('venta_detalles as vd')
            ->join('ventas as v', 'vd.venta_id', '=', 'v.id')
            ->join('productos as p', 'vd.producto_id', '=', 'p.id')
            ->where('v.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('v.sucursal_id', $sucursalId))
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->sum(DB::raw('vd.cantidad * p.precio_costo'));

        $inventarioPromedio = DB::table('producto_stocks as ps')
            ->join('productos as p', 'ps.producto_id', '=', 'p.id')
            ->where('p.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('ps.sucursal_id', $sucursalId))
            ->avg(DB::raw('ps.cantidad_actual * p.precio_costo'));

        return $inventarioPromedio > 0 ? $costoVentas / $inventarioPromedio : 0;
    }

    /**
     * Obtener productos próximos a vencer
     */
    private function obtenerProductosVencidos(int $empresaId, ?int $sucursalId): int
    {
        return Lote::where('empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where('fecha_vencimiento', '<', Carbon::now())
            ->count();
    }

    /**
     * Obtener alertas de stock
     */
    private function obtenerAlertasStock(int $empresaId, ?int $sucursalId): int
    {
        return ProductoStock::join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
            ->where('productos.empresa_id', $empresaId)
            ->when($sucursalId, fn($q) => $q->where('producto_stocks.sucursal_id', $sucursalId))
            ->whereRaw('producto_stocks.cantidad_actual <= productos.stock_minimo')
            ->count();
    }

    /**
     * Generar reporte de eventos de analytics
     */
    public function generarReporteEventos(int $empresaId, array $filtros = []): array
    {
        $query = AnalyticsEvento::where('empresa_id', $empresaId);

        if (isset($filtros['fecha_inicio'])) {
            $query->where('created_at', '>=', $filtros['fecha_inicio']);
        }

        if (isset($filtros['fecha_fin'])) {
            $query->where('created_at', '<=', $filtros['fecha_fin']);
        }

        if (isset($filtros['evento_tipo'])) {
            $query->where('evento_tipo', $filtros['evento_tipo']);
        }

        if (isset($filtros['sucursal_id'])) {
            $query->where('sucursal_id', $filtros['sucursal_id']);
        }

        $eventos = $query->with(['sucursal'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Estadísticas de eventos
        $estadisticas = [
            'total_eventos' => $eventos->total(),
            'eventos_por_tipo' => AnalyticsEvento::where('empresa_id', $empresaId)
                ->selectRaw('evento_tipo, COUNT(*) as total')
                ->groupBy('evento_tipo')
                ->pluck('total', 'evento_tipo'),
            'eventos_por_usuario' => AnalyticsEvento::where('empresa_id', $empresaId)
                ->selectRaw('user_id, COUNT(*) as total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
        ];

        return [
            'eventos' => $eventos,
            'estadisticas' => $estadisticas
        ];
    }
}

