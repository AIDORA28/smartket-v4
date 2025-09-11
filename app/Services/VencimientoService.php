<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\Inventory\Producto;
use App\Models\Inventory\ProductoStock;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class VencimientoService
{
    /**
     * Obtener productos próximos a vencer
     */
    public function getProductosProximosAVencer(int $empresaId, int $dias = 30): Collection
    {
        return Lote::where('empresa_id', $empresaId)
            ->proximosAVencer($dias)
            ->with(['producto', 'proveedor'])
            ->get()
            ->map(function($lote) {
                $lote->stock_actual = $lote->getStockActual();
                $lote->dias_restantes = $lote->diasHastaVencimiento();
                return $lote;
            })
            ->filter(function($lote) {
                return $lote->stock_actual > 0; // Solo mostrar lotes con stock
            })
            ->sortBy('dias_restantes');
    }

    /**
     * Obtener productos vencidos
     */
    public function getProductosVencidos(int $empresaId): Collection
    {
        return Lote::where('empresa_id', $empresaId)
            ->vencidos()
            ->with(['producto', 'proveedor'])
            ->get()
            ->map(function($lote) {
                $lote->stock_actual = $lote->getStockActual();
                $lote->dias_vencido = abs($lote->diasHastaVencimiento());
                return $lote;
            })
            ->filter(function($lote) {
                return $lote->stock_actual > 0; // Solo mostrar lotes con stock
            })
            ->sortByDesc('dias_vencido');
    }

    /**
     * Generar alertas de vencimiento personalizadas
     */
    public function generarAlertas(int $empresaId): array
    {
        $alertas = [
            'criticas' => [],    // Vencen en 1-3 días
            'importantes' => [], // Vencen en 4-7 días
            'preventivas' => [], // Vencen en 8-30 días
            'vencidos' => []     // Ya vencidos
        ];

        // Obtener configuraciones de alerta por producto
        $productosConAlerta = ProductoStock::where('empresa_id', $empresaId)
            ->where('maneja_lotes', true)
            ->with('producto')
            ->get()
            ->keyBy('producto_id');

        // Procesar lotes próximos a vencer
        $lotes = Lote::where('empresa_id', $empresaId)
            ->whereNotNull('fecha_vencimiento')
            ->activos()
            ->with(['producto', 'proveedor'])
            ->get();

        foreach ($lotes as $lote) {
            $stock = $lote->getStockActual();
            if ($stock <= 0) continue;

            $dias = $lote->diasHastaVencimiento();
            $configuracion = $productosConAlerta->get($lote->producto_id);
            $diasAlerta = $configuracion ? $configuracion->alerta_vencimiento_dias : 30;

            $loteData = [
                'lote' => $lote,
                'stock_actual' => $stock,
                'dias_restantes' => $dias,
                'producto_nombre' => $lote->producto->nombre,
                'proveedor_nombre' => $lote->proveedor ? $lote->proveedor->nombre : 'Sin proveedor'
            ];

            if ($dias < 0) {
                $alertas['vencidos'][] = $loteData;
            } elseif ($dias <= 3) {
                $alertas['criticas'][] = $loteData;
            } elseif ($dias <= 7) {
                $alertas['importantes'][] = $loteData;
            } elseif ($dias <= $diasAlerta) {
                $alertas['preventivas'][] = $loteData;
            }
        }

        return $alertas;
    }

    /**
     * Obtener resumen de vencimientos
     */
    public function getResumenVencimientos(int $empresaId): array
    {
        $hoy = Carbon::now();
        
        return [
            'vencidos' => Lote::where('empresa_id', $empresaId)
                ->where('fecha_vencimiento', '<', $hoy)
                ->activos()
                ->count(),
                
            'vencen_hoy' => Lote::where('empresa_id', $empresaId)
                ->whereDate('fecha_vencimiento', $hoy)
                ->activos()
                ->count(),
                
            'vencen_manana' => Lote::where('empresa_id', $empresaId)
                ->whereDate('fecha_vencimiento', $hoy->copy()->addDay())
                ->activos()
                ->count(),
                
            'vencen_esta_semana' => Lote::where('empresa_id', $empresaId)
                ->whereBetween('fecha_vencimiento', [
                    $hoy->copy()->addDays(2),
                    $hoy->copy()->addDays(7)
                ])
                ->activos()
                ->count(),
                
            'vencen_este_mes' => Lote::where('empresa_id', $empresaId)
                ->whereBetween('fecha_vencimiento', [
                    $hoy->copy()->addDays(8),
                    $hoy->copy()->addDays(30)
                ])
                ->activos()
                ->count()
        ];
    }

    /**
     * Marcar lotes vencidos automáticamente
     */
    public function procesarLotesVencidos(int $empresaId): array
    {
        $lotesVencidos = Lote::where('empresa_id', $empresaId)
            ->vencidos()
            ->activos()
            ->get();

        $procesados = [];
        foreach ($lotesVencidos as $lote) {
            $stock = $lote->getStockActual();
            $lote->marcarVencido();
            
            $procesados[] = [
                'lote_id' => $lote->id,
                'codigo_lote' => $lote->codigo_lote,
                'producto' => $lote->producto->nombre,
                'stock_vencido' => $stock,
                'dias_vencido' => abs($lote->diasHastaVencimiento())
            ];
        }

        return $procesados;
    }

    /**
     * Configurar alertas de vencimiento para un producto
     */
    public function configurarAlertaProducto(int $empresaId, int $productoId, int $dias, bool $manejaLotes = true): bool
    {
        return ProductoStock::where('empresa_id', $empresaId)
            ->where('producto_id', $productoId)
            ->update([
                'alerta_vencimiento_dias' => $dias,
                'maneja_lotes' => $manejaLotes
            ]) > 0;
    }
}

