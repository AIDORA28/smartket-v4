<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Core\Empresa;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();
        
        if (!$empresa) {
            return;
        }

        $categorias = Categoria::where('empresa_id', $empresa->id)->pluck('id', 'nombre');
        
        if ($categorias->isEmpty()) {
            return;
        }

        $productos = [
            // Panadería
            [
                'categoria' => 'Panadería',
                'nombre' => 'Pan francés',
                'codigo_interno' => 'PAN-001',
                'precio_costo' => 0.80,
                'precio_venta' => 1.20,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 50,
                'stock_maximo' => 200,
            ],
            [
                'categoria' => 'Panadería',
                'nombre' => 'Pan integral',
                'codigo_interno' => 'PAN-002',
                'precio_costo' => 1.00,
                'precio_venta' => 1.50,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 30,
                'stock_maximo' => 100,
            ],
            [
                'categoria' => 'Panadería',
                'nombre' => 'Pan de molde',
                'codigo_interno' => 'PAN-003',
                'precio_costo' => 3.50,
                'precio_venta' => 5.00,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 20,
                'stock_maximo' => 80,
            ],
            
            // Bebidas
            [
                'categoria' => 'Bebidas',
                'nombre' => 'Coca Cola 500ml',
                'codigo_interno' => 'BEB-001',
                'codigo_barras' => '7894900011517',
                'precio_costo' => 2.50,
                'precio_venta' => 4.00,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 24,
                'stock_maximo' => 144,
            ],
            [
                'categoria' => 'Bebidas',
                'nombre' => 'Agua San Luis 625ml',
                'codigo_interno' => 'BEB-002',
                'precio_costo' => 1.20,
                'precio_venta' => 2.00,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 48,
                'stock_maximo' => 240,
            ],
            [
                'categoria' => 'Bebidas',
                'nombre' => 'Inca Kola 1.5L',
                'codigo_interno' => 'BEB-003',
                'precio_costo' => 4.20,
                'precio_venta' => 6.50,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 12,
                'stock_maximo' => 60,
            ],
            
            // Lácteos
            [
                'categoria' => 'Lácteos',
                'nombre' => 'Leche Gloria 1L',
                'codigo_interno' => 'LAC-001',
                'precio_costo' => 4.50,
                'precio_venta' => 6.00,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 24,
                'stock_maximo' => 120,
            ],
            [
                'categoria' => 'Lácteos',
                'nombre' => 'Yogurt Gloria 1L',
                'codigo_interno' => 'LAC-002',
                'precio_costo' => 6.50,
                'precio_venta' => 9.00,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 12,
                'stock_maximo' => 60,
            ],
            [
                'categoria' => 'Lácteos',
                'nombre' => 'Queso fresco',
                'codigo_interno' => 'LAC-003',
                'precio_costo' => 15.00,
                'precio_venta' => 20.00,
                'unidad_medida' => 'kg',
                'maneja_stock' => true,
                'stock_minimo' => 2,
                'stock_maximo' => 10,
            ],
            
            // Repostería
            [
                'categoria' => 'Repostería',
                'nombre' => 'Torta de chocolate',
                'codigo_interno' => 'REP-001',
                'precio_costo' => 25.00,
                'precio_venta' => 45.00,
                'unidad_medida' => 'unidad',
                'maneja_stock' => false,
            ],
            [
                'categoria' => 'Repostería',
                'nombre' => 'Cupcakes',
                'codigo_interno' => 'REP-002',
                'precio_costo' => 3.00,
                'precio_venta' => 5.50,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 12,
                'stock_maximo' => 48,
            ],
            
            // Dulces
            [
                'categoria' => 'Dulces',
                'nombre' => 'Chocolate Sublime',
                'codigo_interno' => 'DUL-001',
                'precio_costo' => 1.50,
                'precio_venta' => 2.50,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 50,
                'stock_maximo' => 200,
            ],
            [
                'categoria' => 'Dulces',
                'nombre' => 'Chicles Topline',
                'codigo_interno' => 'DUL-002',
                'precio_costo' => 0.20,
                'precio_venta' => 0.50,
                'unidad_medida' => 'unidad',
                'maneja_stock' => true,
                'stock_minimo' => 100,
                'stock_maximo' => 500,
            ],
        ];

        foreach ($productos as $productoData) {
            $categoriaId = $categorias->get($productoData['categoria']);
            
            if (!$categoriaId) {
                continue;
            }

            Producto::create([
                'empresa_id' => $empresa->id,
                'categoria_id' => $categoriaId,
                'nombre' => $productoData['nombre'],
                'codigo_interno' => $productoData['codigo_interno'],
                'codigo_barra' => $productoData['codigo_barras'] ?? null,
                'precio_costo' => $productoData['precio_costo'],
                'precio_venta' => $productoData['precio_venta'],
                'incluye_igv' => true,
                'unidad_medida' => $productoData['unidad_medida'],
                'permite_decimales' => in_array($productoData['unidad_medida'], ['kg', 'gr', 'lt', 'ml']),
                'maneja_stock' => $productoData['maneja_stock'],
                'stock_minimo' => $productoData['stock_minimo'] ?? 0,
                'stock_maximo' => $productoData['stock_maximo'] ?? 0,
                'activo' => true,
            ]);
        }

        // Actualizar contadores de productos en categorías
        foreach ($categorias as $nombre => $id) {
            $categoria = Categoria::find($id);
            if ($categoria) {
                $categoria->updateProductosCount();
            }
        }
    }
}

