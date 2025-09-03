<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CajaMovimiento extends Model
{
    use HasFactory;

    protected $table = 'caja_movimientos';
    
    public $timestamps = false;

    protected $fillable = [
        'empresa_id',
        'caja_sesion_id',
        'tipo',
        'monto',
        'concepto',
        'referencia_tipo',
        'referencia_id',
        'user_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con empresa (multi-tenant)
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación con sesión de caja
     */
    public function cajaSesion(): BelongsTo
    {
        return $this->belongsTo(CajaSesion::class);
    }

    /**
     * Usuario que registró el movimiento
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
