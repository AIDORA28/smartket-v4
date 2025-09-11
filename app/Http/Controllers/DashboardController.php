<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\TenantService;
use App\Services\FeatureFlagService;
use App\Services\DashboardService;
use App\Models\Core\Empresa;
use App\Models\Inventory\Producto;
use App\Models\Sales\Venta;
use App\Models\Sales\VentaDetalle;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Inventory\ProductoStock;
// 🔥 NUEVOS IMPORTS PARA TABLAS ADICIONALES
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Inventory\InventarioMovimiento;
use App\Models\AnalyticsEvento;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $tenantService;
    protected $featureFlagService;
    protected $dashboardService;

    public function __construct(
        TenantService $tenantService, 
        FeatureFlagService $featureFlagService,
        DashboardService $dashboardService = null
    ) {
        $this->tenantService = $tenantService;
        $this->featureFlagService = $featureFlagService;
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $empresa = $user->empresa;
            $sucursal = $user->sucursal;

            // Stats básicas mejoradas CON DATOS REALES
            $stats = [
                'ventasHoy' => $this->getVentasHoy(),
                'productosStock' => $this->getProductosEnStock(),
                'clientesActivos' => $this->getClientesActivos(),
                'facturacionMensual' => $this->getFacturacionMensual(),
            ];

            // Ventas recientes (últimas 5)
            $recentSales = $this->getRecentSales($empresa->id);

            // Productos con stock bajo
            $lowStock = $this->getLowStockProducts($empresa->id);

            // Nuevos datos para dashboard mejorado CON BD REAL
            $topProducts = $this->getTopProducts($empresa->id);
            $salesTrend = $this->getSalesTrend($empresa->id);
            $cajaStatus = $this->getCajaStatus($user->id, $sucursal->id);

            // ✅ NUEVOS DATOS: Resumen de arquitectura real
            $inventoryOverview = $this->getInventoryOverview($empresa->id);
            $recentActivity = $this->getRecentActivity($empresa->id);
            
            // 🔥 AGREGADOS: Más datos de la BD
            $comprasRecientes = $this->getComprasRecientes($empresa->id);
            $lotesProximosVencer = $this->getLotesProximosVencer($empresa->id);
            $movimientosInventario = $this->getMovimientosInventarioRecientes($empresa->id);
            $proveedoresSummary = $this->getProveedoresSummary($empresa->id);
            $analyticsEvents = $this->getAnalyticsEvents($empresa->id);

            // Features disponibles según el plan
            $features = [
                'pos' => true, // Básico en todos los planes
                'inventario_avanzado' => in_array($empresa->plan->nombre ?? '', ['Pro', 'Premium', 'Enterprise']),
                'reportes' => in_array($empresa->plan->nombre ?? '', ['Pro', 'Premium', 'Enterprise']),
                'facturacion_electronica' => in_array($empresa->plan->nombre ?? '', ['Premium', 'Enterprise']),
            ];

            return inertia('Dashboard', [
                'stats' => $stats,
                'recentSales' => $recentSales,
                'lowStock' => $lowStock,
                'topProducts' => $topProducts,
                'salesTrend' => $salesTrend,
                'cajaStatus' => $cajaStatus,
                'inventoryOverview' => $inventoryOverview, // ✅ NUEVO
                'recentActivity' => $recentActivity, // ✅ NUEVO
                // 🔥 DATOS ADICIONALES DE TABLAS BD
                'comprasRecientes' => $comprasRecientes,
                'lotesProximosVencer' => $lotesProximosVencer,
                'movimientosInventario' => $movimientosInventario,
                'proveedoresSummary' => $proveedoresSummary,
                'analyticsEvents' => $analyticsEvents,
                'empresa' => [
                    'id' => $empresa->id,
                    'nombre_empresa' => $empresa->nombre_empresa,
                    'nombre' => $empresa->nombre,
                    'plan' => [
                        'nombre' => $empresa->plan->nombre ?? 'Básico',
                    ],
                ],
                'sucursal' => [
                    'id' => $sucursal->id,
                    'nombre' => $sucursal->nombre,
                ],
                'features' => $features,
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard Controller Error: ' . $e->getMessage());
            
            // Datos por defecto en caso de error
            return inertia('Dashboard', [
                'stats' => [
                    'ventasHoy' => 0,
                    'productosStock' => 0,
                    'clientesActivos' => 0,
                    'facturacionMensual' => 0,
                ],
                'recentSales' => [],
                'lowStock' => [],
                'topProducts' => [],
                'salesTrend' => [],
                'cajaStatus' => [
                    'activa' => false,
                    'mensaje' => 'No hay caja activa',
                ],
                'inventoryOverview' => [
                    'totalProductos' => 0,
                    'totalCategorias' => 0,
                    'totalMarcas' => 0,
                    'totalUnidades' => 0,
                ],
                'recentActivity' => [],
                // 🔥 DATOS POR DEFECTO ADICIONALES
                'comprasRecientes' => [],
                'lotesProximosVencer' => [],
                'movimientosInventario' => [],
                'proveedoresSummary' => [],
                'analyticsEvents' => [],
                'empresa' => [
                    'id' => 1,
                    'nombre_empresa' => 'SmartKet',
                    'nombre' => 'SmartKet',
                    'plan' => ['nombre' => 'Básico'],
                ],
                'sucursal' => [
                    'id' => 1,
                    'nombre' => 'Principal',
                ],
                'features' => [
                    'pos' => true,
                    'inventario_avanzado' => false,
                    'reportes' => false,
                    'facturacion_electronica' => false,
                ],
            ]);
        }
    }

    private function getKPIs($empresaId)
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        
        // Cache por 5 minutos para optimizar rendimiento
        return Cache::remember("dashboard_kpis_{$empresaId}_{$hoy->format('Y-m-d')}", 300, function () use ($empresaId, $hoy, $inicioMes, $finMes) {
            // Ventas de hoy
            $ventasHoy = Venta::where('empresa_id', $empresaId)
                ->whereDate('fecha_venta', $hoy)
                ->get();
                
            // Ventas del mes
            $ventasMes = Venta::where('empresa_id', $empresaId)
                ->whereBetween('fecha_venta', [$inicioMes, $finMes])
                ->get();
                
            // Productos en stock
            $productosStock = Producto::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->count();
                
            // Clientes activos (con compras en los últimos 3 meses)
            $clientesActivos = Cliente::where('empresa_id', $empresaId)
                ->whereHas('ventas', function($query) {
                    $query->where('fecha_venta', '>=', Carbon::now()->subMonths(3));
                })
                ->count();

            return [
                'ventasHoy' => $ventasHoy->sum('total'),
                'productosStock' => $productosStock,
                'clientesActivos' => $clientesActivos,
                'facturacionMensual' => $ventasMes->sum('total')
            ];
        });
    }

    private function getRecentSales($empresaId)
    {
        return Cache::remember("dashboard_recent_sales_{$empresaId}", 300, function () use ($empresaId) {
            return Venta::where('empresa_id', $empresaId)
                ->with(['cliente'])
                ->orderBy('fecha_venta', 'desc')
                ->limit(5)
                ->get()
                ->map(function($venta) {
                    return [
                        'id' => $venta->id,
                        'cliente' => $venta->cliente->nombre ?? 'Cliente General',
                        'total' => $venta->total,
                        'fecha' => $venta->fecha_venta->format('d/m H:i'),
                        'productos' => $venta->detalles->count() ?? 0
                    ];
                })->toArray();
        });
    }

    private function getLowStockProducts($empresaId)
    {
        return Cache::remember("dashboard_low_stock_{$empresaId}", 600, function () use ($empresaId) {
            return Producto::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->with(['stocks' => function($query) {
                    $query->selectRaw('producto_id, SUM(cantidad_actual) as total_stock')
                          ->groupBy('producto_id');
                }])
                ->get()
                ->filter(function($producto) {
                    $totalStock = $producto->stocks->sum('cantidad_actual') ?? 0;
                    return $totalStock <= $producto->stock_minimo;
                })
                ->sortBy(function($producto) {
                    return $producto->stocks->sum('cantidad_actual') ?? 0;
                })
                ->take(5)
                ->map(function($producto) {
                    $stockActual = $producto->stocks->sum('cantidad_actual') ?? 0;
                    return [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'stock' => $stockActual,
                        'minimo' => $producto->stock_minimo
                    ];
                })
                ->values()
                ->toArray();
        });
    }

    private function getTopProducts($empresaId)
    {
        return Cache::remember("dashboard_top_products_{$empresaId}", 600, function () use ($empresaId) {
            return VentaDetalle::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'), DB::raw('SUM(total) as ingresos'))
                ->whereHas('venta', function($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId)
                          ->where('fecha_venta', '>=', Carbon::now()->subDays(30));
                })
                ->with(['producto' => function($query) {
                    $query->select('id', 'nombre', 'precio_venta');
                }])
                ->groupBy('producto_id')
                ->orderByDesc('total_vendido')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->producto_id,
                        'nombre' => $item->producto->nombre ?? 'Producto',
                        'total_vendido' => $item->total_vendido,
                        'ingresos' => $item->ingresos,
                        'precio_promedio' => $item->ingresos / $item->total_vendido
                    ];
                })
                ->toArray();
        });
    }

    private function getSalesTrend($empresaId)
    {
        return Cache::remember("dashboard_sales_trend_{$empresaId}", 300, function () use ($empresaId) {
            $trend = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $sales = Venta::where('empresa_id', $empresaId)
                    ->whereDate('fecha_venta', $date)
                    ->sum('total');
                
                $trend[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('D'),
                    'sales' => $sales
                ];
            }
            return $trend;
        });
    }

    private function getCajaStatus($empresaId, $sucursalId)
    {
        return Cache::remember("dashboard_caja_status_{$empresaId}_{$sucursalId}", 300, function () use ($empresaId, $sucursalId) {
            $sesionActiva = DB::table('caja_sesiones')
                ->join('cajas', 'caja_sesiones.caja_id', '=', 'cajas.id')
                ->where('cajas.empresa_id', $empresaId)
                ->where('cajas.sucursal_id', $sucursalId)
                ->where('caja_sesiones.estado', 'abierta')
                ->select('caja_sesiones.*', 'cajas.nombre as caja_nombre')
                ->first();

            if (!$sesionActiva) {
                return [
                    'activa' => false,
                    'mensaje' => 'No hay sesión de caja activa'
                ];
            }

            $ventasEfectivoHoy = DB::table('ventas')
                ->join('venta_pagos', 'ventas.id', '=', 'venta_pagos.venta_id')
                ->join('metodos_pago', 'venta_pagos.metodo_pago_id', '=', 'metodos_pago.id')
                ->where('ventas.empresa_id', $empresaId)
                ->where('ventas.sucursal_id', $sucursalId)
                ->where('metodos_pago.codigo', 'EFE')
                ->whereDate('ventas.fecha_venta', Carbon::today())
                ->sum('venta_pagos.monto');

            return [
                'activa' => true,
                'caja_nombre' => $sesionActiva->caja_nombre,
                'codigo' => $sesionActiva->codigo,
                'apertura_at' => $sesionActiva->apertura_at,
                'monto_inicial' => $sesionActiva->monto_inicial,
                'ventas_efectivo_hoy' => $ventasEfectivoHoy,
                'total_estimado' => $sesionActiva->monto_inicial + $ventasEfectivoHoy,
                'usuario_apertura' => 'José Pérez' // Simplificado por ahora
            ];
        });
    }

    /**
     * Obtener las ventas del día actual
     */
    private function getVentasHoy()
    {
        $empresa_id = Auth::user()->empresa_id;
        
        return Venta::where('empresa_id', $empresa_id)
            ->whereDate('created_at', Carbon::today())
            ->sum('total') ?? 0;
    }

    /**
     * Obtener cantidad de productos en stock
     */
    private function getProductosEnStock()
    {
        $empresa_id = Auth::user()->empresa_id;
        
        return Producto::where('empresa_id', $empresa_id)
            ->where('activo', true)
            ->count() ?? 0;
    }

    /**
     * Obtener clientes activos del mes
     */
    private function getClientesActivos()
    {
        $empresa_id = Auth::user()->empresa_id;
        
        return Cliente::where('empresa_id', $empresa_id)
            ->where('activo', true)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count() ?? 0;
    }

    /**
     * Obtener facturación del mes actual
     */
    private function getFacturacionMensual()
    {
        $empresa_id = Auth::user()->empresa_id;
        
        return Venta::where('empresa_id', $empresa_id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total') ?? 0;
    }

    /**
     * 🔥 NUEVO: Resumen del inventario con datos reales
     */
    private function getInventoryOverview($empresaId)
    {
        return Cache::remember("dashboard_inventory_overview_{$empresaId}", 600, function () use ($empresaId) {
            $totalProductos = Producto::where('empresa_id', $empresaId)->where('activo', true)->count();
            $totalCategorias = DB::table('categorias')->where('empresa_id', $empresaId)->where('activa', true)->count();
            $totalMarcas = DB::table('marcas')->where('empresa_id', $empresaId)->where('activa', 1)->count();
            $totalUnidades = DB::table('unidades_medida')->where('empresa_id', $empresaId)->where('activa', 1)->count();
            
            return [
                'totalProductos' => $totalProductos,
                'totalCategorias' => $totalCategorias,
                'totalMarcas' => $totalMarcas,
                'totalUnidades' => $totalUnidades,
                'marcasPopulares' => DB::table('marcas')
                    ->where('empresa_id', $empresaId)
                    ->where('activa', 1)
                    ->orderBy('productos_count', 'desc')
                    ->limit(3)
                    ->pluck('nombre')
                    ->toArray(),
                'categoriasPopulares' => DB::table('categorias')
                    ->where('empresa_id', $empresaId)
                    ->where('activa', true)
                    ->orderBy('id')
                    ->limit(3)
                    ->pluck('nombre')
                    ->toArray(),
            ];
        });
    }

    /**
     * 🔥 NUEVO: Actividad reciente del sistema
     */
    private function getRecentActivity($empresaId)
    {
        return Cache::remember("dashboard_recent_activity_{$empresaId}", 300, function () use ($empresaId) {
            $activities = [];
            
            // Productos creados recientemente
            $recentProducts = Producto::where('empresa_id', $empresaId)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get(['nombre', 'created_at']);
                
            foreach ($recentProducts as $product) {
                $activities[] = [
                    'type' => 'product_created',
                    'message' => "Producto '{$product->nombre}' agregado",
                    'time' => $product->created_at->diffForHumans(),
                    'icon' => '📦',
                    'color' => 'blue'
                ];
            }
            
            // Categorías nuevas
            $recentCategories = DB::table('categorias')
                ->where('empresa_id', $empresaId)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get(['nombre', 'created_at']);
                
            foreach ($recentCategories as $category) {
                $activities[] = [
                    'type' => 'category_created',
                    'message' => "Categoría '{$category->nombre}' creada",
                    'time' => Carbon::parse($category->created_at)->diffForHumans(),
                    'icon' => '🏷️',
                    'color' => 'green'
                ];
            }
            
            // Ordenar por tiempo
            usort($activities, function($a, $b) {
                return strcmp($b['time'], $a['time']);
            });
            
            return array_slice($activities, 0, 5);
        });
    }

    /**
     * 🔥 NUEVO: Compras recientes de la empresa
     */
    private function getComprasRecientes($empresaId)
    {
        return Cache::remember("dashboard_compras_recientes_{$empresaId}", 300, function () use ($empresaId) {
            return DB::table('compras')
                ->join('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
                ->where('compras.empresa_id', $empresaId)
                ->where('compras.created_at', '>=', Carbon::now()->subDays(7))
                ->select('compras.id', 'compras.numero_compra', 'compras.total', 'compras.fecha_compra', 'compras.estado', 'proveedores.nombre as proveedor')
                ->orderBy('compras.created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($compra) {
                    return [
                        'id' => $compra->id,
                        'numero' => $compra->numero_compra,
                        'proveedor' => $compra->proveedor,
                        'total' => $compra->total,
                        'fecha' => Carbon::parse($compra->fecha_compra)->format('d/m H:i'),
                        'estado' => $compra->estado,
                        'color' => $compra->estado === 'completada' ? 'green' : 'yellow'
                    ];
                })
                ->toArray();
        });
    }

    /**
     * 🔥 NUEVO: Lotes próximos a vencer
     */
    private function getLotesProximosVencer($empresaId)
    {
        return Cache::remember("dashboard_lotes_vencer_{$empresaId}", 600, function () use ($empresaId) {
            return DB::table('lotes')
                ->join('productos', 'lotes.producto_id', '=', 'productos.id')
                ->where('productos.empresa_id', $empresaId)
                ->where('lotes.fecha_vencimiento', '<=', Carbon::now()->addDays(30))
                ->where('lotes.cantidad_actual', '>', 0)
                ->select('lotes.id', 'lotes.codigo', 'lotes.fecha_vencimiento', 'lotes.cantidad_actual', 'productos.nombre as producto')
                ->orderBy('lotes.fecha_vencimiento', 'asc')
                ->limit(5)
                ->get()
                ->map(function($lote) {
                    $diasParaVencer = Carbon::now()->diffInDays(Carbon::parse($lote->fecha_vencimiento), false);
                    return [
                        'id' => $lote->id,
                        'codigo' => $lote->codigo,
                        'producto' => $lote->producto,
                        'fecha_vencimiento' => Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y'),
                        'cantidad' => $lote->cantidad_actual,
                        'dias_para_vencer' => $diasParaVencer,
                        'urgencia' => $diasParaVencer <= 7 ? 'critica' : ($diasParaVencer <= 15 ? 'media' : 'baja'),
                        'color' => $diasParaVencer <= 7 ? 'red' : ($diasParaVencer <= 15 ? 'yellow' : 'blue')
                    ];
                })
                ->toArray();
        });
    }

    /**
     * 🔥 NUEVO: Movimientos de inventario recientes
     */
    private function getMovimientosInventarioRecientes($empresaId)
    {
        return Cache::remember("dashboard_movimientos_inventario_{$empresaId}", 300, function () use ($empresaId) {
            return DB::table('inventario_movimientos')
                ->join('productos', 'inventario_movimientos.producto_id', '=', 'productos.id')
                ->where('productos.empresa_id', $empresaId)
                ->where('inventario_movimientos.created_at', '>=', Carbon::now()->subDays(7))
                ->select('inventario_movimientos.*', 'productos.nombre as producto')
                ->orderBy('inventario_movimientos.created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($movimiento) {
                    return [
                        'id' => $movimiento->id,
                        'producto' => $movimiento->producto,
                        'tipo' => $movimiento->tipo_movimiento,
                        'cantidad' => $movimiento->cantidad,
                        'motivo' => $movimiento->motivo ?? 'Sin especificar',
                        'fecha' => Carbon::parse($movimiento->created_at)->format('d/m H:i'),
                        'icon' => $movimiento->tipo_movimiento === 'entrada' ? '📈' : '📉',
                        'color' => $movimiento->tipo_movimiento === 'entrada' ? 'green' : 'red'
                    ];
                })
                ->toArray();
        });
    }

    /**
     * 🔥 NUEVO: Resumen de proveedores
     */
    private function getProveedoresSummary($empresaId)
    {
        return Cache::remember("dashboard_proveedores_summary_{$empresaId}", 600, function () use ($empresaId) {
            $totalProveedores = DB::table('proveedores')->where('empresa_id', $empresaId)->where('activo', true)->count();
            
            $comprasUltimos30Dias = DB::table('compras')
                ->join('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
                ->where('proveedores.empresa_id', $empresaId)
                ->where('compras.fecha_compra', '>=', Carbon::now()->subDays(30))
                ->sum('compras.total');

            $proveedorTopMes = DB::table('compras')
                ->join('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
                ->where('proveedores.empresa_id', $empresaId)
                ->where('compras.fecha_compra', '>=', Carbon::now()->subDays(30))
                ->select('proveedores.nombre', DB::raw('SUM(compras.total) as total_comprado'))
                ->groupBy('proveedores.id', 'proveedores.nombre')
                ->orderByDesc('total_comprado')
                ->first();
                
            return [
                'totalProveedores' => $totalProveedores,
                'comprasUltimos30Dias' => $comprasUltimos30Dias,
                'proveedorTopMes' => [
                    'nombre' => $proveedorTopMes->nombre ?? 'N/A',
                    'total' => $proveedorTopMes->total_comprado ?? 0
                ]
            ];
        });
    }

    /**
     * 🔥 NUEVO: Eventos de analytics recientes
     */
    private function getAnalyticsEvents($empresaId)
    {
        return Cache::remember("dashboard_analytics_events_{$empresaId}", 300, function () use ($empresaId) {
            return DB::table('analytics_eventos')
                ->where('empresa_id', $empresaId)
                ->where('fecha_evento', '>=', Carbon::now()->subDays(1))
                ->select('evento', 'contexto', 'fecha_evento', DB::raw('COUNT(*) as cantidad'))
                ->groupBy('evento', 'contexto', 'fecha_evento')
                ->orderBy('fecha_evento', 'desc')
                ->limit(5)
                ->get()
                ->map(function($evento) {
                    return [
                        'evento' => $evento->evento,
                        'contexto' => $evento->contexto,
                        'cantidad' => $evento->cantidad,
                        'fecha' => Carbon::parse($evento->fecha_evento)->format('d/m H:i'),
                        'icon' => $this->getEventIcon($evento->evento)
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Obtener icono según el tipo de evento
     */
    private function getEventIcon($evento)
    {
        $iconos = [
            'login' => '🚀',
            'venta_creada' => '💰',
            'producto_agregado' => '📦',
            'cliente_registrado' => '👤',
            'compra_realizada' => '🛍️',
            'caja_abierta' => '💳',
            'default' => '📊'
        ];

        return $iconos[$evento] ?? $iconos['default'];
    }
}

