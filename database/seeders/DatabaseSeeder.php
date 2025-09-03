<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Empresa;
use App\Models\Rubro;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear planes
        $planBasico = Plan::create([
            'nombre' => 'FREE_BASIC',
            'max_usuarios' => 3,
            'max_sucursales' => 1,
            'max_productos' => 100,
            'limites_json' => json_encode([
                'ventas_diarias' => 50,
                'storage_mb' => 100,
                'reportes_basicos' => true,
            ]),
            'activo' => 1,
            'grace_percent' => 10
        ]);

        $planAvanzado = Plan::create([
            'nombre' => 'STANDARD',
            'max_usuarios' => 10,
            'max_sucursales' => 5,
            'max_productos' => 1000,
            'limites_json' => json_encode([
                'ventas_diarias' => 500,
                'storage_mb' => 1000,
                'reportes_avanzados' => true,
                'facturacion_electronica' => true,
            ]),
            'activo' => 1,
            'grace_percent' => 10
        ]);

        // Crear rubros
        $rubroTienda = Rubro::create([
            'nombre' => 'minimarket',
            'modulos_default_json' => json_encode([
                'pos' => true,
                'inventario' => true,
                'proveedores' => true,
            ]),
            'activo' => 1
        ]);

        $rubroPanaderia = Rubro::create([
            'nombre' => 'panaderia',
            'modulos_default_json' => json_encode([
                'pos' => true,
                'inventario' => true,
                'produccion' => true,
            ]),
            'activo' => 1
        ]);

        // Crear empresa de prueba
        $empresa = Empresa::create([
            'nombre' => 'Tienda Don José',
            'ruc' => '20123456789',
            'tiene_ruc' => 1,
            'plan_id' => $planAvanzado->id,
            'features_json' => json_encode([
                'pos' => true,
                'inventario_avanzado' => true,
                'reportes' => true,
                'facturacion_electronica' => true,
            ]),
            'sucursales_enabled' => 1,
            'sucursales_count' => 1,
            'allow_negative_stock' => 0,
            'precio_incluye_igv' => 1,
            'timezone' => 'America/Lima'
        ]);

        // Asignar rubro a la empresa
        $empresa->rubros()->attach($rubroTienda->id, ['es_principal' => true]);

        // Crear sucursal principal
        $sucursal = Sucursal::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Sucursal Principal',
            'codigo_interno' => 'PRIN',
            'direccion' => 'Av. Los Alamos 123, San Isidro',
            'activa' => 1
        ]);

        // Crear usuario administrador
        $user = User::create([
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal->id,
            'name' => 'José Pérez',
            'email' => 'admin@donj.com',
            'nombre' => 'José Pérez',
            'password_hash' => Hash::make('password123'),
            'rol_principal' => 'owner',
            'activo' => 1,
            'email_verified_at' => now(),
        ]);

        // Crear feature flags para la empresa
        $features = [
            'pos' => true,
            'inventario_avanzado' => true,
            'reportes' => true,
            'facturacion_electronica' => true,
            'multi_sucursal' => true,
        ];

        foreach ($features as $feature => $enabled) {
            FeatureFlag::create([
                'empresa_id' => $empresa->id,
                'feature_key' => $feature,
                'enabled' => $enabled
            ]);
        }

        // Crear una segunda empresa para demostrar multi-tenancy
        $empresa2 = Empresa::create([
            'nombre' => 'Panadería La Esperanza',
            'ruc' => '20987654321',
            'tiene_ruc' => 1,
            'plan_id' => $planBasico->id,
            'features_json' => json_encode([
                'pos' => false,
                'inventario_avanzado' => false,
            ]),
            'sucursales_enabled' => 0,
            'sucursales_count' => 1,
            'allow_negative_stock' => 0,
            'precio_incluye_igv' => 1,
            'timezone' => 'America/Lima'
        ]);

        $empresa2->rubros()->attach($rubroPanaderia->id, ['es_principal' => true]);

        $sucursal2 = Sucursal::create([
            'empresa_id' => $empresa2->id,
            'nombre' => 'Sucursal Principal',
            'codigo_interno' => 'PRIN',
            'direccion' => 'Jr. Lima 456, Miraflores',
            'activa' => 1
        ]);

        User::create([
            'empresa_id' => $empresa2->id,
            'sucursal_id' => $sucursal2->id,
            'name' => 'María García',
            'email' => 'admin@esperanza.com',
            'nombre' => 'María García',
            'password_hash' => Hash::make('password123'),
            'rol_principal' => 'owner',
            'activo' => 1,
            'email_verified_at' => now(),
        ]);

        // Features para empresa 2 (plan básico)
        $featuresBasico = [
            'pos' => false, // No disponible en plan básico
            'inventario_avanzado' => false,
            'reportes' => false,
            'facturacion_electronica' => false,
            'multi_sucursal' => false,
        ];

        foreach ($featuresBasico as $feature => $enabled) {
            FeatureFlag::create([
                'empresa_id' => $empresa2->id,
                'feature_key' => $feature,
                'enabled' => $enabled
            ]);
        }

        $this->command->info('Datos de prueba creados exitosamente!');
        $this->command->info('Usuarios de prueba:');
        $this->command->info('- admin@donj.com / password123 (Plan STANDARD)');
        $this->command->info('- admin@esperanza.com / password123 (Plan FREE_BASIC)');

        // Ejecutar seeders de Módulo 2
        $this->call([
            CategoriaSeeder::class,
            ProductoSeeder::class,
        ]);
    }
}
