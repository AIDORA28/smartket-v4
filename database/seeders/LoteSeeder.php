<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Empresa;
use App\Models\InventarioMovimiento;
use Carbon\Carbon;

class LoteSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();
        if (!$empresa) {
            $this->command->error('No se encontró ninguna empresa. Ejecute EmpresaSeeder primero.');
            return;
        }

        $productos = Producto::where('empresa_id', $empresa->id)->get();
        if ($productos->isEmpty()) {
            $this->command->error('No se encontraron productos. Ejecute ProductoSeeder primero.');
            return;
        }

        $proveedores = Proveedor::where('empresa_id', $empresa->id)->get();
        if ($proveedores->isEmpty()) {
            $this->command->error('No se encontraron proveedores. Ejecute ProveedorSeeder primero.');
            return;
        }

        $this->command->info('Creando lotes de prueba...');

        // Crear lotes con diferentes estados y fechas de vencimiento
        $lotesData = [
            // Lotes activos con diferentes fechas de vencimiento
            [
                'producto_index' => 0,
                'proveedor_index' => 0,
                'cantidad_inicial' => 100,
                'dias_vencimiento' => 30, // Próximo a vencer
                'estado' => 'activo'
            ],
            [
                'producto_index' => 0,
                'proveedor_index' => 1,
                'cantidad_inicial' => 150,
                'dias_vencimiento' => 90, // Normal
                'estado' => 'activo'
            ],
            [
                'producto_index' => 1,
                'proveedor_index' => 0,
                'cantidad_inicial' => 80,
                'dias_vencimiento' => 180, // Largo plazo
                'estado' => 'activo'
            ],
            [
                'producto_index' => 1,
                'proveedor_index' => 2,
                'cantidad_inicial' => 200,
                'dias_vencimiento' => 15, // Crítico - próximo a vencer
                'estado' => 'activo'
            ],
            [
                'producto_index' => 2,
                'proveedor_index' => 1,
                'cantidad_inicial' => 120,
                'dias_vencimiento' => 60, // Normal
                'estado' => 'activo'
            ],
            // Lotes vencidos
            [
                'producto_index' => 2,
                'proveedor_index' => 0,
                'cantidad_inicial' => 50,
                'dias_vencimiento' => -10, // Vencido hace 10 días
                'estado' => 'vencido'
            ],
            [
                'producto_index' => 3,
                'proveedor_index' => 2,
                'cantidad_inicial' => 75,
                'dias_vencimiento' => -5, // Vencido hace 5 días
                'estado' => 'vencido'
            ],
            // Lote agotado
            [
                'producto_index' => 3,
                'proveedor_index' => 1,
                'cantidad_inicial' => 60,
                'dias_vencimiento' => 45,
                'estado' => 'agotado'
            ]
        ];

        foreach ($lotesData as $index => $loteData) {
            if ($loteData['producto_index'] >= $productos->count() || 
                $loteData['proveedor_index'] >= $proveedores->count()) {
                continue;
            }

            $producto = $productos[$loteData['producto_index']];
            $proveedor = $proveedores[$loteData['proveedor_index']];

            // Crear fecha de vencimiento
            $fechaVencimiento = Carbon::now()->addDays($loteData['dias_vencimiento']);

            $lote = Lote::create([
                'empresa_id' => $empresa->id,
                'producto_id' => $producto->id,
                'proveedor_id' => $proveedor->id,
                'codigo_lote' => 'LT' . str_pad($index + 1, 4, '0', STR_PAD_LEFT) . date('Y'),
                'fecha_vencimiento' => $fechaVencimiento,
                'estado_lote' => $loteData['estado']
            ]);

            // Crear movimiento de entrada inicial
            InventarioMovimiento::create([
                'empresa_id' => $empresa->id,
                'producto_id' => $producto->id,
                'lote_id' => $lote->id,
                'sucursal_id' => 1, // Asumiendo sucursal principal
                'usuario_id' => 1, // Usuario admin
                'tipo_movimiento' => 'ENTRADA',
                'cantidad' => $loteData['cantidad_inicial'],
                'fecha_movimiento' => Carbon::now()->subDays(rand(1, 30)),
                'referencia_tipo' => 'COMPRA',
                'referencia_id' => '1',
                'costo_unitario' => rand(10, 100),
                'stock_anterior' => 0,
                'stock_posterior' => $loteData['cantidad_inicial'],
                'observaciones' => 'Entrada inicial del lote ' . $lote->codigo_lote
            ]);

            // Si es un lote agotado, crear movimientos de salida
            if ($loteData['estado'] === 'agotado') {
                $cantidadRestante = $loteData['cantidad_inicial'];
                $numMovimientos = rand(2, 4);
                
                for ($i = 0; $i < $numMovimientos && $cantidadRestante > 0; $i++) {
                    $cantidadSalida = $i === $numMovimientos - 1 ? 
                        $cantidadRestante : // Última salida toma todo lo restante
                        rand(10, min(30, $cantidadRestante));
                    
                    $stockAnterior = $cantidadRestante;
                    $stockPosterior = $cantidadRestante - $cantidadSalida;
                    
                    InventarioMovimiento::create([
                        'empresa_id' => $empresa->id,
                        'producto_id' => $producto->id,
                        'lote_id' => $lote->id,
                        'sucursal_id' => 1,
                        'usuario_id' => 1,
                        'tipo_movimiento' => 'SALIDA',
                        'cantidad' => -$cantidadSalida,
                        'fecha_movimiento' => Carbon::now()->subDays(rand(1, 15)),
                        'referencia_tipo' => 'VENTA',
                        'referencia_id' => (string)rand(1, 10),
                        'costo_unitario' => rand(10, 100),
                        'stock_anterior' => $stockAnterior,
                        'stock_posterior' => $stockPosterior,
                        'observaciones' => 'Salida por venta - Lote ' . $lote->codigo_lote
                    ]);
                    
                    $cantidadRestante -= $cantidadSalida;
                }
            }

            // Para algunos lotes activos, crear algunos movimientos de salida parciales
            if ($loteData['estado'] === 'activo' && rand(0, 1)) {
                $cantidadSalida = rand(10, min(30, $loteData['cantidad_inicial'] / 2));
                
                InventarioMovimiento::create([
                    'empresa_id' => $empresa->id,
                    'producto_id' => $producto->id,
                    'lote_id' => $lote->id,
                    'sucursal_id' => 1,
                    'usuario_id' => 1,
                    'tipo_movimiento' => 'SALIDA',
                    'cantidad' => -$cantidadSalida,
                    'fecha_movimiento' => Carbon::now()->subDays(rand(1, 10)),
                    'referencia_tipo' => 'VENTA',
                    'referencia_id' => (string)rand(1, 10),
                    'costo_unitario' => rand(10, 100),
                    'stock_anterior' => $loteData['cantidad_inicial'],
                    'stock_posterior' => $loteData['cantidad_inicial'] - $cantidadSalida,
                    'observaciones' => 'Venta parcial - Lote ' . $lote->codigo_lote
                ]);
            }

            $this->command->info("✓ Lote creado: {$lote->codigo_lote} - {$producto->nombre} - Estado: {$lote->estado_lote}");
        }

        $this->command->info('✓ Lotes de prueba creados exitosamente');
        
        // Mostrar resumen
        $this->mostrarResumen($empresa->id);
    }

    private function mostrarResumen(int $empresaId): void
    {
        $this->command->info("\n=== RESUMEN DE LOTES CREADOS ===");
        
        $totalLotes = Lote::where('empresa_id', $empresaId)->count();
        $lotesActivos = Lote::where('empresa_id', $empresaId)->where('estado_lote', 'activo')->count();
        $lotesVencidos = Lote::where('empresa_id', $empresaId)->where('estado_lote', 'vencido')->count();
        $lotesAgotados = Lote::where('empresa_id', $empresaId)->where('estado_lote', 'agotado')->count();
        
        $this->command->info("Total de lotes: {$totalLotes}");
        $this->command->info("Lotes activos: {$lotesActivos}");
        $this->command->info("Lotes vencidos: {$lotesVencidos}");
        $this->command->info("Lotes agotados: {$lotesAgotados}");
        
        // Mostrar lotes próximos a vencer
        $lotesProximosVencer = Lote::where('empresa_id', $empresaId)
            ->where('estado_lote', 'activo')
            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->count();
            
        if ($lotesProximosVencer > 0) {
            $this->command->warn("⚠️  {$lotesProximosVencer} lote(s) próximo(s) a vencer en los próximos 30 días");
        }
    }
}
