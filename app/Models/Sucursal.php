<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
