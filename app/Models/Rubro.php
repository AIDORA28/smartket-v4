<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rubro extends Model
{
    protected $fillable = [
        'nombre',
        'modulos_default_json',
        'activo'
    ];

    protected $casts = [
        'modulos_default_json' => 'array',
        'activo' => 'boolean',
    ];

    // Relationships
    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_rubros')
            ->withPivot('es_principal', 'configuracion_json')
            ->withTimestamps();
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->activo;
    }

    public function getModulosDefault(): array
    {
        return $this->modulos_default_json ?? [];
    }
}
