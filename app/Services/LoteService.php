<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\Inventory\Producto;
use App\Models\Inventory\InventarioMovimiento;
use App\Models\Sales\VentaDetalle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoteService
{
    /**
     * Crear nuevo lote
     */
    public function crearLote(array $datos): Lote
    {
        // Generar código automático si no se proporciona
        if (!isset($datos['codigo_lote'])) {
            $datos['codigo_lote'] = Lote::generarCodigo(
                $datos['empresa_id'],
                $datos['producto_id'],
                isset($datos['fecha_produccion']) ? Carbon::parse($datos['fecha_produccion']) : null
            );
        }

        return Lote::create($datos);
    }

    /**
     * Asignar lote automáticamente usando FIFO
     */
    public function asignarLoteFIFO(int $empresaId, int $productoId, float $cantidadNecesaria): array
    {
        $lotesDisponibles = Lote::where('empresa_id', $empresaId)
            ->where('producto_id', $productoId)
            ->activos()
            ->FIFO()
            ->get();

        $asignaciones = [];
        $cantidadRestante = $cantidadNecesaria;

        foreach ($lotesDisponibles as $lote) {
            if ($cantidadRestante <= 0) {
                break;
            }

            if (!$lote->puedeUtilizarse()) {
                continue;
            }

            $stockLote = $lote->getStockActual();
            if ($stockLote <= 0) {
                continue;
            }

            $cantidadAAsignar = min($cantidadRestante, $stockLote);
            
            $asignaciones[] = [
                'lote_id' => $lote->id,
                'lote' => $lote,
                'cantidad' => $cantidadAAsignar
            ];

            $cantidadRestante -= $cantidadAAsignar;
        }

        return [
            'asignaciones' => $asignaciones,
            'cantidad_asignada' => $cantidadNecesaria - $cantidadRestante,
            'cantidad_faltante' => $cantidadRestante
        ];
    }

    /**
     * Procesar salida de stock con FIFO automático
     */
    public function procesarSalidaFIFO(
        int $empresaId,
        int $sucursalId,
        int $productoId,
        float $cantidad,
        string $referenciaType,
        int $referenciaId,
        int $userId
    ): bool {
        return DB::transaction(function() use ($empresaId, $sucursalId, $productoId, $cantidad, $referenciaType, $referenciaId, $userId) {
            $asignacion = $this->asignarLoteFIFO($empresaId, $productoId, $cantidad);
            
            if ($asignacion['cantidad_faltante'] > 0) {
                throw new \Exception("Stock insuficiente. Faltante: {$asignacion['cantidad_faltante']}");
            }

            foreach ($asignacion['asignaciones'] as $item) {
                // Registrar movimiento de inventario
                InventarioMovimiento::create([
                    'empresa_id' => $empresaId,
                    'sucursal_id' => $sucursalId,
                    'producto_id' => $productoId,
                    'lote_id' => $item['lote_id'],
                    'usuario_id' => $userId,
                    'tipo_movimiento' => 'SALIDA',
                    'referencia_tipo' => strtoupper($referenciaType),
                    'referencia_id' => (string)$referenciaId,
                    'cantidad' => -$item['cantidad'], // Negativo para salida
                    'costo_unitario' => 0,
                    'stock_anterior' => 0, // Se podría calcular
                    'stock_posterior' => 0, // Se podría calcular
                    'fecha_movimiento' => Carbon::now()
                ]);

                // Verificar si el lote se agotó
                $stockRestante = $item['lote']->getStockActual() - $item['cantidad'];
                if ($stockRestante <= 0) {
                    $item['lote']->marcarAgotado();
                }
            }

            return true;
        });
    }

    /**
     * Procesar entrada de stock para un lote específico
     */
    public function procesarEntrada(
        int $loteId,
        int $sucursalId,
        float $cantidad,
        string $referenciaType,
        int $referenciaId,
        int $userId,
        ?float $costoUnitario = null
    ): bool {
        $lote = Lote::findOrFail($loteId);
        
        // Registrar movimiento de inventario
        InventarioMovimiento::create([
            'empresa_id' => $lote->empresa_id,
            'sucursal_id' => $sucursalId,
            'producto_id' => $lote->producto_id,
            'lote_id' => $loteId,
            'usuario_id' => $userId,
            'tipo_movimiento' => 'ENTRADA',
            'referencia_tipo' => strtoupper($referenciaType),
            'referencia_id' => (string)$referenciaId,
            'cantidad' => $cantidad,
            'costo_unitario' => $costoUnitario ?? 0,
            'stock_anterior' => 0, // Se podría calcular
            'stock_posterior' => $cantidad, // Se podría calcular
            'fecha_movimiento' => Carbon::now()
        ]);

        // Reactivar lote si estaba agotado
        if ($lote->estado_lote === 'agotado') {
            $lote->activar();
        }

        return true;
    }

    /**
     * Obtener lotes de un producto ordenados por FIFO
     */
    public function getLotesFIFO(int $empresaId, int $productoId): \Illuminate\Database\Eloquent\Collection
    {
        return Lote::empresa($empresaId)
            ->producto($productoId)
            ->activos()
            ->FIFO()
            ->with(['proveedor'])
            ->get()
            ->map(function($lote) {
                $lote->stock_actual = $lote->getStockActual();
                return $lote;
            });
    }

    /**
     * Actualizar estados de lotes vencidos
     */
    public function actualizarLotesVencidos(int $empresaId): int
    {
        $lotesVencidos = Lote::empresa($empresaId)
            ->vencidos()
            ->where('estado_lote', 'activo')
            ->get();

        $count = 0;
        foreach ($lotesVencidos as $lote) {
            $lote->marcarVencido();
            $count++;
        }

        return $count;
    }
}

