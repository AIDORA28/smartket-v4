<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'planes';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_mensual',
        'precio_anual',
        'dias_prueba',
        'max_usuarios',
        'max_sucursales', 
        'max_productos',
        'max_rubros',
        'limites_json',
        'caracteristicas_json',
        'activo',
        'es_gratis',
        'es_visible',
        'orden_display',
        'grace_percent'
    ];

    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'precio_anual' => 'decimal:2',
        'dias_prueba' => 'integer',
        'max_usuarios' => 'integer',
        'max_sucursales' => 'integer',
        'max_productos' => 'integer',
        'max_rubros' => 'integer',
        'limites_json' => 'array',
        'caracteristicas_json' => 'array',
        'activo' => 'boolean',
        'es_gratis' => 'boolean',
        'es_visible' => 'boolean',
        'orden_display' => 'integer',
    ];

    // Relationships
    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(PlanAddon::class);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->activo;
    }

    public function getLimite(string $key, $default = null)
    {
        return $this->limites_json[$key] ?? $default;
    }

    /**
     * Scope para obtener solo planes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para obtener solo planes visibles
     */
    public function scopeVisibles($query)
    {
        return $query->where('es_visible', true);
    }

    /**
     * Scope para obtener planes ordenados para mostrar
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden_display', 'asc');
    }

    /**
     * Obtener el precio según el período
     */
    public function getPrecio(string $periodo = 'mensual'): float
    {
        return $periodo === 'anual' ? $this->precio_anual : $this->precio_mensual;
    }

    /**
     * Verificar si tiene descuento anual
     */
    public function tieneDescuentoAnual(): bool
    {
        return $this->precio_anual > 0 && ($this->precio_mensual * 12) > $this->precio_anual;
    }

    /**
     * Calcular porcentaje de descuento anual
     */
    public function getDescuentoAnual(): float
    {
        if (!$this->tieneDescuentoAnual()) {
            return 0;
        }

        $precioMensualAnualizado = $this->precio_mensual * 12;
        return round((($precioMensualAnualizado - $this->precio_anual) / $precioMensualAnualizado) * 100, 2);
    }

    /**
     * Obtener características como array
     */
    public function getCaracteristicas(): array
    {
        return $this->caracteristicas_json ?? [];
    }

    /**
     * Verificar si es plan gratuito
     */
    public function esGratis(): bool
    {
        return $this->es_gratis || ($this->precio_mensual == 0 && $this->precio_anual == 0);
    }
}

