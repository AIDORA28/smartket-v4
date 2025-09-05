<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Categoria;

// Verificar usuario
$user = User::where('email', 'admin@donj.com')->first();
if (!$user) {
    echo "❌ Usuario no encontrado\n";
    exit;
}

echo "✅ Usuario encontrado: " . $user->name . "\n";
echo "🏪 Empresa: " . $user->empresa->nombre . "\n\n";

// Verificar productos y categorías
$empresa = $user->empresa;
$productos = Producto::where('empresa_id', $empresa->id)->with(['categoria'])->get();
$categorias = Categoria::where('empresa_id', $empresa->id)->where('activa', true)->get();

echo "📦 Total productos: " . $productos->count() . "\n";
echo "📂 Total categorías: " . $categorias->count() . "\n\n";

// Mostrar categorías
echo "=== CATEGORÍAS DISPONIBLES ===\n";
foreach ($categorias as $categoria) {
    $productosEnCategoria = $productos->where('categoria_id', $categoria->id)->count();
    echo "📂 {$categoria->nombre} ({$productosEnCategoria} productos)\n";
}

echo "\n=== PRODUCTOS DISPONIBLES ===\n";
foreach ($productos->take(5) as $producto) {
    $categoria = $producto->categoria ? $producto->categoria->nombre : 'Sin categoría';
    $stock = $producto->getStockTotal();
    echo "📦 {$producto->nombre} - S/{$producto->precio_venta} - Stock: {$stock} - Categoría: {$categoria}\n";
}

if ($productos->count() > 5) {
    echo "... y " . ($productos->count() - 5) . " productos más\n";
}

echo "\n=== TEST DE FILTRADO ===\n";

// Probar búsqueda por nombre
$busqueda = 'leche';
$productosFiltrados = $productos->filter(function($producto) use ($busqueda) {
    return stripos($producto->nombre, $busqueda) !== false || 
           stripos($producto->codigo_interno, $busqueda) !== false ||
           stripos($producto->codigo_barra, $busqueda) !== false;
});
echo "🔍 Búsqueda '{$busqueda}': " . $productosFiltrados->count() . " resultados\n";

// Probar filtrado por categoría
if ($categorias->count() > 0) {
    $primeraCategoria = $categorias->first();
    $productosPorCategoria = $productos->where('categoria_id', $primeraCategoria->id);
    echo "📂 Filtro '{$primeraCategoria->nombre}': " . $productosPorCategoria->count() . " productos\n";
}

echo "\n✅ Sistema listo para usar!\n";
echo "👉 Inicia sesión con: admin@donj.com / password123\n";
echo "🌐 URL: http://127.0.0.1:8000/login\n";
