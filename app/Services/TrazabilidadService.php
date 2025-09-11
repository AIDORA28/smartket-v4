<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\Inventory\InventarioMovimiento;
use App\Models\CompraItem;
use App\Models\Sales\VentaDetalle;
use Illuminate\Support\Collection;

class TrazabilidadService
{
    /**
     * Obtener trazabilidad completa de un lote
     */
    public function getTrazabilidadLote(int $loteId): array
    {
        $lote = Lote::with(['producto', 'proveedor', 'empresa'])->findOrFail($loteId);
        
        return [
            'lote' => $lote,
            'origen' => $this->getOrigenLote($lote),
            'movimientos' => $this->getMovimientosLote($loteId),
            'ventas' => $this->getVentasLote($loteId),
            'stock_actual' => $lote->getStockActual(),
            'historial_estados' => $this->getHistorialEstados($loteId)
        ];
    }

    /**
     * Obtener origen del lote (compra inicial)
     */
    private function getOrigenLote(Lote $lote): ?array
    {
        $compraItem = CompraItem::where('lote_id', $lote->id)
            ->with(['compra.proveedor', 'compra.usuario'])
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$compraItem) {
            return null;
        }

        return [
            'tipo' => 'compra',
            'compra_id' => $compraItem->compra_id,
            'fecha' => $compraItem->compra->fecha,
            'proveedor' => $compraItem->compra->proveedor->nombre,
            'usuario' => $compraItem->compra->usuario->name,
            'cantidad_inicial' => $compraItem->cantidad,
            'costo_unitario' => $compraItem->costo_unitario
        ];
    }

    /**
     * Obtener todos los movimientos de inventario del lote
     */
    private function getMovimientosLote(int $loteId): Collection
    {
        return InventarioMovimiento::where('lote_id', $loteId)
            ->with(['sucursal', 'usuario'])
            ->orderBy('fecha_movimiento', 'desc')
            ->get()
            ->map(function($movimiento) {
                return [
                    'id' => $movimiento->id,
                    'fecha' => $movimiento->fecha_movimiento,
                    'tipo' => $movimiento->tipo_movimiento,
                    'cantidad' => $movimiento->cantidad,
                    'referencia_tipo' => $movimiento->referencia_tipo,
                    'referencia_id' => $movimiento->referencia_id,
                    'sucursal' => $movimiento->sucursal->nombre,
                    'usuario' => $movimiento->usuario ? $movimiento->usuario->name : 'Sistema',
                    'observaciones' => $movimiento->observaciones
                ];
            });
    }

    /**
     * Obtener ventas que utilizaron este lote
     */
    private function getVentasLote(int $loteId): Collection
    {
        return VentaDetalle::where('lote_id', $loteId)
            ->with(['venta.cliente', 'venta.usuario'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($detalle) {
                return [
                    'venta_id' => $detalle->venta_id,
                    'fecha' => $detalle->venta->fecha,
                    'cliente' => $detalle->venta->cliente ? $detalle->venta->cliente->nombre : 'Cliente general',
                    'usuario' => $detalle->venta->usuario->name,
                    'cantidad_vendida' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'total' => $detalle->precio_unitario * $detalle->cantidad
                ];
            });
    }

    /**
     * Obtener historial de cambios de estado del lote
     */
    private function getHistorialEstados(int $loteId): Collection
    {
        // En una implementación más avanzada, esto vendría de una tabla de auditoría
        // Por ahora, inferimos del estado actual y movimientos
        $lote = Lote::find($loteId);
        $movimientos = InventarioMovimiento::where('lote_id', $loteId)
            ->orderBy('fecha_movimiento', 'asc')
            ->get();

        $historial = collect();
        
        // Estado inicial cuando se creó
        $historial->push([
            'fecha' => $lote->created_at,
            'estado_anterior' => null,
            'estado_nuevo' => 'activo',
            'motivo' => 'Creación del lote',
            'usuario' => 'Sistema'
        ]);

        // Inferir cambios de estado basado en stock
        $stockAcumulado = 0;
        foreach ($movimientos as $movimiento) {
            $stockAcumulado += $movimiento->cantidad;
            
            if ($stockAcumulado <= 0 && $movimiento->tipo_movimiento === 'SALIDA') {
                $historial->push([
                    'fecha' => $movimiento->fecha_movimiento,
                    'estado_anterior' => 'activo',
                    'estado_nuevo' => 'agotado',
                    'motivo' => 'Stock agotado por ' . $movimiento->referencia_tipo,
                    'usuario' => $movimiento->usuario ? $movimiento->usuario->name : 'Sistema'
                ]);
            } elseif ($stockAcumulado > 0 && $movimiento->tipo_movimiento === 'ENTRADA') {
                $historial->push([
                    'fecha' => $movimiento->fecha_movimiento,
                    'estado_anterior' => 'agotado',
                    'estado_nuevo' => 'activo',
                    'motivo' => 'Reactivado por ' . $movimiento->referencia_tipo,
                    'usuario' => $movimiento->usuario ? $movimiento->usuario->name : 'Sistema'
                ]);
            }
        }

        // Estado actual si está vencido
        if ($lote->estaVencido() && $lote->estado_lote === 'vencido') {
            $historial->push([
                'fecha' => $lote->fecha_vencimiento,
                'estado_anterior' => 'activo',
                'estado_nuevo' => 'vencido',
                'motivo' => 'Fecha de vencimiento alcanzada',
                'usuario' => 'Sistema'
            ]);
        }

        return $historial->sortByDesc('fecha');
    }

    /**
     * Buscar lotes por código o producto
     */
    public function buscarLotes(int $empresaId, string $termino): Collection
    {
        return Lote::where('empresa_id', $empresaId)
            ->where(function($query) use ($termino) {
                $query->where('codigo_lote', 'like', "%{$termino}%")
                      ->orWhereHas('producto', function($q) use ($termino) {
                          $q->where('nombre', 'like', "%{$termino}%")
                            ->orWhere('codigo_interno', 'like', "%{$termino}%");
                      });
            })
            ->with(['producto', 'proveedor'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($lote) {
                $lote->stock_actual = $lote->getStockActual();
                return $lote;
            });
    }

    /**
     * Obtener reporte de trazabilidad por producto
     */
    public function getReporteTrazabilidadProducto(int $empresaId, int $productoId): array
    {
        $lotes = Lote::where('empresa_id', $empresaId)
            ->where('producto_id', $productoId)
            ->with(['proveedor'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        $resumen = [
            'total_lotes' => $lotes->count(),
            'lotes_activos' => $lotes->where('estado_lote', 'activo')->count(),
            'lotes_vencidos' => $lotes->where('estado_lote', 'vencido')->count(),
            'lotes_agotados' => $lotes->where('estado_lote', 'agotado')->count(),
            'stock_total' => 0,
            'valor_total' => 0,
            'lotes_detalle' => []
        ];

        foreach ($lotes as $lote) {
            $stock = $lote->getStockActual();
            $resumen['stock_total'] += $stock;
            
            // Calcular valor aproximado basado en último costo
            $ultimoMovimiento = InventarioMovimiento::where('lote_id', $lote->id)
                ->whereNotNull('costo_unitario')
                ->orderBy('fecha_movimiento', 'desc')
                ->first();
                
            $costoUnitario = $ultimoMovimiento ? $ultimoMovimiento->costo_unitario : 0;
            $valorLote = $stock * $costoUnitario;
            $resumen['valor_total'] += $valorLote;

            $resumen['lotes_detalle'][] = [
                'lote' => $lote,
                'stock_actual' => $stock,
                'costo_unitario' => $costoUnitario,
                'valor_total' => $valorLote,
                'dias_vencimiento' => $lote->diasHastaVencimiento()
            ];
        }

        return $resumen;
    }
}

