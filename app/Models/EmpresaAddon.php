<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EmpresaAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'plan_addon_id',
        'cantidad',
        'fecha_inicio',
        'fecha_fin',
        'precio_pagado',
        'periodo',
        'activo',
        'configuracion_json',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'precio_pagado' => 'decimal:2',
        'activo' => 'boolean',
        'configuracion_json' => 'array',
    ];

    /**
     * Relación con Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación con PlanAddon
     */
    public function planAddon(): BelongsTo
    {
        return $this->belongsTo(PlanAddon::class);
    }

    /**
     * Scope para obtener solo add-ons activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para obtener add-ons vigentes
     */
    public function scopeVigentes($query)
    {
        return $query->where('activo', true)
                    ->whereDate('fecha_inicio', '<=', now())
                    ->whereDate('fecha_fin', '>=', now());
    }

    /**
     * Verificar si el addon está vigente
     */
    public function estaVigente(): bool
    {
        return $this->activo && 
               Carbon::parse($this->fecha_inicio)->lte(now()) && 
               Carbon::parse($this->fecha_fin)->gte(now());
    }

    /**
     * Obtener días restantes del addon
     */
    public function getDiasRestantes(): int
    {
        if (!$this->estaVigente()) {
            return 0;
        }

        return Carbon::parse($this->fecha_fin)->diffInDays(now());
    }

    /**
     * Verificar si está próximo a vencer (menos de 30 días)
     */
    public function proximoAVencer(): bool
    {
        return $this->estaVigente() && $this->getDiasRestantes() <= 30;
    }
}
