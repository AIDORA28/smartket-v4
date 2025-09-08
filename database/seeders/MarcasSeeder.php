<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcasSeeder extends Seeder
{
    /**
     * Marcas más populares en el mercado peruano por categoría de negocio
     */
    public function run(): void
    {
        $marcas = [
            // === BEBIDAS ===
            ['nombre' => 'Coca-Cola', 'descripcion' => 'Bebidas gaseosas y no gaseosas', 'color' => '#FF0000', 'icono' => '🥤'],
            ['nombre' => 'Pepsi', 'descripcion' => 'Bebidas gaseosas', 'color' => '#004B93', 'icono' => '🥤'],
            ['nombre' => 'Inca Kola', 'descripcion' => 'Gaseosa dorada peruana', 'color' => '#FFD700', 'icono' => '🥤'],
            ['nombre' => 'Fanta', 'descripcion' => 'Bebidas de sabores', 'color' => '#FF8C00', 'icono' => '🍊'],
            ['nombre' => 'Sprite', 'descripcion' => 'Bebida de lima-limón', 'color' => '#00FF00', 'icono' => '🍋'],
            ['nombre' => 'Guaraná', 'descripcion' => 'Bebida energética natural', 'color' => '#8B4513', 'icono' => '🌿'],
            ['nombre' => 'Frugos', 'descripcion' => 'Néctares y jugos de fruta', 'color' => '#FF6347', 'icono' => '🧃'],
            ['nombre' => 'Pulp', 'descripcion' => 'Bebidas de fruta', 'color' => '#FF1493', 'icono' => '🧃'],
            ['nombre' => 'Cielo', 'descripcion' => 'Agua embotellada', 'color' => '#87CEEB', 'icono' => '💧'],
            ['nombre' => 'San Luis', 'descripcion' => 'Agua mineral', 'color' => '#4169E1', 'icono' => '💧'],

            // === SNACKS Y DULCES ===
            ['nombre' => 'Lay\'s', 'descripcion' => 'Papas fritas y snacks', 'color' => '#FFD700', 'icono' => '🍟'],
            ['nombre' => 'Pringles', 'descripcion' => 'Papas apiladas', 'color' => '#FF0000', 'icono' => '🍟'],
            ['nombre' => 'Sublime', 'descripcion' => 'Chocolate peruano icónico', 'color' => '#8B4513', 'icono' => '🍫'],
            ['nombre' => 'Doña Pepa', 'descripcion' => 'Turrones y dulces peruanos', 'color' => '#9932CC', 'icono' => '🍬'],
            ['nombre' => 'Tic Tac', 'descripcion' => 'Mentas y caramelos', 'color' => '#FF69B4', 'icono' => '🍬'],
            ['nombre' => 'Mentitas', 'descripcion' => 'Caramelos de menta', 'color' => '#00FF7F', 'icono' => '🍬'],

            // === PANADERÍA ===
            ['nombre' => 'Bimbo', 'descripcion' => 'Panes y productos de panadería', 'color' => '#FF4500', 'icono' => '🍞'],
            ['nombre' => 'Todinno', 'descripcion' => 'Productos de panadería', 'color' => '#DAA520', 'icono' => '🍞'],
            ['nombre' => 'Oroweat', 'descripcion' => 'Panes integrales', 'color' => '#8B4513', 'icono' => '🍞'],

            // === LÁCTEOS ===
            ['nombre' => 'Gloria', 'descripcion' => 'Lácteos y derivados', 'color' => '#FF0000', 'icono' => '🥛'],
            ['nombre' => 'Laive', 'descripcion' => 'Productos lácteos', 'color' => '#4169E1', 'icono' => '🥛'],
            ['nombre' => 'Bonlé', 'descripcion' => 'Yogures y lácteos', 'color' => '#32CD32', 'icono' => '🥛'],
            ['nombre' => 'Pura Vida', 'descripcion' => 'Yogures naturales', 'color' => '#98FB98', 'icono' => '🥛'],

            // === LIMPIEZA ===
            ['nombre' => 'Sapolio', 'descripcion' => 'Productos de limpieza peruanos', 'color' => '#1E90FF', 'icono' => '🧽'],
            ['nombre' => 'Ariel', 'descripcion' => 'Detergentes para ropa', 'color' => '#0066CC', 'icono' => '🧽'],
            ['nombre' => 'Ace', 'descripcion' => 'Detergentes y lejía', 'color' => '#FF6347', 'icono' => '🧽'],
            ['nombre' => 'Bolivar', 'descripcion' => 'Detergentes peruanos', 'color' => '#228B22', 'icono' => '🧽'],
            ['nombre' => 'Elite', 'descripcion' => 'Papel higiénico y servilletas', 'color' => '#FF1493', 'icono' => '🧻'],
            ['nombre' => 'Suave', 'descripcion' => 'Papel higiénico', 'color' => '#87CEEB', 'icono' => '🧻'],

            // === CUIDADO PERSONAL ===
            ['nombre' => 'Head & Shoulders', 'descripcion' => 'Champús anticaspa', 'color' => '#4169E1', 'icono' => '🧴'],
            ['nombre' => 'Pantene', 'descripcion' => 'Productos para el cabello', 'color' => '#FFD700', 'icono' => '🧴'],
            ['nombre' => 'Colgate', 'descripcion' => 'Productos de higiene bucal', 'color' => '#FF0000', 'icono' => '🦷'],
            ['nombre' => 'Dento', 'descripcion' => 'Pasta dental peruana', 'color' => '#00CED1', 'icono' => '🦷'],

            // === ABARROTES ===
            ['nombre' => 'Costeño', 'descripcion' => 'Mayonesa y salsas peruanas', 'color' => '#FFD700', 'icono' => '🥫'],
            ['nombre' => 'Alacena', 'descripcion' => 'Salsas y conservas', 'color' => '#FF6347', 'icono' => '🥫'],
            ['nombre' => 'Marco Polo', 'descripcion' => 'Fideos y pastas', 'color' => '#FF4500', 'icono' => '🍝'],
            ['nombre' => 'Don Vittorio', 'descripcion' => 'Fideos gourmet', 'color' => '#8B0000', 'icono' => '🍝'],
            ['nombre' => 'Nicolini', 'descripcion' => 'Aceites comestibles', 'color' => '#FFD700', 'icono' => '🫒'],
            ['nombre' => 'Primor', 'descripcion' => 'Aceites y mantecas', 'color' => '#FF8C00', 'icono' => '🫒'],

            // === CEREALES Y GRANOS ===
            ['nombre' => 'Ángel', 'descripcion' => 'Fideos y pastas', 'color' => '#FF69B4', 'icono' => '🌾'],
            ['nombre' => 'Paisana', 'descripcion' => 'Harinas y cereales', 'color' => '#8B4513', 'icono' => '🌾'],
            ['nombre' => 'Molitalia', 'descripcion' => 'Harinas y galletitas', 'color' => '#DAA520', 'icono' => '🌾'],

            // === MARCAS GENÉRICAS ===
            ['nombre' => 'Sin Marca', 'descripcion' => 'Productos sin marca específica', 'color' => '#808080', 'icono' => '📦'],
            ['nombre' => 'Marca Propia', 'descripcion' => 'Productos de marca propia del establecimiento', 'color' => '#4B0082', 'icono' => '🏪'],
        ];

        foreach ($marcas as $marca) {
            DB::table('marcas')->insert([
                'empresa_id' => 1, // Empresa por defecto - se puede cambiar
                'nombre' => $marca['nombre'],
                'codigo' => strtoupper(str_replace([' ', '&', '\''], ['_', 'AND', ''], $marca['nombre'])),
                'descripcion' => $marca['descripcion'],
                'color' => $marca['color'],
                'icono' => $marca['icono'],
                'activa' => 1,
                'productos_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
