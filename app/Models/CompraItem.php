<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraItem extends Model
{
    protected $fillable = [
        'empresa_id', 'compra_id', 'producto_id', 'lote_id',
        'cantidad', 'costo_unitario', 'descuento_pct', 'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'costo_unitario' => 'decimal:4',
        'descuento_pct' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function calcularSubtotal(): void
    {
        $descuento = ($this->cantidad * $this->costo_unitario) * ($this->descuento_pct / 100);
        $this->subtotal = ($this->cantidad * $this->costo_unitario) - $descuento;
        $this->save();
    }
}
