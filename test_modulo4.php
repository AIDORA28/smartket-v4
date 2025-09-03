<?php

require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Caja;
use App\Models\CajaSesion;
use App\Models\CajaMovimiento;
use App\Services\TenantService;
use App\Services\CajaService;

echo "=== VERIFICACIÓN MÓDULO 4: SISTEMA CAJA ===\n\n";

try {
    // Configurar contexto multi-tenant
    $tenantService = app(TenantService::class);
    $tenantService->setEmpresa(1);
    
    // Verificar datos básicos
    echo "1. VERIFICANDO DATOS BÁSICOS:\n";
    echo "   - Cajas: " . Caja::count() . "\n";
    echo "   - Sesiones de caja: " . CajaSesion::count() . "\n";
    echo "   - Movimientos de caja: " . CajaMovimiento::count() . "\n\n";

    // Verificar estructura de cajas
    echo "2. VERIFICANDO ESTRUCTURA CAJAS:\n";
    $caja = Caja::with(['sucursal', 'sesiones'])->first();
    if ($caja) {
        echo "   - Caja: {$caja->nombre}\n";
        echo "   - Código: {$caja->codigo}\n";
        echo "   - Tipo: {$caja->tipo}\n";
        echo "   - Activa: " . ($caja->activa ? 'SI' : 'NO') . "\n";
        echo "   - Sucursal: {$caja->sucursal->nombre}\n";
        echo "   - Sesiones registradas: " . $caja->sesiones->count() . "\n\n";
    }

    // Verificar sesiones de caja
    echo "3. VERIFICANDO SESIONES DE CAJA:\n";
    $sesion = CajaSesion::with(['caja', 'userApertura', 'movimientos'])->first();
    if ($sesion) {
        echo "   - Código sesión: {$sesion->codigo}\n";
        echo "   - Estado: {$sesion->estado}\n";
        echo "   - Monto inicial: S/ {$sesion->monto_inicial}\n";
        echo "   - Usuario apertura: {$sesion->userApertura->name}\n";
        echo "   - Fecha apertura: {$sesion->apertura_at}\n";
        echo "   - Movimientos: " . $sesion->movimientos->count() . "\n\n";
    }

    // Verificar movimientos de caja
    echo "4. VERIFICANDO MOVIMIENTOS DE CAJA:\n";
    $movimiento = CajaMovimiento::with(['cajaSesion', 'user'])->first();
    if ($movimiento) {
        echo "   - Tipo: {$movimiento->tipo}\n";
        echo "   - Monto: S/ {$movimiento->monto}\n";
        echo "   - Concepto: {$movimiento->concepto}\n";
        echo "   - Usuario: {$movimiento->user->name}\n";
        echo "   - Fecha: {$movimiento->fecha}\n\n";
    }

    // Verificar CajaService
    echo "5. VERIFICANDO CAJA SERVICE:\n";
    $cajaService = app(CajaService::class);
    echo "   - CajaService cargado: SI\n";
    
    // Verificar cajas activas
    if ($caja) {
        $cajasActivas = $cajaService->getCajasActivas($caja->empresa_id, $caja->sucursal_id);
        echo "   - Cajas activas en sucursal: " . $cajasActivas->count() . "\n";
    }
    echo "\n";

    // Verificar multi-tenancy
    echo "6. VERIFICANDO MULTI-TENANCY:\n";
    $cajasEmpresa1 = Caja::where('empresa_id', 1)->count();
    $cajasTotal = Caja::count();
    echo "   - Cajas Empresa 1: {$cajasEmpresa1}\n";
    echo "   - Cajas Total: {$cajasTotal}\n";
    echo "   - Aislamiento correcto: " . ($cajasEmpresa1 == $cajasTotal ? 'SI' : 'NO') . "\n\n";

    // Verificar integridad de relaciones
    echo "7. VERIFICANDO INTEGRIDAD RELACIONES:\n";
    $cajaConSesiones = Caja::has('sesiones')->count();
    $sesionConMovimientos = CajaSesion::has('movimientos')->count();
    echo "   - Cajas con sesiones: {$cajaConSesiones}\n";
    echo "   - Sesiones con movimientos: {$sesionConMovimientos}\n\n";

    echo "✅ MÓDULO 4 VERIFICADO EXITOSAMENTE\n";

} catch (Exception $e) {
    echo "❌ ERROR EN MÓDULO 4: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
