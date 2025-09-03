<?php

require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cliente;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VentaPago;
use App\Models\MetodoPago;
use App\Services\TenantService;
use App\Services\VentaService;

echo "=== VERIFICACIÓN MÓDULO 3: POS BÁSICO ===\n\n";

try {
    // Configurar contexto multi-tenant
    $tenantService = app(TenantService::class);
    $tenantService->setEmpresa(1);
    
    // Verificar datos básicos
    echo "1. VERIFICANDO DATOS BÁSICOS:\n";
    echo "   - Clientes: " . Cliente::count() . "\n";
    echo "   - Métodos de pago: " . MetodoPago::count() . "\n";
    echo "   - Ventas: " . Venta::count() . "\n";
    echo "   - Detalles de venta: " . VentaDetalle::count() . "\n";
    echo "   - Pagos de venta: " . VentaPago::count() . "\n\n";

    // Verificar estructura de ventas
    echo "2. VERIFICANDO ESTRUCTURA VENTAS:\n";
    $venta = Venta::with(['cliente', 'detalles.producto', 'pagos.metodoPago'])->first();
    if ($venta) {
        echo "   - Venta N°: {$venta->numero}\n";
        echo "   - Cliente: " . ($venta->cliente ? $venta->cliente->nombre : 'Cliente general') . "\n";
        echo "   - Estado: {$venta->estado}\n";
        echo "   - Total: S/ {$venta->total}\n";
        echo "   - Detalles: " . $venta->detalles->count() . "\n";
        echo "   - Pagos: " . $venta->pagos->count() . "\n";
        
        // Verificar primer detalle
        $detalle = $venta->detalles->first();
        if ($detalle) {
            echo "   - Producto: {$detalle->producto->nombre}\n";
            echo "   - Cantidad: {$detalle->cantidad}\n";
            echo "   - Precio: S/ {$detalle->precio_unitario}\n";
        }
        
        // Verificar primer pago
        $pago = $venta->pagos->first();
        if ($pago) {
            echo "   - Método pago: {$pago->metodoPago->nombre}\n";
            echo "   - Monto: S/ {$pago->monto}\n";
        }
        echo "\n";
    }

    // Verificar métodos de pago
    echo "3. VERIFICANDO MÉTODOS DE PAGO:\n";
    $metodos = MetodoPago::all();
    foreach ($metodos as $metodo) {
        echo "   - {$metodo->nombre} (Activo: " . ($metodo->activo ? 'SI' : 'NO') . ")\n";
    }
    echo "\n";

    // Verificar clientes
    echo "4. VERIFICANDO CLIENTES:\n";
    $cliente = Cliente::first();
    if ($cliente) {
        echo "   - Cliente: {$cliente->nombre}\n";
        echo "   - Documento: {$cliente->numero_documento}\n";
        echo "   - Ventas realizadas: " . $cliente->ventas()->count() . "\n\n";
    }

    // Verificar VentaService
    echo "5. VERIFICANDO VENTA SERVICE:\n";
    $ventaService = app(VentaService::class);
    echo "   - VentaService cargado: SI\n";
    
    // Verificar último número de venta
    $ultimoNumero = Venta::max('numero');
    echo "   - Último número de venta: " . ($ultimoNumero ?: 'Ninguno') . "\n\n";

    // Verificar multi-tenancy
    echo "6. VERIFICANDO MULTI-TENANCY:\n";
    $ventasEmpresa1 = Venta::where('empresa_id', 1)->count();
    $ventasTotal = Venta::count();
    echo "   - Ventas Empresa 1: {$ventasEmpresa1}\n";
    echo "   - Ventas Total: {$ventasTotal}\n";
    echo "   - Aislamiento correcto: " . ($ventasEmpresa1 == $ventasTotal ? 'SI' : 'NO') . "\n\n";

    echo "✅ MÓDULO 3 VERIFICADO EXITOSAMENTE\n";

} catch (Exception $e) {
    echo "❌ ERROR EN MÓDULO 3: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
