<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo',
        'descripcion',
        'color',
        'icono',
        'activa',
        'productos_count'
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->activa;
    }

    public function updateProductosCount(): void
    {
        $this->update(['productos_count' => $this->productos()->count()]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('activa', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('activa', false);
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeWithProductosCount($query)
    {
        return $query->withCount('productos');
    }
}
