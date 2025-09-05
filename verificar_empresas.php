<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO EMPRESAS EN EL SISTEMA ===\n";

$empresas = \App\Models\Empresa::all();

if ($empresas->isEmpty()) {
    echo "❌ NO HAY EMPRESAS EN LA BASE DE DATOS\n";
    echo "Esto explica el error 'Attempt to read property \"id\" on null'\n\n";
    
    echo "Creando empresa de prueba...\n";
    $empresa = \App\Models\Empresa::create([
        'nombre' => 'Empresa de Prueba',
        'ruc' => '12345678901',
        'direccion' => 'Dirección de prueba',
        'telefono' => '123456789',
        'email' => 'test@empresa.com',
        'estado' => 'activo'
    ]);
    
    echo "✅ Empresa creada: " . $empresa->nombre . " (ID: " . $empresa->id . ")\n";
} else {
    echo "✅ Empresas encontradas: " . $empresas->count() . "\n";
    foreach ($empresas as $empresa) {
        echo "  - {$empresa->nombre} (ID: {$empresa->id})\n";
    }
}

echo "\n=== VERIFICANDO TENANTSERVICE ===\n";
try {
    $tenantService = app(\App\Services\TenantService::class);
    $empresaActual = $tenantService->getEmpresa();
    if ($empresaActual) {
        echo "✅ TenantService funciona: " . $empresaActual->nombre . "\n";
    } else {
        echo "⚠️ TenantService retorna null\n";
    }
} catch (\Exception $e) {
    echo "❌ Error en TenantService: " . $e->getMessage() . "\n";
}
?>
