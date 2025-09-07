<?php

use Illuminate\Database\Seeder;
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

class DashboardDemoSeeder extends Seeder
{
    public function run()
    {
        echo "🚀 Creando datos de demostración para el dashboard...\n\n";

        // Obtener empresas existentes
        $empresa1 = DB::table('empresas')->where('id', 1)->first();
        $empresa2 = DB::table('empresas')->where('id', 2)->first();

        if (!$empresa1 || !$empresa2) {
            echo "❌ No se encontraron las empresas. Ejecuta primero DatabaseSeeder.\n";
            return;
        }

        // Crear categorías
        echo "📂 Creando categorías...\n";
        $categorias = [
            ['nombre' => 'Bebidas', 'empresa_id' => 1, 'activa' => 1],
            ['nombre' => 'Snacks', 'empresa_id' => 1, 'activa' => 1],
            ['nombre' => 'Lácteos', 'empresa_id' => 1, 'activa' => 1],
            ['nombre' => 'Panadería', 'empresa_id' => 1, 'activa' => 1],
            ['nombre' => 'Limpieza', 'empresa_id' => 1, 'activa' => 1],
            
            ['nombre' => 'Medicamentos', 'empresa_id' => 2, 'activa' => 1],
            ['nombre' => 'Vitaminas', 'empresa_id' => 2, 'activa' => 1],
            ['nombre' => 'Cuidado Personal', 'empresa_id' => 2, 'activa' => 1],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }

        // Crear métodos de pago
        echo "💳 Creando métodos de pago...\n";
        $metodosPago = [
            ['nombre' => 'Efectivo', 'empresa_id' => 1, 'activo' => 1, 'requiere_referencia' => 0],
            ['nombre' => 'Tarjeta Débito', 'empresa_id' => 1, 'activo' => 1, 'requiere_referencia' => 1],
            ['nombre' => 'Tarjeta Crédito', 'empresa_id' => 1, 'activo' => 1, 'requiere_referencia' => 1],
            ['nombre' => 'Transferencia', 'empresa_id' => 1, 'activo' => 1, 'requiere_referencia' => 1],
            
            ['nombre' => 'Efectivo', 'empresa_id' => 2, 'activo' => 1, 'requiere_referencia' => 0],
            ['nombre' => 'Tarjeta Débito', 'empresa_id' => 2, 'activo' => 1, 'requiere_referencia' => 1],
            ['nombre' => 'Transferencia', 'empresa_id' => 2, 'activo' => 1, 'requiere_referencia' => 1],
        ];

        foreach ($metodosPago as $metodo) {
            MetodoPago::create($metodo);
        }

        // Crear productos para empresa 1 (Don J)
        echo "📦 Creando productos para Don J...\n";
        $productos1 = [
            ['nombre' => 'Coca Cola 500ml', 'categoria_id' => 1, 'precio_venta' => 2.50, 'stock_minimo' => 10],
            ['nombre' => 'Pepsi 500ml', 'categoria_id' => 1, 'precio_venta' => 2.30, 'stock_minimo' => 10],
            ['nombre' => 'Agua San Luis 625ml', 'categoria_id' => 1, 'precio_venta' => 1.00, 'stock_minimo' => 20],
            ['nombre' => 'Doritos Nacho', 'categoria_id' => 2, 'precio_venta' => 3.50, 'stock_minimo' => 5],
            ['nombre' => 'Papas Lays Clásicas', 'categoria_id' => 2, 'precio_venta' => 2.80, 'stock_minimo' => 8],
            ['nombre' => 'Leche Gloria 1L', 'categoria_id' => 3, 'precio_venta' => 4.20, 'stock_minimo' => 6],
            ['nombre' => 'Yogurt Griego 150g', 'categoria_id' => 3, 'precio_venta' => 3.80, 'stock_minimo' => 12],
            ['nombre' => 'Pan Integral', 'categoria_id' => 4, 'precio_venta' => 3.00, 'stock_minimo' => 15],
            ['nombre' => 'Croissants x6', 'categoria_id' => 4, 'precio_venta' => 8.50, 'stock_minimo' => 4],
            ['nombre' => 'Detergente Ace 1kg', 'categoria_id' => 5, 'precio_venta' => 12.90, 'stock_minimo' => 3],
        ];

        $productosCreados1 = [];
        foreach ($productos1 as $prod) {
            $producto = Producto::create([
                'empresa_id' => 1,
                'categoria_id' => $prod['categoria_id'],
                'nombre' => $prod['nombre'],
                'codigo_barras' => 'PROD' . str_pad(rand(100000, 999999), 6, '0'),
                'precio_compra' => $prod['precio_venta'] * 0.65, // 35% margen
                'precio_venta' => $prod['precio_venta'],
                'stock_minimo' => $prod['stock_minimo'],
                'unidad_medida' => 'unidad',
                'activo' => 1,
            ]);
            $productosCreados1[] = $producto;

            // Crear lote y stock inicial
            $lote = Lote::create([
                'empresa_id' => 1,
                'producto_id' => $producto->id,
                'codigo_lote' => 'LT' . date('Y') . str_pad($producto->id, 4, '0'),
                'cantidad_inicial' => rand(20, 100),
                'cantidad_actual' => rand(15, 95),
                'precio_compra_unitario' => $producto->precio_compra,
                'fecha_vencimiento' => Carbon::now()->addMonths(rand(6, 24)),
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

        // Crear productos para empresa 2 (Esperanza)
        echo "💊 Creando productos para Farmacia Esperanza...\n";
        $productos2 = [
            ['nombre' => 'Paracetamol 500mg x24', 'categoria_id' => 6, 'precio_venta' => 8.50, 'stock_minimo' => 10],
            ['nombre' => 'Ibuprofeno 400mg x20', 'categoria_id' => 6, 'precio_venta' => 12.30, 'stock_minimo' => 8],
            ['nombre' => 'Vitamina C 1000mg x30', 'categoria_id' => 7, 'precio_venta' => 25.90, 'stock_minimo' => 5],
            ['nombre' => 'Omega 3 x60 cápsulas', 'categoria_id' => 7, 'precio_venta' => 45.00, 'stock_minimo' => 4],
            ['nombre' => 'Shampoo Pantene 400ml', 'categoria_id' => 8, 'precio_venta' => 18.90, 'stock_minimo' => 6],
            ['nombre' => 'Crema Dental Colgate', 'categoria_id' => 8, 'precio_venta' => 7.50, 'stock_minimo' => 12],
        ];

        $productosCreados2 = [];
        foreach ($productos2 as $prod) {
            $producto = Producto::create([
                'empresa_id' => 2,
                'categoria_id' => $prod['categoria_id'],
                'nombre' => $prod['nombre'],
                'codigo_barras' => 'MED' . str_pad(rand(100000, 999999), 6, '0'),
                'precio_compra' => $prod['precio_venta'] * 0.60, // 40% margen farmacia
                'precio_venta' => $prod['precio_venta'],
                'stock_minimo' => $prod['stock_minimo'],
                'unidad_medida' => 'unidad',
                'activo' => 1,
            ]);
            $productosCreados2[] = $producto;

            // Crear lote y stock
            $lote = Lote::create([
                'empresa_id' => 2,
                'producto_id' => $producto->id,
                'codigo_lote' => 'FM' . date('Y') . str_pad($producto->id, 4, '0'),
                'cantidad_inicial' => rand(15, 50),
                'cantidad_actual' => rand(10, 45),
                'precio_compra_unitario' => $producto->precio_compra,
                'fecha_vencimiento' => Carbon::now()->addMonths(rand(12, 36)),
                'activo' => 1,
            ]);

            ProductoStock::create([
                'empresa_id' => 2,
                'sucursal_id' => 2,
                'producto_id' => $producto->id,
                'lote_id' => $lote->id,
                'cantidad_actual' => $lote->cantidad_actual,
            ]);
        }

        // Crear clientes
        echo "👥 Creando clientes...\n";
        $clientes1 = [
            ['nombre' => 'María González', 'email' => 'maria.gonzalez@email.com', 'telefono' => '987654321'],
            ['nombre' => 'Carlos Mendoza', 'email' => 'carlos.mendoza@email.com', 'telefono' => '987654322'],
            ['nombre' => 'Ana Rodríguez', 'email' => 'ana.rodriguez@email.com', 'telefono' => '987654323'],
            ['nombre' => 'Luis Herrera', 'email' => 'luis.herrera@email.com', 'telefono' => '987654324'],
            ['nombre' => 'Cliente General', 'email' => 'general@donj.com', 'telefono' => '000000000'],
        ];

        $clientesCreados1 = [];
        foreach ($clientes1 as $cliente) {
            $clienteCreado = Cliente::create([
                'empresa_id' => 1,
                'nombre' => $cliente['nombre'],
                'email' => $cliente['email'],
                'telefono' => $cliente['telefono'],
                'direccion' => 'Lima, Perú',
                'activo' => 1,
            ]);
            $clientesCreados1[] = $clienteCreado;
        }

        $clientes2 = [
            ['nombre' => 'Patricia Silva', 'email' => 'patricia.silva@email.com', 'telefono' => '987654325'],
            ['nombre' => 'Roberto Castro', 'email' => 'roberto.castro@email.com', 'telefono' => '987654326'],
            ['nombre' => 'Elena Vargas', 'email' => 'elena.vargas@email.com', 'telefono' => '987654327'],
            ['nombre' => 'Cliente General', 'email' => 'general@esperanza.com', 'telefono' => '000000000'],
        ];

        $clientesCreados2 = [];
        foreach ($clientes2 as $cliente) {
            $clienteCreado = Cliente::create([
                'empresa_id' => 2,
                'nombre' => $cliente['nombre'],
                'email' => $cliente['email'],
                'telefono' => $cliente['telefono'],
                'direccion' => 'Lima, Perú',
                'activo' => 1,
            ]);
            $clientesCreados2[] = $clienteCreado;
        }

        // Crear ventas de ejemplo
        echo "💰 Creando ventas de ejemplo...\n";
        
        // Ventas para Don J (últimos 7 días)
        for ($i = 0; $i < 15; $i++) {
            $fechaVenta = Carbon::now()->subDays(rand(0, 7));
            $cliente = $clientesCreados1[array_rand($clientesCreados1)];
            
            $venta = Venta::create([
                'empresa_id' => 1,
                'sucursal_id' => 1,
                'cliente_id' => $cliente->id,
                'fecha_venta' => $fechaVenta,
                'subtotal' => 0, // Se calculará después
                'descuento' => 0,
                'total' => 0, // Se calculará después
                'estado' => 'completada',
                'numero_comprobante' => 'B001-' . str_pad($i + 1, 8, '0'),
                'tipo_comprobante' => 'boleta',
            ]);

            // Agregar productos aleatorios a la venta
            $numProductos = rand(1, 4);
            $subtotal = 0;
            
            for ($j = 0; $j < $numProductos; $j++) {
                $producto = $productosCreados1[array_rand($productosCreados1)];
                $cantidad = rand(1, 3);
                $precioUnitario = $producto->precio_venta;
                $subtotalDetalle = $cantidad * $precioUnitario;
                
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalDetalle,
                ]);
                
                $subtotal += $subtotalDetalle;
            }
            
            // Actualizar totales de la venta
            $venta->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);
            
            // Crear pago
            $metodoPago = MetodoPago::where('empresa_id', 1)->inRandomOrder()->first();
            VentaPago::create([
                'venta_id' => $venta->id,
                'metodo_pago_id' => $metodoPago->id,
                'monto' => $subtotal,
                'referencia' => $metodoPago->requiere_referencia ? 'REF' . rand(100000, 999999) : null,
            ]);
        }

        // Ventas para Farmacia Esperanza
        for ($i = 0; $i < 10; $i++) {
            $fechaVenta = Carbon::now()->subDays(rand(0, 7));
            $cliente = $clientesCreados2[array_rand($clientesCreados2)];
            
            $venta = Venta::create([
                'empresa_id' => 2,
                'sucursal_id' => 2,
                'cliente_id' => $cliente->id,
                'fecha_venta' => $fechaVenta,
                'subtotal' => 0,
                'descuento' => 0,
                'total' => 0,
                'estado' => 'completada',
                'numero_comprobante' => 'F001-' . str_pad($i + 1, 8, '0'),
                'tipo_comprobante' => 'factura',
            ]);

            $numProductos = rand(1, 3);
            $subtotal = 0;
            
            for ($j = 0; $j < $numProductos; $j++) {
                $producto = $productosCreados2[array_rand($productosCreados2)];
                $cantidad = rand(1, 2);
                $precioUnitario = $producto->precio_venta;
                $subtotalDetalle = $cantidad * $precioUnitario;
                
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalDetalle,
                ]);
                
                $subtotal += $subtotalDetalle;
            }
            
            $venta->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);
            
            $metodoPago = MetodoPago::where('empresa_id', 2)->inRandomOrder()->first();
            VentaPago::create([
                'venta_id' => $venta->id,
                'metodo_pago_id' => $metodoPago->id,
                'monto' => $subtotal,
                'referencia' => $metodoPago->requiere_referencia ? 'REF' . rand(100000, 999999) : null,
            ]);
        }

        // Crear algunos productos con stock bajo para alertas
        echo "⚠️ Configurando alertas de stock bajo...\n";
        
        // Reducir stock de algunos productos para generar alertas
        $productosStockBajo = [
            $productosCreados1[0], // Coca Cola
            $productosCreados1[3], // Doritos
            $productosCreados2[1], // Ibuprofeno
        ];

        foreach ($productosStockBajo as $producto) {
            // Actualizar el stock a un nivel bajo
            $stockBajo = rand(1, $producto->stock_minimo - 1);
            
            ProductoStock::where('producto_id', $producto->id)->update([
                'cantidad_actual' => $stockBajo
            ]);
            
            Lote::where('producto_id', $producto->id)->update([
                'cantidad_actual' => $stockBajo
            ]);
        }

        echo "\n✅ Datos de demostración creados exitosamente!\n";
        echo "📊 Resumen:\n";
        echo "   • Productos: " . (count($productos1) + count($productos2)) . "\n";
        echo "   • Clientes: " . (count($clientes1) + count($clientes2)) . "\n";
        echo "   • Ventas: 25\n";
        echo "   • Categorías: " . count($categorias) . "\n";
        echo "   • Métodos de pago: " . count($metodosPago) . "\n\n";
        echo "🔑 Ya puedes hacer login con:\n";
        echo "   • admin@donj.com (password123)\n";
        echo "   • admin@esperanza.com (password123)\n";
    }
}
