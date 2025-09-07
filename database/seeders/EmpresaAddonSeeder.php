<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmpresaAddon;
use App\Models\Empresa;
use App\Models\PlanAddon;

class EmpresaAddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar add-ons de empresas existentes
        EmpresaAddon::truncate();

        // Obtener empresas y add-ons existentes
        $empresas = Empresa::all();
        $addons = PlanAddon::all();

        if ($empresas->isEmpty() || $addons->isEmpty()) {
            $this->command->warn('No hay empresas o add-ons disponibles. Ejecuta primero los seeders básicos.');
            return;
        }

        // Crear algunos add-ons de ejemplo para demostrar el sistema
        foreach ($empresas as $empresa) {
            $plan = $empresa->plan;
            
            // Solo agregar add-ons si el plan es compatible
            if (in_array($plan->nombre, ['BÁSICO', 'PROFESIONAL', 'EMPRESARIAL'])) {
                
                // Agregar usuario adicional (Pack 5) para algunas empresas
                if (rand(1, 100) <= 60) { // 60% probabilidad
                    $addonUsuario = $addons->where('tipo', 'usuario')->where('cantidad', 5)->first();
                    if ($addonUsuario) {
                        $fechaInicio = now()->subDays(rand(1, 30));
                        $periodo = rand(1, 2) === 1 ? 'mensual' : 'anual';
                        $fechaFin = $periodo === 'anual' 
                            ? $fechaInicio->copy()->addYear()
                            : $fechaInicio->copy()->addMonth();

                        EmpresaAddon::create([
                            'empresa_id' => $empresa->id,
                            'plan_addon_id' => $addonUsuario->id,
                            'cantidad' => $addonUsuario->cantidad,
                            'fecha_inicio' => $fechaInicio,
                            'fecha_fin' => $fechaFin,
                            'precio_pagado' => $addonUsuario->getPrecio($periodo),
                            'periodo' => $periodo,
                            'activo' => true,
                            'configuracion_json' => [
                                'comprado_automaticamente' => true,
                                'seeder_generated' => true,
                            ],
                        ]);
                    }
                }

                // Agregar sucursal adicional para empresas PROFESIONAL y EMPRESARIAL
                if (in_array($plan->nombre, ['PROFESIONAL', 'EMPRESARIAL']) && rand(1, 100) <= 40) {
                    $addonSucursal = $addons->where('tipo', 'sucursal')->where('cantidad', 1)->first();
                    if ($addonSucursal) {
                        $fechaInicio = now()->subDays(rand(1, 60));
                        $periodo = rand(1, 3) === 1 ? 'mensual' : 'anual'; // Más probabilidad anual
                        $fechaFin = $periodo === 'anual' 
                            ? $fechaInicio->copy()->addYear()
                            : $fechaInicio->copy()->addMonth();

                        EmpresaAddon::create([
                            'empresa_id' => $empresa->id,
                            'plan_addon_id' => $addonSucursal->id,
                            'cantidad' => $addonSucursal->cantidad,
                            'fecha_inicio' => $fechaInicio,
                            'fecha_fin' => $fechaFin,
                            'precio_pagado' => $addonSucursal->getPrecio($periodo),
                            'periodo' => $periodo,
                            'activo' => true,
                            'configuracion_json' => [
                                'comprado_automaticamente' => true,
                                'seeder_generated' => true,
                            ],
                        ]);
                    }
                }

                // Agregar rubro adicional para la mayoría de empresas
                if (rand(1, 100) <= 70) { // 70% probabilidad
                    $addonRubro = $addons->where('tipo', 'rubro')->where('cantidad', 1)->first();
                    if ($addonRubro) {
                        $fechaInicio = now()->subDays(rand(1, 90));
                        $periodo = 'anual'; // Los rubros adicionales generalmente se compran anuales
                        $fechaFin = $fechaInicio->copy()->addYear();

                        EmpresaAddon::create([
                            'empresa_id' => $empresa->id,
                            'plan_addon_id' => $addonRubro->id,
                            'cantidad' => $addonRubro->cantidad,
                            'fecha_inicio' => $fechaInicio,
                            'fecha_fin' => $fechaFin,
                            'precio_pagado' => $addonRubro->getPrecio($periodo),
                            'periodo' => $periodo,
                            'activo' => true,
                            'configuracion_json' => [
                                'comprado_automaticamente' => true,
                                'seeder_generated' => true,
                            ],
                        ]);
                    }
                }

                // Para empresas EMPRESARIAL, agregar pack de rubros adicionales
                if ($plan->nombre === 'EMPRESARIAL' && rand(1, 100) <= 30) {
                    $addonRubroPack = $addons->where('tipo', 'rubro')->where('cantidad', 3)->first();
                    if ($addonRubroPack) {
                        $fechaInicio = now()->subDays(rand(1, 120));
                        $fechaFin = $fechaInicio->copy()->addYear();

                        EmpresaAddon::create([
                            'empresa_id' => $empresa->id,
                            'plan_addon_id' => $addonRubroPack->id,
                            'cantidad' => $addonRubroPack->cantidad,
                            'fecha_inicio' => $fechaInicio,
                            'fecha_fin' => $fechaFin,
                            'precio_pagado' => $addonRubroPack->getPrecio('anual'),
                            'periodo' => 'anual',
                            'activo' => true,
                            'configuracion_json' => [
                                'comprado_automaticamente' => true,
                                'seeder_generated' => true,
                                'pack_empresarial' => true,
                            ],
                        ]);
                    }
                }
            }
        }

        $this->command->info('Add-ons de empresas creados exitosamente');
        
        // Mostrar estadísticas
        $totalAddons = EmpresaAddon::count();
        $addonsPorTipo = EmpresaAddon::join('plan_addons', 'empresa_addons.plan_addon_id', '=', 'plan_addons.id')
            ->selectRaw('plan_addons.tipo, COUNT(*) as total')
            ->groupBy('plan_addons.tipo')
            ->get();

        $this->command->info("Total de add-ons creados: {$totalAddons}");
        foreach ($addonsPorTipo as $estadistica) {
            $this->command->info("- {$estadistica->tipo}: {$estadistica->total}");
        }
    }
}
