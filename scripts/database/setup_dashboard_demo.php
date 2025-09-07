<?php

require_once __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VentaPago;
use App\Models\ProductoStock;
use App\Models\Lote;
use App\Models\Categoria;
use App\Models\MetodoPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "🚀 Creando datos de demostración para el dashboard...\n\n";

// Solo crear algunos productos adicionales si no existen muchos
$productosExistentes = Producto::where('empresa_id', 1)->count();

if ($productosExistentes < 5) {
    echo "📦 Creando algunos productos de ejemplo...\n";
    
    // Obtener o crear categoría
    $categoria = Categoria::firstOrCreate(
        ['empresa_id' => 1, 'nombre' => 'General'],
        ['activa' => 1]
    );
    
    $productos = [
        ['nombre' => 'Coca Cola 500ml', 'precio' => 2.50],
        ['nombre' => 'Pan Integral', 'precio' => 3.00],
        ['nombre' => 'Leche Gloria 1L', 'precio' => 4.20],
    ];
    
    foreach ($productos as $prod) {
        $producto = Producto::firstOrCreate([
            'empresa_id' => 1,
            'nombre' => $prod['nombre']
        ], [
            'categoria_id' => $categoria->id,
            'codigo_barras' => 'PROD' . str_pad(rand(100000, 999999), 6, '0'),
            'precio_compra' => $prod['precio'] * 0.65,
            'precio_venta' => $prod['precio'],
            'stock_minimo' => 10,
            'unidad_medida' => 'unidad',
            'activo' => 1,
        ]);
        
        // Crear stock si no existe
        $stockExiste = ProductoStock::where('producto_id', $producto->id)->exists();
        if (!$stockExiste) {
            $lote = Lote::create([
                'empresa_id' => 1,
                'producto_id' => $producto->id,
                'codigo_lote' => 'LT' . date('Y') . str_pad($producto->id, 4, '0'),
                'cantidad_inicial' => rand(20, 50),
                'cantidad_actual' => rand(15, 45),
                'precio_compra_unitario' => $producto->precio_compra,
                'fecha_vencimiento' => Carbon::now()->addMonths(12),
                'activo' => 1,
            ]);

            ProductoStock::create([
                'empresa_id' => 1,
                'sucursal_id' => 1,
                'producto_id' => $producto->id,
                'lote_id' => $lote->id,
                'cantidad_actual' => $lote->cantidad_actual,
            ]);
        }
    }
}

// Crear algunos clientes si no existen muchos
$clientesExistentes = Cliente::where('empresa_id', 1)->count();
if ($clientesExistentes < 3) {
    echo "👥 Creando algunos clientes...\n";
    
    $clientes = [
        ['nombre' => 'Cliente General', 'email' => 'general@donj.com', 'doc' => '00000000'],
        ['nombre' => 'María González', 'email' => 'maria@email.com', 'doc' => '12345678'],
        ['nombre' => 'Carlos Mendoza', 'email' => 'carlos@email.com', 'doc' => '87654321'],
    ];
    
    foreach ($clientes as $cliente) {
        Cliente::firstOrCreate([
            'empresa_id' => 1,
            'email' => $cliente['email']
        ], [
            'nombre' => $cliente['nombre'],
            'numero_documento' => $cliente['doc'],
            'telefono' => '987654' . rand(100, 999),
            'direccion' => 'Lima, Perú',
            'activo' => 1,
        ]);
    }
}

// Crear método de pago si no existe
$metodoPago = MetodoPago::firstOrCreate([
    'empresa_id' => 1,
    'nombre' => 'Efectivo'
], [
    'codigo' => 'EFE',
    'tipo' => 'efectivo',
    'activo' => 1,
    'requiere_referencia' => 0,
    'afecta_caja' => 1,
    'comision_porcentaje' => 0,
    'comision_fija' => 0,
    'orden' => 1,
]);

// Crear algunas ventas de ejemplo si no hay muchas
$ventasExistentes = Venta::where('empresa_id', 1)->count();
if ($ventasExistentes < 10) {
    echo "💰 Creando algunas ventas de ejemplo...\n";
    
    $productos = Producto::where('empresa_id', 1)->take(3)->get();
    $clientes = Cliente::where('empresa_id', 1)->take(3)->get();
    
    if ($productos->count() > 0 && $clientes->count() > 0) {
        for ($i = 0; $i < 5; $i++) {
            $fechaVenta = Carbon::now()->subDays(rand(0, 7));
            $cliente = $clientes->random();
            $producto = $productos->random();
            
            $venta = Venta::create([
                'empresa_id' => 1,
                'sucursal_id' => 1,
                'usuario_id' => 1, // Usuario admin
                'cliente_id' => $cliente->id,
                'fecha_venta' => $fechaVenta,
                'subtotal' => $producto->precio_venta,
                'descuento' => 0,
                'total' => $producto->precio_venta,
                'estado' => 'completada',
                'numero_comprobante' => 'B001-' . str_pad($i + 1, 8, '0'),
                'tipo_comprobante' => 'boleta',
            ]);
            
            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => 1,
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $producto->precio_venta,
            ]);
            
            VentaPago::create([
                'venta_id' => $venta->id,
                'metodo_pago_id' => $metodoPago->id,
                'monto' => $producto->precio_venta,
            ]);
        }
    }
}

// Configurar un producto con stock bajo para alertas
$productoBajo = Producto::where('empresa_id', 1)->first();
if ($productoBajo) {
    echo "⚠️ Configurando alerta de stock bajo...\n";
    ProductoStock::where('producto_id', $productoBajo->id)->update([
        'cantidad_actual' => 2 // Por debajo del mínimo
    ]);
}

echo "\n✅ Datos de demostración configurados!\n";
echo "🔑 Puedes hacer login con: admin@donj.com (password123)\n";

// Mostrar resumen
$stats = [
    'productos' => Producto::where('empresa_id', 1)->count(),
    'clientes' => Cliente::where('empresa_id', 1)->count(),
    'ventas' => Venta::where('empresa_id', 1)->count(),
];

echo "\n📊 Resumen para empresa Don J:\n";
echo "   • Productos: {$stats['productos']}\n";
echo "   • Clientes: {$stats['clientes']}\n";
echo "   • Ventas: {$stats['ventas']}\n";
