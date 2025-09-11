<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(PlanAddon::class, 'empresa_addons')
            ->withPivot('cantidad_contratada', 'precio_unitario', 'precio_total', 'fecha_inicio', 'fecha_fin', 'activo', 'configuracion_json')
            ->withTimestamps();
    }

    public function empresaAddons(): HasMany
    {
        return $this->hasMany(EmpresaAddon::class);
    }

    // Fase 3: Company & Branch Management Relations
    public function settings(): HasOne
    {
        return $this->hasOne(EmpresaSettings::class);
    }

    public function branding(): HasOne
    {
        return $this->hasOne(OrganizationBranding::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(EmpresaAnalytics::class);
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

    /**
     * Scopes útiles
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeConPlan($query, int $planId)
    {
        return $query->where('plan_id', $planId);
    }

    public function scopeConRuc($query)
    {
        return $query->where('tiene_ruc', true);
    }

    /**
     * Métodos helper adicionales
     */
    public function isActive(): bool
    {
        return $this->activa;
    }

    public function hasRuc(): bool
    {
        return $this->tiene_ruc;
    }

    public function canCreateSucursales(): bool
    {
        return $this->sucursales_enabled;
    }

    public function allowsNegativeStock(): bool
    {
        return $this->allow_negative_stock;
    }

    public function includesIgvInPrice(): bool
    {
        return $this->precio_incluye_igv;
    }

    /**
     * Obtener límites del plan
     */
    public function getMaxUsers(): int
    {
        return $this->plan->max_usuarios ?? 0;
    }

    public function getMaxSucursales(): int
    {
        return $this->plan->max_sucursales ?? 0;
    }

    public function getMaxProducts(): int
    {
        return $this->plan->max_productos ?? 0;
    }

    /**
     * Verificar límites
     */
    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->getMaxUsers();
    }

    public function canAddSucursal(): bool
    {
        return $this->sucursales()->count() < $this->getMaxSucursales();
    }

    /**
     * Obtener addons activos
     */
    public function getActiveAddons()
    {
        return $this->empresaAddons()
            ->activos()
            ->vigentes()
            ->with('planAddon')
            ->get();
    }
}

