<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    
    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo_interno',
        'direccion',
        'activa'
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Fase 3: Branch Management Relations
    public function settings(): HasOne
    {
        return $this->hasOne(SucursalSettings::class);
    }

    public function performance(): HasMany
    {
        return $this->hasMany(SucursalPerformance::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(SucursalTransfer::class, 'from_sucursal_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(SucursalTransfer::class, 'to_sucursal_id');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->activa;
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('activa', true);
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}

