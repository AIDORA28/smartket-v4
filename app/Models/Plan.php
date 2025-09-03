<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'planes';
    
    protected $fillable = [
        'nombre',
        'max_usuarios',
        'max_sucursales', 
        'max_productos',
        'limites_json',
        'activo',
        'grace_percent'
    ];

    protected $casts = [
        'limites_json' => 'array',
        'activo' => 'boolean',
    ];

    // Relationships
    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
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
}
