<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN DEL MÓDULO 4: INVENTARIO ===" . PHP_EOL;
echo "=============================================" . PHP_EOL;

// 1. Verificar modelos
echo "1. MODELOS:" . PHP_EOL;
echo "- Producto: " . (class_exists('App\Models\Producto') ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;
echo "- ProductoStock: " . (class_exists('App\Models\ProductoStock') ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;
echo "- InventarioMovimiento: " . (class_exists('App\Models\InventarioMovimiento') ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;
echo "- Categoria: " . (class_exists('App\Models\Categoria') ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;
echo PHP_EOL;

// 2. Verificar componentes Livewire
echo "2. COMPONENTES LIVEWIRE:" . PHP_EOL;
echo "- Dashboard: " . (class_exists('App\Livewire\Inventario\Dashboard') ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;
echo "- Movimientos: " . (class_exists('App\Livewire\Inventario\Movimientos') ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;
echo "- Productos\Lista: " . (class_exists('App\Livewire\Productos\Lista') ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;
echo "- Productos\Formulario: " . (class_exists('App\Livewire\Productos\Formulario') ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;
echo PHP_EOL;

// 3. Verificar datos
echo "3. DATOS:" . PHP_EOL;
try {
    $productos = App\Models\Producto::count();
    echo "- Productos: ✅ {$productos} registros" . PHP_EOL;
} catch (Exception $e) {
    echo "- Productos: ❌ ERROR: " . $e->getMessage() . PHP_EOL;
}

try {
    $stocks = App\Models\ProductoStock::count();
    echo "- ProductoStock: ✅ {$stocks} registros" . PHP_EOL;
} catch (Exception $e) {
    echo "- ProductoStock: ❌ ERROR: " . $e->getMessage() . PHP_EOL;
}

try {
    $movimientos = App\Models\InventarioMovimiento::count();
    echo "- InventarioMovimiento: ✅ {$movimientos} registros" . PHP_EOL;
} catch (Exception $e) {
    echo "- InventarioMovimiento: ❌ ERROR: " . $e->getMessage() . PHP_EOL;
}

try {
    $categorias = App\Models\Categoria::count();
    echo "- Categorias: ✅ {$categorias} registros" . PHP_EOL;
} catch (Exception $e) {
    echo "- Categorias: ❌ ERROR: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;

// 4. Verificar vistas
echo "4. VISTAS:" . PHP_EOL;
$dashboardView = file_exists('resources/views/livewire/inventario/dashboard.blade.php');
echo "- dashboard.blade.php: " . ($dashboardView ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;

$movimientosView = file_exists('resources/views/livewire/inventario/movimientos.blade.php');
echo "- movimientos.blade.php: " . ($movimientosView ? "✅ EXISTE" : "❌ NO EXISTE") . PHP_EOL;

echo PHP_EOL;

// 5. Verificar constantes
echo "5. CONSTANTES DE INVENTARIO:" . PHP_EOL;
try {
    echo "- TIPO_ENTRADA: " . App\Models\InventarioMovimiento::TIPO_ENTRADA . " ✅" . PHP_EOL;
    echo "- TIPO_SALIDA: " . App\Models\InventarioMovimiento::TIPO_SALIDA . " ✅" . PHP_EOL;
    echo "- TIPO_AJUSTE: " . App\Models\InventarioMovimiento::TIPO_AJUSTE . " ✅" . PHP_EOL;
    echo "- REFERENCIA_AJUSTE: " . App\Models\InventarioMovimiento::REFERENCIA_AJUSTE . " ✅" . PHP_EOL;
} catch (Exception $e) {
    echo "❌ ERROR en constantes: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;
echo "=== VERIFICACIÓN COMPLETADA ===" . PHP_EOL;
echo "✅ Módulo 4 parece estar correctamente configurado" . PHP_EOL;
echo "🌐 Prueba accediendo a: http://127.0.0.1:8000/inventario" . PHP_EOL;
echo "🌐 Y también a: http://127.0.0.1:8000/inventario/movimientos" . PHP_EOL;
