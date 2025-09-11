<?php

namespace App\Services;

use App\Models\Inventory\Producto;
use App\Models\Inventory\ProductoStock;
use App\Models\Inventory\InventarioMovimiento;
use App\Models\Inventory\Categoria;
use Illuminate\Support\Facades\DB;

class InventarioService
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Registrar un movimiento de inventario
     */
    public function registrarMovimiento(int $productoId, int $sucursalId, string $tipo, float $cantidad, float $costoUnitario = 0, string $motivo = null, int $usuarioId = null): InventarioMovimiento
    {
        return DB::transaction(function () use ($productoId, $sucursalId, $tipo, $cantidad, $costoUnitario, $motivo, $usuarioId) {
            $empresa = $this->tenantService->getEmpresa();
            
            // Obtener o crear registro de stock
            $stock = ProductoStock::firstOrCreate([
                'empresa_id' => $empresa->id,
                'producto_id' => $productoId,
                'sucursal_id' => $sucursalId,
            ], [
                'cantidad_actual' => 0,
                'cantidad_reservada' => 0,
                'cantidad_disponible' => 0,
                'costo_promedio' => 0,
            ]);

            $stockAnterior = $stock->cantidad_actual;
            
            // Determinar el nuevo stock según tipo de movimiento
            switch ($tipo) {
                case 'entrada':
                    $stockPosterior = $stockAnterior + $cantidad;
                    break;
                    
                case 'salida':
                    $stockPosterior = $stockAnterior - $cantidad;
                    if ($stockPosterior < 0) {
                        throw new \Exception('Stock insuficiente para la operación');
                    }
                    break;
                    
                case 'ajuste':
                    $stockPosterior = $cantidad; // La cantidad es el nuevo total
                    $cantidad = $stockPosterior - $stockAnterior; // Diferencia real
                    break;
                    
                default:
                    throw new \Exception('Tipo de movimiento no válido');
            }

            // Crear el movimiento
            $movimiento = InventarioMovimiento::create([
                'empresa_id' => $empresa->id,
                'producto_id' => $productoId,
                'sucursal_id' => $sucursalId,
                'usuario_id' => $usuarioId ?? request()->user()?->id,
                'tipo_movimiento' => $tipo,
                'cantidad' => abs($cantidad),
                'costo_unitario' => $costoUnitario,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $stockPosterior,
                'observaciones' => $motivo,
                'fecha_movimiento' => now(),
            ]);

            // Actualizar stock
            $costoPromedio = $this->calcularCostoPromedio($stock, abs($cantidad), $costoUnitario);
            
            $stock->update([
                'cantidad_actual' => $stockPosterior,
                'costo_promedio' => $costoPromedio > 0 ? $costoPromedio : $stock->costo_promedio,
                'cantidad_disponible' => $stockPosterior,
            ]);

            return $movimiento;
        });
    }

    /**
     * Calcular costo promedio ponderado
     */
    private function calcularCostoPromedio(ProductoStock $stock, float $cantidad, float $costoUnitario): float
    {
        if ($cantidad <= 0 || $costoUnitario <= 0) {
            return $stock->costo_promedio;
        }

        $valorActual = $stock->cantidad_actual * $stock->costo_promedio;
        $valorNuevo = $cantidad * $costoUnitario;
        $cantidadTotal = $stock->cantidad_actual + $cantidad;

        return $cantidadTotal > 0 ? ($valorActual + $valorNuevo) / $cantidadTotal : 0;
    }

    /**
     * Obtener stock actual de un producto en una sucursal
     */
    public function obtenerStock(int $productoId, int $sucursalId): float
    {
        $stock = ProductoStock::forProducto($productoId)
            ->forSucursal($sucursalId)
            ->first();

        return $stock ? $stock->cantidad_actual : 0;
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getProductosStockBajo(): \Illuminate\Database\Eloquent\Collection
    {
        $empresa = $this->tenantService->getEmpresa();
        $sucursal = $this->tenantService->getSucursal();

        return Producto::forEmpresa($empresa->id)
            ->active()
            ->withStock()
            ->whereHas('stocks', function ($query) use ($sucursal) {
                $query->forSucursal($sucursal->id)
                      ->whereRaw('cantidad_actual <= stock_minimo');
            })
            ->with(['categoria', 'stocks' => function ($query) use ($sucursal) {
                $query->forSucursal($sucursal->id);
            }])
            ->get();
    }

    /**
     * Obtener resumen de inventario por categoría
     */
    public function getResumenPorCategoria(): array
    {
        $empresa = $this->tenantService->getEmpresa();
        $sucursal = $this->tenantService->getSucursal();

        return Categoria::forEmpresa($empresa->id)
            ->active()
            ->withCount(['productos' => function ($query) use ($sucursal) {
                $query->active()
                      ->whereHas('stocks', function ($stockQuery) use ($sucursal) {
                          $stockQuery->forSucursal($sucursal->id)
                                    ->where('cantidad_actual', '>', 0);
                      });
            }])
            ->get()
            ->map(function ($categoria) use ($sucursal) {
                $valorTotal = $categoria->productos()
                    ->active()
                    ->whereHas('stocks', function ($query) use ($sucursal) {
                        $query->forSucursal($sucursal->id);
                    })
                    ->with(['stocks' => function ($query) use ($sucursal) {
                        $query->forSucursal($sucursal->id);
                    }])
                    ->get()
                    ->sum(function ($producto) {
                        $stock = $producto->stocks->first();
                        return $stock ? $stock->cantidad_actual * $stock->costo_promedio : 0;
                    });

                return [
                    'categoria' => $categoria,
                    'valor_inventario' => $valorTotal,
                ];
            })
            ->toArray();
    }

    /**
     * Transferir stock entre sucursales
     */
    public function transferirStock(int $productoId, int $sucursalOrigen, int $sucursalDestino, float $cantidad, string $observaciones = null): array
    {
        return DB::transaction(function () use ($productoId, $sucursalOrigen, $sucursalDestino, $cantidad, $observaciones) {
            $empresa = $this->tenantService->getEmpresa();

            // Validar que hay suficiente stock en origen
            $stockOrigen = ProductoStock::where('empresa_id', $empresa->id)
                ->where('producto_id', $productoId)
                ->where('sucursal_id', $sucursalOrigen)
                ->first();

            if (!$stockOrigen || $stockOrigen->cantidad_actual < $cantidad) {
                throw new \Exception('Stock insuficiente en sucursal de origen');
            }

            // Registrar salida en origen
            $movimientoSalida = $this->registrarMovimiento(
                $productoId,
                $sucursalOrigen,
                'salida',
                $cantidad,
                $stockOrigen->costo_promedio,
                $observaciones ? "Transferencia: $observaciones" : 'Transferencia entre sucursales'
            );

            // Registrar entrada en destino
            $movimientoEntrada = $this->registrarMovimiento(
                $productoId,
                $sucursalDestino,
                'entrada',
                $cantidad,
                $stockOrigen->costo_promedio,
                $observaciones ? "Transferencia: $observaciones" : 'Transferencia entre sucursales'
            );

            return [
                'movimiento_salida' => $movimientoSalida,
                'movimiento_entrada' => $movimientoEntrada,
            ];
        });
    }
}

