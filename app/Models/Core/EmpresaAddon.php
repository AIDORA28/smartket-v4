<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Pivot EmpresaAddon
 * 
 * Gestiona la relación many-to-many entre empresas y plan addons.
 * Permite gestionar addons específicos contratados por cada empresa.
 */
class EmpresaAddon extends Model
{
    protected $table = 'empresa_addons';

    protected $fillable = [
        'empresa_id',
        'plan_addon_id',
        'cantidad',
        'fecha_inicio',
        'fecha_fin',
        'precio_pagado',
        'periodo',
        'activo',
        'configuracion_json'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_pagado' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'configuracion_json' => 'array'
    ];

    /**
     * Relación con empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación con plan addon
     */
    public function planAddon(): BelongsTo
    {
        return $this->belongsTo(PlanAddon::class);
    }

    /**
     * Scope para obtener solo addons activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para una empresa específica
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para addons vigentes
     */
    public function scopeVigentes($query)
    {
        $today = now()->toDateString();
        return $query->where('fecha_inicio', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('fecha_fin')
                  ->orWhere('fecha_fin', '>=', $today);
            });
    }

    /**
     * Verificar si el addon está vigente
     */
    public function isVigente(): bool
    {
        $today = now()->toDateString();
        return $this->fecha_inicio <= $today && 
               ($this->fecha_fin === null || $this->fecha_fin >= $today);
    }

    /**
     * Verificar si está activo
     */
    public function isActive(): bool
    {
        return $this->activo;
    }

    /**
     * Obtener precio pagado
     */
    public function getPrecioPagado(): float
    {
        return $this->precio_pagado;
    }

    /**
     * Verificar si es periodo anual
     */
    public function isAnual(): bool
    {
        return $this->periodo === 'anual';
    }

    /**
     * Verificar si es periodo mensual
     */
    public function isMensual(): bool
    {
        return $this->periodo === 'mensual';
    }

    /**
     * Obtener configuración específica
     */
    public function getConfiguracion(string $key, $default = null)
    {
        return $this->configuracion_json[$key] ?? $default;
    }
}

