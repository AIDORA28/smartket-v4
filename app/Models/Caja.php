<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'nombre',
        'codigo',
        'tipo',
        'activa',
        'configuracion_json',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'configuracion_json' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(CajaSesion::class);
    }

    public function sesionActual(): HasMany
    {
        return $this->sesiones()->where('estado', 'abierta');
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true);
    }

    public function scopeEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeSucursal(Builder $query, int $sucursalId): Builder
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function tieneSesionAbierta(): bool
    {
        return $this->sesionActual()->exists();
    }

    public function getSesionAbierta(): ?CajaSesion
    {
        return $this->sesionActual()->first();
    }

    public static function generarCodigo(int $sucursalId): string
    {
        $count = self::where('sucursal_id', $sucursalId)->count() + 1;
        return "CJ-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
