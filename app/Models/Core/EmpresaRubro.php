<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Pivot EmpresaRubro
 * 
 * Gestiona la relación many-to-many entre empresas y rubros.
 * Permite configuraciones específicas por rubro para cada empresa.
 */
class EmpresaRubro extends Model
{
    protected $table = 'empresa_rubros';

    protected $fillable = [
        'empresa_id',
        'rubro_id',
        'es_principal',
        'configuracion_json'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
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
     * Relación con rubro
     */
    public function rubro(): BelongsTo
    {
        return $this->belongsTo(Rubro::class);
    }

    /**
     * Scope para obtener solo rubros principales
     */
    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }

    /**
     * Scope para una empresa específica
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Obtener configuración específica
     */
    public function getConfiguracion(string $key, $default = null)
    {
        return $this->configuracion_json[$key] ?? $default;
    }

    /**
     * Verificar si es el rubro principal
     */
    public function isPrincipal(): bool
    {
        return $this->es_principal;
    }
}

