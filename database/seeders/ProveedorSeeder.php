<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;
use App\Models\Empresa;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa = Empresa::first();
        
        if (!$empresa) {
            $this->command->error('No hay empresas en la base de datos. Ejecute primero el seeder de empresas.');
            return;
        }

        $proveedores = [
            [
                'empresa_id' => $empresa->id,
                'nombre' => 'Distribuidora San Miguel S.A.C.',
                'documento_tipo' => 'RUC',
                'documento_numero' => '20123456789',
                'telefono' => '01-234-5678',
                'email' => 'ventas@sanmiguel.com.pe',
                'direccion' => 'Av. Industrial 123, Lima',
                'contacto_json' => [
                    'contacto_principal' => 'Juan Pérez',
                    'cargo_contacto' => 'Ejecutivo de Ventas',
                    'telefono_movil' => '987-654-321',
                    'horario_atencion' => 'L-V 8:00-18:00'
                ]
            ],
            [
                'empresa_id' => $empresa->id,
                'nombre' => 'Molinos del Sur E.I.R.L.',
                'documento_tipo' => 'RUC',
                'documento_numero' => '20987654321',
                'telefono' => '01-876-5432',
                'email' => 'pedidos@molinosdelsur.pe',
                'direccion' => 'Jr. Los Molinos 456, Arequipa',
                'contacto_json' => [
                    'contacto_principal' => 'María García',
                    'cargo_contacto' => 'Gerente Comercial',
                    'telefono_movil' => '123-456-789',
                    'horario_atencion' => 'L-S 7:00-19:00'
                ]
            ],
            [
                'empresa_id' => $empresa->id,
                'nombre' => 'Insumos Panaderos Unidos',
                'documento_tipo' => 'RUC',
                'documento_numero' => '20555666777',
                'telefono' => '01-555-6667',
                'email' => 'info@insumospanaderos.com',
                'direccion' => 'Calle Las Flores 789, Cusco',
                'contacto_json' => [
                    'contacto_principal' => 'Carlos Rodríguez',
                    'cargo_contacto' => 'Representante de Ventas',
                    'telefono_movil' => '999-888-777',
                    'horario_atencion' => 'L-V 9:00-17:00'
                ]
            ],
            [
                'empresa_id' => $empresa->id,
                'nombre' => 'Proveedor Local',
                'documento_tipo' => 'DNI',
                'documento_numero' => '12345678',
                'telefono' => '987-123-456',
                'email' => 'local@proveedor.com',
                'direccion' => 'Av. Local 123',
                'contacto_json' => [
                    'contacto_principal' => 'Ana López',
                    'telefono_movil' => '987-123-456'
                ]
            ]
        ];

        foreach ($proveedores as $proveedorData) {
            Proveedor::create($proveedorData);
        }

        $this->command->info('✅ Seeder de Proveedores ejecutado exitosamente');
        $this->command->info('   - 4 proveedores creados para la empresa: ' . $empresa->nombre);
    }
}
