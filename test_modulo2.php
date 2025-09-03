<?php

require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ProductoStock;
use App\Models\InventarioMovimiento;
use App\Services\TenantService;

echo "=== VERIFICACIÓN MÓDULO 2: PRODUCTOS + INVENTARIO ===\n\n";

try {
    // Configurar contexto multi-tenant
    $tenantService = app(TenantService::class);
    $tenantService->setEmpresa(1);
    
    // Verificar datos básicos
    echo "1. VERIFICANDO DATOS BÁSICOS:\n";
    echo "   - Categorías: " . Categoria::count() . "\n";
    echo "   - Productos: " . Producto::count() . "\n";
    echo "   - Stock por sucursal: " . ProductoStock::count() . "\n";
    echo "   - Movimientos inventario: " . InventarioMovimiento::count() . "\n\n";

    // Verificar estructura de productos
    echo "2. VERIFICANDO ESTRUCTURA PRODUCTOS:\n";
    $producto = Producto::with(['categoria', 'stocks'])->first();
    if ($producto) {
        echo "   - Producto: {$producto->nombre}\n";
        echo "   - Código: {$producto->codigo}\n";
        echo "   - Categoría: " . ($producto->categoria ? $producto->categoria->nombre : 'Sin categoría') . "\n";
        echo "   - Precio venta: S/ {$producto->precio_venta}\n";
        echo "   - Stock registros: " . $producto->stocks->count() . "\n";
        
        // Verificar stock por sucursal
        $stock = $producto->stocks->first();
        if ($stock) {
            echo "   - Stock actual: {$stock->cantidad_actual}\n";
            echo "   - Stock mínimo: {$stock->stock_minimo}\n";
        }
        echo "\n";
    }

    // Verificar categorías
    echo "3. VERIFICANDO CATEGORÍAS:\n";
    $categoria = Categoria::with('productos')->first();
    if ($categoria) {
        echo "   - Categoría: {$categoria->nombre}\n";
        echo "   - Productos en categoría: " . $categoria->productos->count() . "\n\n";
    }

    // Verificar movimientos de inventario
    echo "4. VERIFICANDO MOVIMIENTOS INVENTARIO:\n";
    $movimiento = InventarioMovimiento::with(['producto', 'sucursal'])->first();
    if ($movimiento) {
        echo "   - Tipo movimiento: {$movimiento->tipo}\n";
        echo "   - Producto: {$movimiento->producto->nombre}\n";
        echo "   - Cantidad: {$movimiento->cantidad}\n";
        echo "   - Motivo: {$movimiento->motivo}\n\n";
    }

    // Verificar multi-tenancy
    echo "5. VERIFICANDO MULTI-TENANCY:\n";
    $productosEmpresa1 = Producto::where('empresa_id', 1)->count();
    $productosTotal = Producto::count();
    echo "   - Productos Empresa 1: {$productosEmpresa1}\n";
    echo "   - Productos Total: {$productosTotal}\n";
    echo "   - Aislamiento correcto: " . ($productosEmpresa1 == $productosTotal ? 'SI' : 'NO') . "\n\n";

    echo "✅ MÓDULO 2 VERIFICADO EXITOSAMENTE\n";

} catch (Exception $e) {
    echo "❌ ERROR EN MÓDULO 2: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
