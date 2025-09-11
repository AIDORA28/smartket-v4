<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Core\PlanAddon;

class PlanAddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar add-ons existentes
        PlanAddon::truncate();

        $addons = [
            // Add-ons de usuarios adicionales
            [
                'tipo' => 'usuario',
                'nombre' => 'Usuario Adicional (Pack 5)',
                'descripcion' => 'Añade 5 usuarios adicionales a tu plan',
                'precio_mensual' => 9.90,
                'precio_anual' => 99.00,
                'cantidad' => 5,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['BÁSICO', 'PROFESIONAL', 'EMPRESARIAL'],
                    'max_packs' => 10
                ],
            ],
            [
                'tipo' => 'usuario',
                'nombre' => 'Usuario Adicional (Pack 10)',
                'descripcion' => 'Añade 10 usuarios adicionales a tu plan',
                'precio_mensual' => 18.90,
                'precio_anual' => 189.00,
                'cantidad' => 10,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['PROFESIONAL', 'EMPRESARIAL'],
                    'max_packs' => 5
                ],
            ],
            
            // Add-ons de sucursales adicionales
            [
                'tipo' => 'sucursal',
                'nombre' => 'Sucursal Adicional',
                'descripcion' => 'Añade 1 sucursal adicional a tu plan',
                'precio_mensual' => 14.90,
                'precio_anual' => 149.00,
                'cantidad' => 1,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['BÁSICO', 'PROFESIONAL', 'EMPRESARIAL'],
                    'max_packs' => 20
                ],
            ],
            [
                'tipo' => 'sucursal',
                'nombre' => 'Sucursales Adicionales (Pack 3)',
                'descripcion' => 'Añade 3 sucursales adicionales a tu plan',
                'precio_mensual' => 39.90,
                'precio_anual' => 399.00,
                'cantidad' => 3,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['PROFESIONAL', 'EMPRESARIAL'],
                    'max_packs' => 10
                ],
            ],
            [
                'tipo' => 'sucursal',
                'nombre' => 'Sucursales Adicionales (Pack 5)',
                'descripcion' => 'Añade 5 sucursales adicionales a tu plan',
                'precio_mensual' => 59.90,
                'precio_anual' => 599.00,
                'cantidad' => 5,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['EMPRESARIAL'],
                    'max_packs' => 5
                ],
            ],

            // Add-ons de rubros adicionales
            [
                'tipo' => 'rubro',
                'nombre' => 'Rubro Adicional',
                'descripcion' => 'Añade 1 rubro adicional a tu plan',
                'precio_mensual' => 7.90,
                'precio_anual' => 79.00,
                'cantidad' => 1,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['FREE', 'BÁSICO', 'PROFESIONAL', 'EMPRESARIAL'],
                    'max_packs' => 50
                ],
            ],
            [
                'tipo' => 'rubro',
                'nombre' => 'Rubros Adicionales (Pack 3)',
                'descripcion' => 'Añade 3 rubros adicionales a tu plan',
                'precio_mensual' => 19.90,
                'precio_anual' => 199.00,
                'cantidad' => 3,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['BÁSICO', 'PROFESIONAL', 'EMPRESARIAL'],
                    'max_packs' => 20
                ],
            ],
            [
                'tipo' => 'rubro',
                'nombre' => 'Rubros Adicionales (Pack 5)',
                'descripcion' => 'Añade 5 rubros adicionales a tu plan',
                'precio_mensual' => 29.90,
                'precio_anual' => 299.00,
                'cantidad' => 5,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['PROFESIONAL', 'EMPRESARIAL'],
                    'max_packs' => 10
                ],
            ],
            [
                'tipo' => 'rubro',
                'nombre' => 'Rubros Adicionales (Pack 10)',
                'descripcion' => 'Añade 10 rubros adicionales a tu plan',
                'precio_mensual' => 49.90,
                'precio_anual' => 499.00,
                'cantidad' => 10,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['EMPRESARIAL'],
                    'max_packs' => 5
                ],
            ],
            
            // Add-ons GRATUITOS para DEVELOPER (testing)
            [
                'tipo' => 'usuario',
                'nombre' => 'Usuarios Ilimitados (DEVELOPER)',
                'descripcion' => 'Pack gratuito de usuarios para desarrollo y testing',
                'precio_mensual' => 0.00,
                'precio_anual' => 0.00,
                'cantidad' => 1000,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['DEVELOPER'],
                    'max_packs' => 1
                ],
            ],
            [
                'tipo' => 'sucursal',
                'nombre' => 'Sucursales de Testing (DEVELOPER)',
                'descripcion' => 'Pack gratuito de sucursales para desarrollo y testing',
                'precio_mensual' => 0.00,
                'precio_anual' => 0.00,
                'cantidad' => 100,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['DEVELOPER'],
                    'max_packs' => 1
                ],
            ],
            [
                'tipo' => 'rubro',
                'nombre' => 'Rubros de Testing (DEVELOPER)',
                'descripcion' => 'Pack gratuito de rubros para desarrollo y testing',
                'precio_mensual' => 0.00,
                'precio_anual' => 0.00,
                'cantidad' => 500,
                'activo' => true,
                'restricciones_json' => [
                    'planes_permitidos' => ['DEVELOPER'],
                    'max_packs' => 1
                ],
            ],
        ];

        foreach ($addons as $addon) {
            // Convertir restricciones_json a string JSON manualmente
            if (isset($addon['restricciones_json'])) {
                $addon['restricciones_json'] = json_encode($addon['restricciones_json']);
            }
            PlanAddon::create($addon);
        }

        $this->command->info('Add-ons creados exitosamente');
    }
}

