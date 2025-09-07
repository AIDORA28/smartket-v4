<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Empresa extends Model
{
    protected $fillable = [
        'nombre',
        'logo',
        'ruc',
        'tiene_ruc',
        'plan_id',
        'features_json',
        'sucursales_enabled',
        'sucursales_count',
        'allow_negative_stock',
        'precio_incluye_igv',
        'timezone',
        'connection_name',
        'activa'
    ];

    protected $casts = [
        'features_json' => 'array',
        'tiene_ruc' => 'boolean',
        'sucursales_enabled' => 'boolean',
        'allow_negative_stock' => 'boolean',
        'precio_incluye_igv' => 'boolean',
        'activa' => 'boolean',
    ];

    // Relationships
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class);
    }

    public function featureFlags(): HasMany
    {
        return $this->hasMany(FeatureFlag::class);
    }

    public function rubros(): BelongsToMany
    {
        return $this->belongsToMany(Rubro::class, 'empresa_rubros')
            ->withPivot('es_principal', 'configuracion_json')
            ->withTimestamps();
    }

    // Helper methods
    public function hasFeature(string $feature): bool
    {
        return $this->featureFlags()
            ->where('feature_key', $feature)
            ->where('enabled', true)
            ->exists();
    }

    public function getSucursalPrincipal()
    {
        return $this->sucursales()->where('activa', true)->first();
    }

    public function getRubroPrincipal()
    {
        return $this->rubros()->wherePivot('es_principal', 1)->first();
    }
}
