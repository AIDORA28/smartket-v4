<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar planes existentes
        Plan::truncate();

        $planes = [
            [
                'nombre' => 'FREE',
                'descripcion' => 'Plan gratuito perfecto para probar SmartKet',
                'precio_mensual' => 0.00,
                'precio_anual' => 0.00,
                'dias_prueba' => 0,
                'max_usuarios' => 1,
                'max_sucursales' => 1,
                'max_productos' => 50,
                'max_rubros' => 1,
                'caracteristicas_json' => [
                    'Hasta 2 usuarios',
                    '1 sucursal',
                    'Hasta 100 productos',
                    '1 rubro',
                    'Reportes básicos',
                    'Soporte por email'
                ],
                'limites_json' => [
                    'reportes_avanzados' => false,
                    'integraciones' => false,
                    'api_acceso' => false
                ],
                'es_gratis' => true,
                'es_visible' => true,
                'orden_display' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'BÁSICO',
                'descripcion' => 'Para pequeños negocios que necesitan más funcionalidades',
                'precio_mensual' => 29.00,
                'precio_anual' => 290.00, // 2 meses gratis
                'dias_prueba' => 15,
                'max_usuarios' => 3,
                'max_sucursales' => 1,
                'max_productos' => 500,
                'max_rubros' => 1,
                'caracteristicas_json' => [
                    'Hasta 5 usuarios',
                    'Hasta 2 sucursales',
                    'Hasta 1000 productos',
                    'Hasta 3 rubros',
                    'Reportes básicos y avanzados',
                    'Control de inventario',
                    'Gestión de clientes',
                    'Soporte por email y chat'
                ],
                'limites_json' => [
                    'reportes_avanzados' => true,
                    'integraciones' => false,
                    'api_acceso' => false
                ],
                'es_gratis' => false,
                'es_visible' => true,
                'orden_display' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'PROFESIONAL',
                'descripcion' => 'Para empresas medianas con múltiples necesidades',
                'precio_mensual' => 59.00,
                'precio_anual' => 590.00, // 2 meses gratis
                'dias_prueba' => 30,
                'max_usuarios' => 10,
                'max_sucursales' => 1,
                'max_productos' => 2000,
                'max_rubros' => 1,
                'caracteristicas_json' => [
                    'Hasta 15 usuarios',
                    'Hasta 5 sucursales',
                    'Hasta 5000 productos',
                    'Hasta 10 rubros',
                    'Todos los reportes',
                    'Integraciones básicas',
                    'API de consulta',
                    'Control avanzado de inventario',
                    'Gestión de proveedores',
                    'Soporte prioritario'
                ],
                'limites_json' => [
                    'reportes_avanzados' => true,
                    'integraciones' => true,
                    'api_acceso' => 'read',
                    'multi_moneda' => true
                ],
                'es_gratis' => false,
                'es_visible' => true,
                'orden_display' => 3,
                'activo' => true,
            ],
            [
                'nombre' => 'EMPRESARIAL',
                'descripcion' => 'Para grandes empresas con necesidades avanzadas',
                'precio_mensual' => 99.00,
                'precio_anual' => 990.00, // 2 meses gratis
                'dias_prueba' => 30,
                'max_usuarios' => 50,
                'max_sucursales' => 1,
                'max_productos' => 10000,
                'max_rubros' => 1,
                'caracteristicas_json' => [
                    'Hasta 50 usuarios',
                    'Hasta 20 sucursales',
                    'Hasta 20,000 productos',
                    'Hasta 50 rubros',
                    'Todas las funcionalidades',
                    'Integraciones avanzadas',
                    'API completa',
                    'Dashboard ejecutivo',
                    'Análisis predictivo',
                    'Soporte dedicado',
                    'Capacitación incluida'
                ],
                'limites_json' => [
                    'reportes_avanzados' => true,
                    'integraciones' => true,
                    'api_acceso' => 'full',
                    'multi_moneda' => true,
                    'analytics_avanzado' => true,
                    'soporte_dedicado' => true
                ],
                'es_gratis' => false,
                'es_visible' => true,
                'orden_display' => 4,
                'activo' => true,
            ],
            [
                'nombre' => 'DEVELOPER',
                'descripcion' => 'Para desarrolladores y integradores',
                'precio_mensual' => 0.00,
                'precio_anual' => 0.00,
                'dias_prueba' => 365,
                'max_usuarios' => 100,
                'max_sucursales' => 1,
                'max_productos' => 100000,
                'max_rubros' => 1,
                'caracteristicas_json' => [
                    'Usuarios ilimitados*',
                    'Sucursales escalables con add-ons',
                    'Productos ilimitados*',
                    'Rubros escalables con add-ons',
                    'API completa sin límites',
                    'Webhooks',
                    'Sandbox de desarrollo',
                    'Documentación técnica completa',
                    'Soporte técnico especializado',
                    'SLA garantizado'
                ],
                'limites_json' => [
                    'reportes_avanzados' => true,
                    'integraciones' => true,
                    'api_acceso' => 'unlimited',
                    'multi_moneda' => true,
                    'analytics_avanzado' => true,
                    'soporte_dedicado' => true,
                    'webhooks' => true,
                    'sandbox' => true
                ],
                'es_gratis' => true,
                'es_visible' => false,
                'orden_display' => 5,
                'activo' => true,
            ]
        ];

        foreach ($planes as $plan) {
            Plan::create($plan);
        }

        $this->command->info('Planes creados exitosamente');
    }
}
