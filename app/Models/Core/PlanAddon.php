<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'nombre',
        'descripcion',
        'precio_mensual',
        'precio_anual',
        'cantidad',
        'activo',
        'restricciones_json',
    ];

    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'precio_anual' => 'decimal:2',
        'cantidad' => 'integer',
        'activo' => 'boolean',
        'restricciones_json' => 'array',
    ];

    /**
     * Relación con empresa_addons
     */
    public function empresaAddons(): HasMany
    {
        return $this->hasMany(EmpresaAddon::class);
    }

    /**
     * Relación con plan (si fuera necesario)
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Scope para obtener solo add-ons activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para obtener add-ons por tipo
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
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
}
 
