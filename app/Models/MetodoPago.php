<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo',
        'tipo',
        'requiere_referencia',
        'afecta_caja',
        'comision_porcentaje',
        'comision_fija',
        'activo',
        'orden',
        'icono',
    ];

    protected $casts = [
        'requiere_referencia' => 'boolean',
        'afecta_caja' => 'boolean',
        'activo' => 'boolean',
        'comision_porcentaje' => 'decimal:2',
        'comision_fija' => 'decimal:2',
        'orden' => 'integer',
    ];

    // Constantes para tipos
    const TIPO_EFECTIVO = 'efectivo';
    const TIPO_TARJETA = 'tarjeta';
    const TIPO_TRANSFERENCIA = 'transferencia';
    const TIPO_CREDITO = 'credito';
    const TIPO_DIGITAL = 'digital';

    // Relaciones
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ventaPagos(): HasMany
    {
        return $this->hasMany(VentaPago::class);
    }

    // Scopes
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('activo', true)
                    ->where(function ($q) {
                        $q->whereNull('fecha_inicio')
                          ->orWhere('fecha_inicio', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>=', now());
                    });
    }

    public function scopeByTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // Accessors
    public function getComisionTotalAttribute(): float
    {
        return $this->comision_porcentaje + $this->comision_fija;
    }

    public function getTieneComisionAttribute(): bool
    {
        return $this->comision_porcentaje > 0 || $this->comision_fija > 0;
    }

    // Métodos de negocio
    public function calcularComision(float $monto): float
    {
        $comisionPorcentaje = ($monto * $this->comision_porcentaje) / 100;
        return $comisionPorcentaje + $this->comision_fija;
    }

    public function getMontoNeto(float $montoBruto): float
    {
        return $montoBruto - $this->calcularComision($montoBruto);
    }

    public static function getMetodosPorTipo(int $empresaId): array
    {
        return self::forEmpresa($empresaId)
            ->active()
            ->ordered()
            ->get()
            ->groupBy('tipo')
            ->toArray();
    }

    public static function getEfectivo(int $empresaId): ?self
    {
        return self::forEmpresa($empresaId)
            ->byTipo(self::TIPO_EFECTIVO)
            ->active()
            ->first();
    }
}
