<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    protected $fillable = [
        'empresa_id', 'proveedor_id', 'sucursal_destino_id', 'user_id',
        'fecha', 'numero_doc', 'tipo_doc', 'estado', 'total', 'moneda', 'observaciones'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2'
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function sucursalDestino(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CompraItem::class);
    }

    public function recepciones(): HasMany
    {
        return $this->hasMany(Recepcion::class);
    }

    public function calcularTotal(): void
    {
        $this->total = $this->items()->sum('subtotal');
        $this->save();
    }
}
