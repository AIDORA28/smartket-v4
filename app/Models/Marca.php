<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marca extends Model
{
    use HasFactory;

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
        'productos_count' => 'integer'
    ];

    /**
     * Relación con empresa (multi-tenant)
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación con productos
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para marcas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
