<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Core\FeatureFlag;
use App\Models\Core\Empresa;

class ConfiguracionSeeder extends Seeder
{
    public function run()
    {
        // Obtener la primera empresa
        $empresa = Empresa::first();
        
        if (!$empresa) {
            return;
        }
        
        // Crear algunos feature flags de ejemplo
        $features = [
            ['feature_key' => 'multi_sucursal', 'name' => 'Multi-sucursal', 'enabled' => false],
            ['feature_key' => 'lotes_vencimientos', 'name' => 'Lotes y Vencimientos', 'enabled' => true],
            ['feature_key' => 'sistema_caja', 'name' => 'Sistema de Caja', 'enabled' => true],
            ['feature_key' => 'facturacion_sunat', 'name' => 'Facturación SUNAT', 'enabled' => false],
            ['feature_key' => 'reportes_avanzados', 'name' => 'Reportes Avanzados', 'enabled' => true],
        ];
        
        foreach ($features as $feature) {
            FeatureFlag::updateOrCreate(
                [
                    'feature_key' => $feature['feature_key'],
                    'empresa_id' => $empresa->id
                ],
                [
                    'enabled' => $feature['enabled']
                ]
            );
        }
        
        $this->command->info('Feature flags creados para configuraciones.');
    }
}

