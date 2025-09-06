<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantService;
use App\Services\FeatureFlagService;

class DashboardController extends Controller
{
    protected $tenantService;
    protected $featureFlagService;

    public function __construct(TenantService $tenantService, FeatureFlagService $featureFlagService)
    {
        $this->tenantService = $tenantService;
        $this->featureFlagService = $featureFlagService;
    }

    public function index(Request $request): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        $sucursal = $this->tenantService->getSucursal();
        
        // Stats optimizados con cache para evitar multiple queries
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'totalSales' => DB::table('ventas')->count(),
                'totalProducts' => DB::table('productos')->where('activo', true)->count(),
                'totalCustomers' => DB::table('clientes')->count(),
                'totalRevenue' => DB::table('ventas')
                    ->where('estado', 'completada')
                    ->sum('total') ?? 0,
            ];
        });

        // Verificar features disponibles (también en cache)
        $features = Cache::remember('user_features', 300, function () {
            return [
                'pos' => $this->featureFlagService->hasFeature('pos'),
                'inventario_avanzado' => $this->featureFlagService->hasFeature('inventario_avanzado'),
                'reportes' => $this->featureFlagService->hasFeature('reportes'),
                'facturacion_electronica' => $this->featureFlagService->hasFeature('facturacion_electronica'),
            ];
        });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'features' => $features,
            'empresa' => $empresa,
            'sucursal' => $sucursal,
        ]);
    }
}
