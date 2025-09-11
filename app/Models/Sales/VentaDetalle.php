<?php

namespace App\Models\Sales;

use App\Models\Inventory\Producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class VentaDetalle extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'precio_costo',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal',
        'impuesto_monto',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio_unitario' => 'decimal:4',
        'precio_costo' => 'decimal:4',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'impuesto_monto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Accessors
    public function getPrecioFinalAttribute(): float
    {
        return $this->precio_unitario - ($this->precio_unitario * $this->descuento_porcentaje / 100);
    }

    public function getMargenUnitarioAttribute(): float
    {
        if ($this->precio_costo > 0) {
            return $this->precio_final - $this->precio_costo;
        }
        return 0;
    }

    public function getMargenPorcentajeAttribute(): float
    {
        if ($this->precio_costo > 0) {
            return (($this->precio_final - $this->precio_costo) / $this->precio_costo) * 100;
        }
        return 0;
    }

    public function getGananciaTotalAttribute(): float
    {
        return $this->margen_unitario * $this->cantidad;
    }

    public function getDescuentoTotalAttribute(): float
    {
        return $this->descuento_monto;
    }

    public function getValorSinDescuentoAttribute(): float
    {
        return $this->cantidad * $this->precio_unitario;
    }

    public function getAhorroClienteAttribute(): float
    {
        return $this->valor_sin_descuento - $this->subtotal;
    }

    // Scopes
    public function scopeByProducto(Builder $query, int $productoId): Builder
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopeConDescuento(Builder $query): Builder
    {
        return $query->where('descuento_porcentaje', '>', 0);
    }

    public function scopeRentables(Builder $query): Builder
    {
        return $query->whereRaw('(precio_unitario - precio_costo) * cantidad > 0');
    }

    public function scopeByVenta(Builder $query, int $ventaId): Builder
    {
        return $query->where('venta_id', $ventaId);
    }

    public function scopeTopProductos(Builder $query, int $limit = 10): Builder
    {
        return $query->select('producto_id')
            ->selectRaw('SUM(cantidad) as total_vendido')
            ->selectRaw('SUM(total) as ingresos_total')
            ->selectRaw('COUNT(*) as numero_ventas')
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->limit($limit);
    }

    // Métodos de negocio
    public function calcularTotales(float $impuestoPorcentaje = 0): self
    {
        $subtotalSinDescuento = $this->cantidad * $this->precio_unitario;
        $descuentoMonto = ($subtotalSinDescuento * $this->descuento_porcentaje) / 100;
        $subtotal = $subtotalSinDescuento - $descuentoMonto;
        $impuestoMonto = ($subtotal * $impuestoPorcentaje) / 100;
        $total = $subtotal + $impuestoMonto;

        $this->update([
            'descuento_monto' => $descuentoMonto,
            'subtotal' => $subtotal,
            'impuesto_monto' => $impuestoMonto,
            'total' => $total,
        ]);

        return $this;
    }

    public function actualizarCantidad(float $nuevaCantidad): self
    {
        $this->update(['cantidad' => $nuevaCantidad]);
        $this->calcularTotales($this->venta->impuesto_porcentaje);

        return $this;
    }

    public function actualizarPrecio(float $nuevoPrecio): self
    {
        $this->update(['precio_unitario' => $nuevoPrecio]);
        $this->calcularTotales($this->venta->impuesto_porcentaje);

        return $this;
    }

    public function aplicarDescuento(float $porcentaje): self
    {
        $this->update(['descuento_porcentaje' => $porcentaje]);
        $this->calcularTotales($this->venta->impuesto_porcentaje);

        return $this;
    }

    public function esRentable(): bool
    {
        return $this->ganancia_total > 0;
    }

    public function tieneDescuento(): bool
    {
        return $this->descuento_porcentaje > 0;
    }

    public function esMayorDescuento(float $porcentaje): bool
    {
        return $this->descuento_porcentaje > $porcentaje;
    }

    public function esVentaGrande(float $cantidad = 10): bool
    {
        return $this->cantidad >= $cantidad;
    }

    public function duplicar(int $nuevaVentaId): self
    {
        $nuevoDetalle = $this->replicate();
        $nuevoDetalle->venta_id = $nuevaVentaId;
        $nuevoDetalle->save();

        return $nuevoDetalle;
    }

    // Métodos de validación
    public function validarStock(): bool
    {
        if (!$this->producto->maneja_stock) {
            return true;
        }

        $stockDisponible = $this->producto->obtenerStock($this->venta->sucursal_id);
        return $stockDisponible >= $this->cantidad;
    }

    public function validarPrecio(): bool
    {
        // Validar que el precio no sea menor al costo (con tolerancia)
        return $this->precio_unitario >= ($this->precio_costo * 0.8);
    }

    public function validarDescuento(float $descuentoMaximo = 50): bool
    {
        return $this->descuento_porcentaje <= $descuentoMaximo;
    }

    // Métodos estáticos de utilidad
    public static function ventaPromedioPorProducto(int $productoId): float
    {
        return self::byProducto($productoId)->avg('precio_unitario') ?? 0;
    }

    public static function cantidadTotalVendida(int $productoId): float
    {
        return self::byProducto($productoId)->sum('cantidad') ?? 0;
    }

    public static function ingresosTotales(int $productoId): float
    {
        return self::byProducto($productoId)->sum('total') ?? 0;
    }
}
