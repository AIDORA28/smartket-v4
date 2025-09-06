<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Panadería', 'descripcion' => 'Pan fresco, bollos y productos de panadería', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Pastelería', 'descripcion' => 'Pasteles, tartas y postres', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Bebidas', 'descripcion' => 'Jugos, refrescos y bebidas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Dulces', 'descripcion' => 'Chocolates, caramelos y dulces', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Lácteos', 'descripcion' => 'Leche, queso y productos lácteos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Snacks', 'descripcion' => 'Papas fritas, galletas y bocadillos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Otros', 'descripcion' => 'Productos diversos', 'empresa_id' => 1, 'activa' => true],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(
                ['nombre' => $categoria['nombre'], 'empresa_id' => $categoria['empresa_id']],
                $categoria
            );
        }
    }
}
