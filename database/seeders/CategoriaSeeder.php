<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Empresa;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();
        
        if (!$empresa) {
            return;
        }

        $categorias = [
            [
                'nombre' => 'Panadería',
                'icono' => '🍞',
                'color' => '#F59E0B',
                'activa' => true,
            ],
            [
                'nombre' => 'Bebidas',
                'icono' => '🥤',
                'color' => '#3B82F6',
                'activa' => true,
            ],
            [
                'nombre' => 'Lácteos',
                'icono' => '🥛',
                'color' => '#10B981',
                'activa' => true,
            ],
            [
                'nombre' => 'Repostería',
                'icono' => '🍰',
                'color' => '#EC4899',
                'activa' => true,
            ],
            [
                'nombre' => 'Embutidos',
                'icono' => '🥓',
                'color' => '#EF4444',
                'activa' => true,
            ],
            [
                'nombre' => 'Dulces',
                'icono' => '🍫',
                'color' => '#8B5CF6',
                'activa' => true,
            ],
            [
                'nombre' => 'Limpieza',
                'icono' => '🧽',
                'color' => '#6B7280',
                'activa' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create([
                'empresa_id' => $empresa->id,
                'nombre' => $categoria['nombre'],
                'icono' => $categoria['icono'],
                'color' => $categoria['color'],
                'activa' => $categoria['activa'],
            ]);
        }
    }
}
