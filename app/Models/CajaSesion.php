<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class CajaSesion extends Model
{
    use HasFactory;

    protected $table = 'caja_sesiones';

    protected $fillable = [
        'empresa_id',
        'caja_id',
        'user_apertura_id',
        'user_cierre_id',
        'codigo',
        'apertura_at',
        'cierre_at',
        'monto_inicial',
        'monto_ingresos',
        'monto_retiros',
        'monto_ventas_efectivo',
        'monto_declarado_cierre',
        'diferencia',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'apertura_at' => 'datetime',
        'cierre_at' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_ingresos' => 'decimal:2',
        'monto_retiros' => 'decimal:2',
        'monto_ventas_efectivo' => 'decimal:2',
        'monto_declarado_cierre' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    /**
     * Relación con empresa (multi-tenant)
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación con caja
     */
    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    /**
     * Usuario que abrió la sesión
     */
    public function usuarioApertura(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_apertura_id');
    }

    /**
     * Usuario que cerró la sesión
     */
    public function usuarioCierre(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_cierre_id');
    }

    /**
     * Movimientos de la sesión
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(CajaMovimiento::class);
    }

    /**
     * Pagos de ventas en efectivo de esta sesión
     */
    public function pagosEfectivo(): HasMany
    {
        return $this->hasMany(VentaPago::class, 'caja_sesion_id');
    }

    /**
     * Verificar si la sesión está abierta
     */
    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }

    /**
     * Calcular el monto total que debería haber en caja
     */
    public function getMontoCalculadoAttribute(): float
    {
        return $this->monto_inicial + 
               $this->monto_ingresos - 
               $this->monto_retiros + 
               $this->monto_ventas_efectivo;
    }

    /**
     * Generar código de sesión
     */
    public static function generarCodigo(int $cajaId): string
    {
        $fecha = now()->format('Ymd');
        $count = self::where('caja_id', $cajaId)
            ->whereDate('apertura_at', now())
            ->count() + 1;
        
        return "CX-{$fecha}-" . str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Actualizar monto de ventas en efectivo
     */
    public function actualizarVentasEfectivo(): void
    {
        $total = $this->pagosEfectivo()
            ->join('ventas', 'venta_pagos.venta_id', '=', 'ventas.id')
            ->where('ventas.estado', '!=', 'anulada')
            ->sum('venta_pagos.monto');

        $this->update(['monto_ventas_efectivo' => $total]);
    }
}

