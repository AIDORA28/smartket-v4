<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Caja;

class CajaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa = Empresa::first();
        $sucursal = Sucursal::first();

        if (!$empresa || !$sucursal) {
            $this->command->error('No hay empresa o sucursal disponible');
            return;
        }

        $this->command->info("Creando cajas para: {$empresa->nombre} - {$sucursal->nombre}");

        $cajas = [
            [
                'nombre' => 'Caja Principal',
                'codigo' => 'CJ-001',
                'tipo' => 'principal'
            ],
            [
                'nombre' => 'Caja Secundaria',
                'codigo' => 'CJ-002', 
                'tipo' => 'secundaria'
            ]
        ];

        foreach ($cajas as $cajaData) {
            $caja = Caja::create([
                'empresa_id' => $empresa->id,
                'sucursal_id' => $sucursal->id,
                'nombre' => $cajaData['nombre'],
                'codigo' => $cajaData['codigo'],
                'tipo' => $cajaData['tipo'],
                'activa' => true
            ]);
            
            $this->command->info("✅ Caja creada: {$caja->nombre} (ID: {$caja->id})");
        }

        $this->command->info("Total cajas creadas: " . Caja::count());
    }
}
