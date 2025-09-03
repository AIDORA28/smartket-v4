<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'documento_tipo',
        'documento_numero',
        'telefono',
        'email',
        'direccion',
        'contacto_json'
    ];

    protected $casts = [
        'contacto_json' => 'array'
    ];

    // =====================================================================
    // RELACIONES
    // =====================================================================

    /**
     * Proveedor pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Proveedor tiene muchas compras
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    // =====================================================================
    // SCOPES
    // =====================================================================

    /**
     * Scope por empresa
     */
    public function scopeEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope buscar por nombre o documento
     */
    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('documento_numero', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    /**
     * Scope por tipo de documento
     */
    public function scopeTipoDocumento(Builder $query, string $tipo): Builder
    {
        return $query->where('documento_tipo', $tipo);
    }

    // =====================================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================================

    /**
     * Obtener el nombre completo del proveedor
     */
    public function getNombreCompletoAttribute(): string
    {
        if ($this->documento_numero) {
            return "{$this->nombre} ({$this->documento_numero})";
        }
        return $this->nombre;
    }

    /**
     * Verificar si tiene compras
     */
    public function tieneCompras(): bool
    {
        return $this->compras()->exists();
    }

    /**
     * Obtener total de compras
     */
    public function getTotalCompras(): float
    {
        return $this->compras()
            ->where('estado', '!=', 'anulada')
            ->sum('total');
    }

    /**
     * Obtener cantidad de compras
     */
    public function getCantidadCompras(): int
    {
        return $this->compras()
            ->where('estado', '!=', 'anulada')
            ->count();
    }

    /**
     * Obtener información de contacto adicional
     */
    public function getContactoInfo(string $campo): ?string
    {
        return $this->contacto_json[$campo] ?? null;
    }

    /**
     * Establecer información de contacto adicional
     */
    public function setContactoInfo(string $campo, string $valor): void
    {
        $contacto = $this->contacto_json ?: [];
        $contacto[$campo] = $valor;
        $this->contacto_json = $contacto;
    }
}
