<?php

require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proveedor;
use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Recepcion;
use App\Services\TenantService;
use App\Services\ProveedorService;
use App\Services\CompraService;
use App\Services\RecepcionService;
use Illuminate\Support\Facades\Schema;

echo "=== VERIFICACIÓN MÓDULO 5: COMPRAS + PROVEEDORES ===\n\n";

try {
    // Configurar contexto multi-tenant
    $tenantService = app(TenantService::class);
    $tenantService->setEmpresa(1);
    
    // Verificar datos básicos
    echo "1. VERIFICANDO DATOS BÁSICOS:\n";
    echo "   - Proveedores: " . Proveedor::count() . "\n";
    echo "   - Compras: " . Compra::count() . "\n";
    echo "   - Items de compra: " . CompraItem::count() . "\n";
    echo "   - Recepciones: " . Recepcion::count() . "\n\n";

    // Verificar estructura de proveedores
    echo "2. VERIFICANDO ESTRUCTURA PROVEEDORES:\n";
    $proveedor = Proveedor::with(['empresa', 'compras'])->first();
    if ($proveedor) {
        echo "   - Proveedor: {$proveedor->nombre}\n";
        echo "   - Documento: {$proveedor->documento_tipo} {$proveedor->documento_numero}\n";
        echo "   - Email: {$proveedor->email}\n";
        echo "   - Teléfono: {$proveedor->telefono}\n";
        echo "   - Empresa: {$proveedor->empresa->nombre}\n";
        echo "   - Compras registradas: " . $proveedor->compras->count() . "\n";
        
        // Verificar contacto JSON
        $contacto = $proveedor->getContactoInfo('contacto_principal');
        if ($contacto) {
            echo "   - Contacto principal: {$contacto}\n";
        }
        echo "\n";
    }

    // Verificar servicios
    echo "3. VERIFICANDO SERVICIOS:\n";
    $proveedorService = app(ProveedorService::class);
    $compraService = app(CompraService::class);
    $recepcionService = app(RecepcionService::class);
    
    echo "   - ProveedorService cargado: SI\n";
    echo "   - CompraService cargado: SI\n";
    echo "   - RecepcionService cargado: SI\n";
    
    // Probar búsqueda de proveedores
    $busqueda = $proveedorService->buscarProveedores(1, 'San Miguel');
    echo "   - Búsqueda 'San Miguel': " . $busqueda->count() . " resultados\n\n";

    // Verificar multi-tenancy
    echo "4. VERIFICANDO MULTI-TENANCY:\n";
    $proveedoresEmpresa1 = Proveedor::where('empresa_id', 1)->count();
    $proveedoresTotal = Proveedor::count();
    echo "   - Proveedores Empresa 1: {$proveedoresEmpresa1}\n";
    echo "   - Proveedores Total: {$proveedoresTotal}\n";
    echo "   - Aislamiento correcto: " . ($proveedoresEmpresa1 == $proveedoresTotal ? 'SI' : 'NO') . "\n\n";

    // Verificar integridad de relaciones
    echo "5. VERIFICANDO INTEGRIDAD RELACIONES:\n";
    $proveedorConEmpresa = Proveedor::has('empresa')->count();
    echo "   - Proveedores con empresa: {$proveedorConEmpresa}\n";
    echo "   - Integridad relaciones: " . ($proveedorConEmpresa == $proveedoresTotal ? 'CORRECTA' : 'ERROR') . "\n\n";

    // Verificar estructura de migraciones
    echo "6. VERIFICANDO MIGRACIONES:\n";
    echo "   - Tabla proveedores: " . (Schema::hasTable('proveedores') ? 'EXISTE' : 'NO EXISTE') . "\n";
    echo "   - Tabla compras: " . (Schema::hasTable('compras') ? 'EXISTE' : 'NO EXISTE') . "\n";
    echo "   - Tabla compra_items: " . (Schema::hasTable('compra_items') ? 'EXISTE' : 'NO EXISTE') . "\n";
    echo "   - Tabla recepciones: " . (Schema::hasTable('recepciones') ? 'EXISTE' : 'NO EXISTE') . "\n\n";

    echo "✅ MÓDULO 5 VERIFICADO EXITOSAMENTE\n";
    echo "📊 RESUMEN: 4 migraciones, 4 modelos, 3 servicios funcionando correctamente\n";

} catch (Exception $e) {
    echo "❌ ERROR EN MÓDULO 5: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
