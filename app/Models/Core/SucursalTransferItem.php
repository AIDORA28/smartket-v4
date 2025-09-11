<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SucursalTransferItem extends Model
{
    protected $table = 'sucursal_transfer_items';

    protected $fillable = [
        'transfer_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'lote_id',
        'notas'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'precio_total' => 'decimal:2',
    ];

    // Relationships
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(SucursalTransfer::class, 'transfer_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Inventory\Producto::class);
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lote::class);
    }

    // Helper methods
    public function getTotalFormattedPrice(): string
    {
        return 'S/ ' . number_format($this->precio_total, 2);
    }

    public function getUnitFormattedPrice(): string
    {
        return 'S/ ' . number_format($this->precio_unitario, 2);
    }

    // Calculate total based on quantity and unit price
    public function calculateTotal(): void
    {
        $this->precio_total = $this->cantidad * $this->precio_unitario;
    }

    // Auto-calculate total when saving
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            $model->calculateTotal();
        });
    }
}
