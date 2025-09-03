<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\ProductoStock;
use App\Models\InventarioMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CompraService
{
    /**
     * Crear nueva compra
     */
    public function crearCompra(array $datos): Compra
    {
        return DB::transaction(function() use ($datos) {
            $compra = Compra::create([
                'empresa_id' => $datos['empresa_id'],
                'proveedor_id' => $datos['proveedor_id'],
                'sucursal_destino_id' => $datos['sucursal_destino_id'],
                'user_id' => $datos['user_id'] ?? Auth::id(),
                'fecha' => $datos['fecha'] ?? Carbon::now(),
                'numero_doc' => $datos['numero_doc'] ?? null,
                'tipo_doc' => $datos['tipo_doc'] ?? 'factura',
                'estado' => 'borrador',
                'moneda' => $datos['moneda'] ?? 'PEN',
                'observaciones' => $datos['observaciones'] ?? null
            ]);

            // Agregar items si vienen en los datos
            if (isset($datos['items']) && is_array($datos['items'])) {
                foreach ($datos['items'] as $itemData) {
                    $this->agregarItem($compra->id, $itemData);
                }
                $compra->calcularTotal();
            }

            return $compra;
        });
    }

    /**
     * Agregar item a compra
     */
    public function agregarItem(int $compraId, array $datos): CompraItem
    {
        $compra = Compra::findOrFail($compraId);
        
        $item = CompraItem::create([
            'empresa_id' => $compra->empresa_id,
            'compra_id' => $compraId,
            'producto_id' => $datos['producto_id'],
            'cantidad' => $datos['cantidad'],
            'costo_unitario' => $datos['costo_unitario'],
            'descuento_pct' => $datos['descuento_pct'] ?? 0
        ]);

        $item->calcularSubtotal();
        $compra->calcularTotal();

        return $item;
    }

    /**
     * Confirmar compra
     */
    public function confirmarCompra(int $id): bool
    {
        $compra = Compra::findOrFail($id);
        
        if ($compra->estado !== 'borrador') {
            return false;
        }

        if ($compra->items()->count() === 0) {
            return false;
        }

        return $compra->confirmar();
    }

    /**
     * Recibir mercadería (actualizar stock)
     */
    public function recibirMercaderia(int $compraId, array $itemsRecibidos): bool
    {
        return DB::transaction(function() use ($compraId, $itemsRecibidos) {
            $compra = Compra::findOrFail($compraId);
            
            if (!$compra->puedeRecibirse()) {
                return false;
            }

            foreach ($itemsRecibidos as $itemData) {
                $item = CompraItem::findOrFail($itemData['item_id']);
                $cantidadRecibida = $itemData['cantidad_recibida'];

                // Actualizar stock
                $this->actualizarStock($item, $cantidadRecibida);
                
                // Registrar movimiento de inventario
                $this->registrarMovimientoInventario($item, $cantidadRecibida);
            }

            $compra->marcarRecibida();
            return true;
        });
    }

    /**
     * Actualizar stock del producto
     */
    private function actualizarStock(CompraItem $item, float $cantidad): void
    {
        $stock = ProductoStock::firstOrCreate([
            'empresa_id' => $item->empresa_id,
            'producto_id' => $item->producto_id,
            'sucursal_id' => $item->compra->sucursal_destino_id
        ], [
            'cantidad_actual' => 0,
            'stock_minimo' => 0,
            'stock_maximo' => 0
        ]);

        $stock->cantidad_actual += $cantidad;
        $stock->save();
    }

    /**
     * Registrar movimiento de inventario
     */
    private function registrarMovimientoInventario(CompraItem $item, float $cantidad): void
    {
        InventarioMovimiento::create([
            'empresa_id' => $item->empresa_id,
            'producto_id' => $item->producto_id,
            'sucursal_id' => $item->compra->sucursal_destino_id,
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'costo_unitario' => $item->costo_unitario,
            'motivo' => 'Compra #' . $item->compra->id,
            'referencia_tipo' => 'compra',
            'referencia_id' => $item->compra_id,
            'user_id' => Auth::id(),
            'fecha' => Carbon::now()
        ]);
    }

    /**
     * Anular compra
     */
    public function anularCompra(int $id, string $motivo): bool
    {
        $compra = Compra::findOrFail($id);
        return $compra->anular($motivo);
    }
}
