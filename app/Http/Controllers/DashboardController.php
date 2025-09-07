<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantService;
use App\Services\FeatureFlagService;
use App\Services\DashboardService;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\ProductoStock;
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

    public function index(Request $request): Response
    {
        // Inicializar contexto si no está ya inicializado
        if (!$this->tenantService->getEmpresa() && Auth::user() && Auth::user()->empresa_id) {
            $this->tenantService->setEmpresa(Auth::user()->empresa_id);
        }
        
        $empresa = $this->tenantService->getEmpresa();
        $sucursal = $this->tenantService->getSucursal();
        
        if (!$empresa) {
            return Inertia::render('Dashboard', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        // Obtener datos del dashboard
        $stats = $this->getKPIs($empresa->id);
        $recentSales = $this->getRecentSales($empresa->id);
        $lowStock = $this->getLowStockProducts($empresa->id);
        
        // Verificar features disponibles
        $features = Cache::remember("user_features_{$empresa->id}", 300, function () {
            return [
                'pos' => $this->featureFlagService->hasFeature('pos'),
                'inventario_avanzado' => $this->featureFlagService->hasFeature('inventario_avanzado'),
                'reportes' => $this->featureFlagService->hasFeature('reportes'),
                'facturacion_electronica' => $this->featureFlagService->hasFeature('facturacion_electronica'),
            ];
        });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentSales' => $recentSales,
            'lowStock' => $lowStock,
            'features' => $features,
            'empresa' => [
                'id' => $empresa->id,
                'nombre_empresa' => $empresa->nombre_empresa,
                'plan' => [
                    'nombre' => $empresa->plan->nombre ?? 'Sin Plan'
                ]
            ],
            'sucursal' => [
                'id' => $sucursal->id ?? null,
                'nombre' => $sucursal->nombre ?? 'Sin Sucursal'
            ],
        ]);
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
}
