<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recepcion extends Model
{
    protected $table = 'recepciones';

    protected $fillable = [
        'empresa_id', 'compra_id', 'sucursal_id', 'user_id',
        'fecha_recepcion', 'estado', 'observaciones'
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime'
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
