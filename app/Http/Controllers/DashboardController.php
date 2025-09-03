<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        $sucursal = $this->tenantService->getSucursal();
        
        // Datos básicos del dashboard
        $stats = [
            'ventas_hoy' => 0, // Placeholder para módulo futuro
            'productos_stock_bajo' => 0, // Placeholder para módulo futuro
            'total_productos' => 0, // Placeholder para módulo futuro
            'sucursales_activas' => $empresa->sucursales()->where('activa', true)->count(),
        ];

        // Verificar features disponibles
        $features = [
            'pos' => $this->featureFlagService->hasFeature('pos'),
            'inventario_avanzado' => $this->featureFlagService->hasFeature('inventario_avanzado'),
            'reportes' => $this->featureFlagService->hasFeature('reportes'),
            'facturacion_electronica' => $this->featureFlagService->hasFeature('facturacion_electronica'),
        ];

        return view('dashboard.index', compact('empresa', 'sucursal', 'stats', 'features'));
    }
}
