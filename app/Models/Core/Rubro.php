<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Rubro
 * 
 * Gestiona los rubros o sectores empresariales.
 * Una empresa puede pertenecer a múltiples rubros.
 */
class Rubro extends Model
{
    protected $fillable = [
        'nombre',
        'modulos_default_json',
        'activo'
    ];

    protected $casts = [
        'modulos_default_json' => 'array',
        'activo' => 'boolean'
    ];

    /**
     * Relación many-to-many con empresas
     */
    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_rubros')
            ->withPivot('es_principal', 'configuracion_json')
            ->withTimestamps();
    }

    /**
     * Relación con la tabla pivot empresa_rubros
     */
    public function empresaRubros(): HasMany
    {
        return $this->hasMany(EmpresaRubro::class);
    }

    /**
     * Scope para obtener solo rubros activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para ordenar por nombre
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('nombre', 'asc');
    }

    /**
     * Obtener empresas que tienen este rubro como principal
     */
    public function empresasPrincipales()
    {
        return $this->empresas()->wherePivot('es_principal', true);
    }

    /**
     * Contar empresas asociadas a este rubro
     */
    public function getCountEmpresasAttribute(): int
    {
        return $this->empresas()->count();
    }

    /**
     * Obtener módulos por defecto
     */
    public function getModulosDefault(): array
    {
        return $this->modulos_default_json ?? [];
    }

    /**
     * Verificar si el rubro está activo
     */
    public function isActive(): bool
    {
        return $this->activo;
    }
}

