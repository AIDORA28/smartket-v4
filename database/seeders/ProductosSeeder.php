<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\ProductoStock;

class ProductosSeeder extends Seeder
{
    public function run()
    {
        // Obtener la primera empresa
        $empresa = Empresa::first();
        
        if (!$empresa) {
            $this->command->error('No hay empresas en la base de datos.');
            return;
        }
        
        // Crear categorías si no existen
        $categorias = [
            ['nombre' => 'Panadería', 'descripcion' => 'Productos de panadería'],
            ['nombre' => 'Pastelería', 'descripcion' => 'Productos de pastelería'],
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes'],
            ['nombre' => 'Snacks', 'descripcion' => 'Snacks y aperitivos'],
            ['nombre' => 'Dulces', 'descripcion' => 'Dulces y golosinas'],
        ];
        
        $categoriasCreadas = [];
        foreach ($categorias as $categoriaData) {
            $categoria = Categoria::updateOrCreate(
                [
                    'nombre' => $categoriaData['nombre'],
                    'empresa_id' => $empresa->id
                ],
                [
                    'descripcion' => $categoriaData['descripcion'],
                    'activa' => true
                ]
            );
            $categoriasCreadas[] = $categoria;
        }
        
        // Crear productos de ejemplo
        $productos = [
            // Panadería
            [
                'nombre' => 'Pan Francés',
                'categoria' => 'Panadería',
                'codigo_interno' => 'PAN001',
                'codigo_barra' => '7501234567890',
                'precio_costo' => 0.50,
                'precio_venta' => 0.80,
                'stock_inicial' => 100,
            ],
            [
                'nombre' => 'Pan Integral',
                'categoria' => 'Panadería',
                'codigo_interno' => 'PAN002',
                'codigo_barra' => '7501234567891',
                'precio_costo' => 0.60,
                'precio_venta' => 1.00,
                'stock_inicial' => 80,
            ],
            [
                'nombre' => 'Croissant',
                'categoria' => 'Panadería',
                'codigo_interno' => 'PAN003',
                'codigo_barra' => '7501234567892',
                'precio_costo' => 1.20,
                'precio_venta' => 2.50,
                'stock_inicial' => 50,
            ],
            
            // Pastelería
            [
                'nombre' => 'Torta de Chocolate',
                'categoria' => 'Pastelería',
                'codigo_interno' => 'PAST001',
                'codigo_barra' => '7501234567893',
                'precio_costo' => 15.00,
                'precio_venta' => 35.00,
                'stock_inicial' => 5,
            ],
            [
                'nombre' => 'Cupcake de Vainilla',
                'categoria' => 'Pastelería',
                'codigo_interno' => 'PAST002',
                'codigo_barra' => '7501234567894',
                'precio_costo' => 2.00,
                'precio_venta' => 4.50,
                'stock_inicial' => 30,
            ],
            
            // Bebidas
            [
                'nombre' => 'Café Americano',
                'categoria' => 'Bebidas',
                'codigo_interno' => 'BEB001',
                'codigo_barra' => '7501234567895',
                'precio_costo' => 0.80,
                'precio_venta' => 3.00,
                'stock_inicial' => 200,
            ],
            [
                'nombre' => 'Jugo de Naranja',
                'categoria' => 'Bebidas',
                'codigo_interno' => 'BEB002',
                'codigo_barra' => '7501234567896',
                'precio_costo' => 1.50,
                'precio_venta' => 4.00,
                'stock_inicial' => 50,
            ],
            
            // Snacks
            [
                'nombre' => 'Empanada de Pollo',
                'categoria' => 'Snacks',
                'codigo_interno' => 'SNK001',
                'codigo_barra' => '7501234567897',
                'precio_costo' => 1.80,
                'precio_venta' => 3.50,
                'stock_inicial' => 40,
            ],
            [
                'nombre' => 'Sandwich Mixto',
                'categoria' => 'Snacks',
                'codigo_interno' => 'SNK002',
                'codigo_barra' => '7501234567898',
                'precio_costo' => 3.00,
                'precio_venta' => 6.50,
                'stock_inicial' => 25,
            ],
            
            // Dulces
            [
                'nombre' => 'Alfajor de Dulce de Leche',
                'categoria' => 'Dulces',
                'codigo_interno' => 'DUL001',
                'codigo_barra' => '7501234567899',
                'precio_costo' => 1.00,
                'precio_venta' => 2.50,
                'stock_inicial' => 60,
            ],
        ];
        
        foreach ($productos as $productoData) {
            // Encontrar la categoría
            $categoria = collect($categoriasCreadas)->firstWhere('nombre', $productoData['categoria']);
            
            if (!$categoria) continue;
            
            // Crear el producto
            $producto = Producto::updateOrCreate(
                [
                    'codigo_interno' => $productoData['codigo_interno'],
                    'empresa_id' => $empresa->id
                ],
                [
                    'categoria_id' => $categoria->id,
                    'nombre' => $productoData['nombre'],
                    'codigo_barra' => $productoData['codigo_barra'],
                    'precio_costo' => $productoData['precio_costo'],
                    'precio_venta' => $productoData['precio_venta'],
                    'margen_ganancia' => (($productoData['precio_venta'] - $productoData['precio_costo']) / $productoData['precio_costo']) * 100,
                    'incluye_igv' => true,
                    'unidad_medida' => 'UNIDAD',
                    'permite_decimales' => false,
                    'maneja_stock' => true,
                    'stock_minimo' => 5,
                    'stock_maximo' => 500,
                    'activo' => true,
                ]
            );
            
            // Crear stock inicial
            ProductoStock::updateOrCreate(
                [
                    'producto_id' => $producto->id,
                    'empresa_id' => $empresa->id
                ],
                [
                    'stock_actual' => $productoData['stock_inicial'],
                    'stock_reservado' => 0,
                    'stock_disponible' => $productoData['stock_inicial']
                ]
            );
        }
        
        $this->command->info('Productos creados exitosamente: ' . count($productos) . ' productos.');
    }
}
