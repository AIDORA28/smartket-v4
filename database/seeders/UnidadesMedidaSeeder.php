<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadesMedidaSeeder extends Seeder
{
    /**
     * Unidades de medida más comunes en el mercado peruano
     */
    public function run(): void
    {
        $unidades = [
            // === UNIDADES DE PESO ===
            ['nombre' => 'Kilogramo', 'abreviacion' => 'Kg', 'tipo' => 'PESO', 'icono' => '⚖️'],
            ['nombre' => 'Gramo', 'abreviacion' => 'g', 'tipo' => 'PESO', 'icono' => '⚖️'],
            ['nombre' => 'Libra', 'abreviacion' => 'Lb', 'tipo' => 'PESO', 'icono' => '⚖️'],
            ['nombre' => 'Onza', 'abreviacion' => 'Oz', 'tipo' => 'PESO', 'icono' => '⚖️'],
            ['nombre' => 'Tonelada', 'abreviacion' => 'Tn', 'tipo' => 'PESO', 'icono' => '⚖️'],

            // === UNIDADES DE VOLUMEN ===
            ['nombre' => 'Litro', 'abreviacion' => 'L', 'tipo' => 'VOLUMEN', 'icono' => '🥤'],
            ['nombre' => 'Mililitro', 'abreviacion' => 'ml', 'tipo' => 'VOLUMEN', 'icono' => '🥤'],
            ['nombre' => 'Galón', 'abreviacion' => 'Gal', 'tipo' => 'VOLUMEN', 'icono' => '🥤'],
            ['nombre' => 'Copa', 'abreviacion' => 'Copa', 'tipo' => 'VOLUMEN', 'icono' => '🍷'],

            // === UNIDADES DE LONGITUD ===
            ['nombre' => 'Metro', 'abreviacion' => 'm', 'tipo' => 'LONGITUD', 'icono' => '📏'],
            ['nombre' => 'Centímetro', 'abreviacion' => 'cm', 'tipo' => 'LONGITUD', 'icono' => '📏'],
            ['nombre' => 'Milímetro', 'abreviacion' => 'mm', 'tipo' => 'LONGITUD', 'icono' => '📏'],
            ['nombre' => 'Pulgada', 'abreviacion' => 'in', 'tipo' => 'LONGITUD', 'icono' => '📏'],
            ['nombre' => 'Pie', 'abreviacion' => 'ft', 'tipo' => 'LONGITUD', 'icono' => '📏'],

            // === UNIDADES DE CANTIDAD ===
            ['nombre' => 'Unidad', 'abreviacion' => 'Und', 'tipo' => 'CONTABLE', 'icono' => '📦'],
            ['nombre' => 'Docena', 'abreviacion' => 'Doc', 'tipo' => 'CONTABLE', 'icono' => '📦'],
            ['nombre' => 'Ciento', 'abreviacion' => 'Cto', 'tipo' => 'CONTABLE', 'icono' => '📦'],
            ['nombre' => 'Millar', 'abreviacion' => 'Mill', 'tipo' => 'CONTABLE', 'icono' => '📦'],
            ['nombre' => 'Par', 'abreviacion' => 'Par', 'tipo' => 'CONTABLE', 'icono' => '👟'],

            // === UNIDADES DE EMPAQUE ===
            ['nombre' => 'Paquete', 'abreviacion' => 'Pqt', 'tipo' => 'GENERAL', 'icono' => '📦'],
            ['nombre' => 'Caja', 'abreviacion' => 'Cja', 'tipo' => 'GENERAL', 'icono' => '📦'],
            ['nombre' => 'Bolsa', 'abreviacion' => 'Bls', 'tipo' => 'GENERAL', 'icono' => '🛍️'],
            ['nombre' => 'Sobre', 'abreviacion' => 'Sbr', 'tipo' => 'GENERAL', 'icono' => '✉️'],
            ['nombre' => 'Frasco', 'abreviacion' => 'Frc', 'tipo' => 'GENERAL', 'icono' => '🏺'],
            ['nombre' => 'Botella', 'abreviacion' => 'Bot', 'tipo' => 'GENERAL', 'icono' => '🍾'],
            ['nombre' => 'Lata', 'abreviacion' => 'Lat', 'tipo' => 'GENERAL', 'icono' => '🥫'],
            ['nombre' => 'Tubo', 'abreviacion' => 'Tub', 'tipo' => 'GENERAL', 'icono' => '🧴'],
            ['nombre' => 'Bandeja', 'abreviacion' => 'Bdj', 'tipo' => 'GENERAL', 'icono' => '🍱'],
            ['nombre' => 'Rollo', 'abreviacion' => 'Rll', 'tipo' => 'GENERAL', 'icono' => '🧻'],
            ['nombre' => 'Cojín', 'abreviacion' => 'Coj', 'tipo' => 'GENERAL', 'icono' => '💊'],

            // === UNIDADES ESPECIALES PERÚ ===
            ['nombre' => 'Atado', 'abreviacion' => 'Atd', 'tipo' => 'GENERAL', 'icono' => '🌿'],
            ['nombre' => 'Manojo', 'abreviacion' => 'Mnj', 'tipo' => 'GENERAL', 'icono' => '🌿'],
            ['nombre' => 'Rama', 'abreviacion' => 'Rma', 'tipo' => 'GENERAL', 'icono' => '🌿'],
            ['nombre' => 'Porción', 'abreviacion' => 'Por', 'tipo' => 'GENERAL', 'icono' => '🍽️'],
            ['nombre' => 'Rebanada', 'abreviacion' => 'Reb', 'tipo' => 'GENERAL', 'icono' => '🍞'],
            ['nombre' => 'Plato', 'abreviacion' => 'Plto', 'tipo' => 'GENERAL', 'icono' => '🍽️'],
            ['nombre' => 'Vaso', 'abreviacion' => 'Vso', 'tipo' => 'VOLUMEN', 'icono' => '🥤'],

            // === FARMACIA ===
            ['nombre' => 'Tableta', 'abreviacion' => 'Tab', 'tipo' => 'CONTABLE', 'icono' => '💊'],
            ['nombre' => 'Cápsula', 'abreviacion' => 'Cap', 'tipo' => 'CONTABLE', 'icono' => '💊'],
            ['nombre' => 'Ampolla', 'abreviacion' => 'Amp', 'tipo' => 'CONTABLE', 'icono' => '💉'],
            ['nombre' => 'Gota', 'abreviacion' => 'Gta', 'tipo' => 'VOLUMEN', 'icono' => '💧'],

            // === FERRETERÍA ===
            ['nombre' => 'Metro Cuadrado', 'abreviacion' => 'm²', 'tipo' => 'LONGITUD', 'icono' => '📐'],
            ['nombre' => 'Metro Cúbico', 'abreviacion' => 'm³', 'tipo' => 'VOLUMEN', 'icono' => '📦'],
            ['nombre' => 'Plancha', 'abreviacion' => 'Plnc', 'tipo' => 'GENERAL', 'icono' => '🔧'],
            ['nombre' => 'Barra', 'abreviacion' => 'Brr', 'tipo' => 'GENERAL', 'icono' => '🔧'],

            // === TEXTILES ===
            ['nombre' => 'Talla', 'abreviacion' => 'Tll', 'tipo' => 'GENERAL', 'icono' => '👕'],
            ['nombre' => 'Conjunto', 'abreviacion' => 'Cnj', 'tipo' => 'GENERAL', 'icono' => '👔'],
        ];

        foreach ($unidades as $unidad) {
            DB::table('unidades_medida')->insert([
                'empresa_id' => 1, // Empresa por defecto
                'nombre' => $unidad['nombre'],
                'abreviacion' => $unidad['abreviacion'],
                'tipo' => $unidad['tipo'],
                'icono' => $unidad['icono'],
                'activa' => 1,
                'productos_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
