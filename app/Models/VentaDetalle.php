<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaDetalle extends Model
{
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
        'precio_unitario' => 'decimal:2',
        'precio_costo' => 'decimal:2',
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

    public function getMargenAttribute(): float
    {
        if ($this->precio_costo > 0) {
            return (($this->precio_final - $this->precio_costo) / $this->precio_costo) * 100;
        }
        return 0;
    }

    public function getGananciaAttribute(): float
    {
        return ($this->precio_final - $this->precio_costo) * $this->cantidad;
    }

    public function getDescuentoTotalAttribute(): float
    {
        return $this->descuento_monto * $this->cantidad;
    }

    // Métodos de negocio
    public function calcularTotales(float $impuestoPorcentaje = 0): void
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
    }

    public function actualizarCantidad(float $nuevaCantidad): void
    {
        $this->update(['cantidad' => $nuevaCantidad]);
        $this->calcularTotales($this->venta->impuesto_porcentaje);
    }

    public function actualizarPrecio(float $nuevoPrecio): void
    {
        $this->update(['precio_unitario' => $nuevoPrecio]);
        $this->calcularTotales($this->venta->impuesto_porcentaje);
    }

    public function aplicarDescuento(float $porcentaje): void
    {
        $this->update(['descuento_porcentaje' => $porcentaje]);
        $this->calcularTotales($this->venta->impuesto_porcentaje);
    }

    public function esRentable(): bool
    {
        return $this->ganancia > 0;
    }

    public function tieneDescuento(): bool
    {
        return $this->descuento_porcentaje > 0;
    }

    // Scopes
    public function scopeByProducto($query, int $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopeConDescuento($query)
    {
        return $query->where('descuento_porcentaje', '>', 0);
    }

    public function scopeRentables($query)
    {
        return $query->whereRaw('(precio_unitario - precio_costo) * cantidad > 0');
    }
}
