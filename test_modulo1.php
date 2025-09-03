<?php

require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Plan;
use App\Models\FeatureFlag;
use App\Services\TenantService;
use App\Services\FeatureFlagService;

echo "=== VERIFICACIÓN MÓDULO 1: CORE MULTI-TENANT ===\n\n";

try {
    // Verificar datos básicos
    echo "1. VERIFICANDO DATOS BÁSICOS:\n";
    echo "   - Planes: " . Plan::count() . "\n";
    echo "   - Empresas: " . Empresa::count() . "\n";
    echo "   - Sucursales: " . Sucursal::count() . "\n";
    echo "   - Feature Flags: " . FeatureFlag::count() . "\n\n";

    // Verificar relaciones
    echo "2. VERIFICANDO RELACIONES:\n";
    $empresa = Empresa::first();
    if ($empresa) {
        echo "   - Empresa: {$empresa->nombre}\n";
        echo "   - Plan: {$empresa->plan->nombre}\n";
        echo "   - Sucursales: " . $empresa->sucursales->count() . "\n";
        echo "   - Feature Flags: " . $empresa->featureFlags->count() . "\n\n";
    }

    // Verificar TenantService
    echo "3. VERIFICANDO TENANT SERVICE:\n";
    $tenantService = app(TenantService::class);
    $tenantService->setEmpresa($empresa->id);
    echo "   - Empresa actual: {$tenantService->getEmpresa()->nombre}\n";
    echo "   - Sucursal actual: {$tenantService->getSucursal()->nombre}\n\n";

    // Verificar FeatureFlagService
    echo "4. VERIFICANDO FEATURE FLAGS:\n";
    $flagService = app(FeatureFlagService::class);
    echo "   - POS habilitado: " . ($flagService->isEnabled('pos') ? 'SI' : 'NO') . "\n";
    echo "   - Inventario habilitado: " . ($flagService->isEnabled('inventario') ? 'SI' : 'NO') . "\n";
    echo "   - Caja habilitado: " . ($flagService->isEnabled('caja') ? 'SI' : 'NO') . "\n\n";

    echo "✅ MÓDULO 1 VERIFICADO EXITOSAMENTE\n";

} catch (Exception $e) {
    echo "❌ ERROR EN MÓDULO 1: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
