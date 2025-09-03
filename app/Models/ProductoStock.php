<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoStock extends Model
{
    protected $fillable = [
        'empresa_id',
        'producto_id',
        'sucursal_id',
        'cantidad_actual',
        'cantidad_reservada',
        'cantidad_disponible',
        'costo_promedio',
        'ultimo_movimiento'
    ];

    protected $casts = [
        'cantidad_actual' => 'decimal:2',
        'cantidad_reservada' => 'decimal:2',
        'cantidad_disponible' => 'decimal:2',
        'costo_promedio' => 'decimal:4',
        'ultimo_movimiento' => 'datetime',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Helper methods
    public function actualizarCantidadDisponible(): void
    {
        $this->update([
            'cantidad_disponible' => $this->cantidad_actual - $this->cantidad_reservada
        ]);
    }

    public function tieneStockSuficiente(float $cantidad): bool
    {
        return $this->cantidad_disponible >= $cantidad;
    }

    public function estaEnStockMinimo(): bool
    {
        return $this->cantidad_actual <= $this->producto->stock_minimo;
    }

    // Scopes
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeForSucursal($query, int $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeConStock($query)
    {
        return $query->where('cantidad_actual', '>', 0);
    }

    public function scopeStockBajo($query)
    {
        return $query->whereHas('producto', function ($q) {
            $q->whereRaw('cantidad_actual <= stock_minimo');
        });
    }
}
