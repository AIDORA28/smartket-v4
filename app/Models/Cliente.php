<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $fillable = [
        'empresa_id',
        'tipo_documento',
        'numero_documento',
        'nombre',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'genero',
        'es_empresa',
        'limite_credito',
        'credito_usado',
        'permite_credito',
        'descuento_porcentaje',
        'activo',
        'extras_json',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'es_empresa' => 'boolean',
        'permite_credito' => 'boolean',
        'activo' => 'boolean',
        'limite_credito' => 'decimal:2',
        'credito_usado' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'extras_json' => 'array',
    ];

    // Relaciones
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function ventasRecientes(): HasMany
    {
        return $this->hasMany(Venta::class)->latest('fecha_venta')->limit(5);
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

    public function scopeConCredito($query)
    {
        return $query->where('permite_credito', true)->where('limite_credito', '>', 0);
    }

    public function scopeSinCredito($query)
    {
        return $query->where('permite_credito', false)->orWhere('limite_credito', '<=', 0);
    }

    public function scopeByDocumento($query, string $tipoDocumento = null, string $numeroDocumento = null)
    {
        $query = $query->when($tipoDocumento, function($q) use ($tipoDocumento) {
            return $q->where('tipo_documento', $tipoDocumento);
        });

        return $query->when($numeroDocumento, function($q) use ($numeroDocumento) {
            return $q->where('numero_documento', 'like', "%{$numeroDocumento}%");
        });
    }

    public function scopeByNombre($query, string $nombre)
    {
        return $query->where('nombre', 'like', "%{$nombre}%");
    }

    // Accessors & Mutators
    public function getCreditoDisponibleAttribute(): float
    {
        return $this->limite_credito - $this->credito_usado;
    }

    public function getTieneCreditoDisponibleAttribute(): bool
    {
        return $this->permite_credito && $this->getCreditoDisponibleAttribute() > 0;
    }

    public function getDocumentoCompletoAttribute(): string
    {
        return "{$this->tipo_documento}: {$this->numero_documento}";
    }

    // Métodos de negocio
    public function puedeComprarACredito(float $monto): bool
    {
        if (!$this->permite_credito) {
            return false;
        }

        return ($this->credito_usado + $monto) <= $this->limite_credito;
    }

    public function actualizarCreditoUsado(): void
    {
        $creditoUsado = $this->ventas()
            ->whereIn('estado', ['pendiente'])
            ->where('total_pagado', '<', DB::raw('total'))
            ->sum(DB::raw('total - total_pagado'));

        $this->update(['credito_usado' => $creditoUsado]);
    }

    public function getTotalCompras(string $periodo = 'mes'): float
    {
        $query = $this->ventas()->where('estado', '!=', 'anulada');

        switch ($periodo) {
            case 'dia':
                $query->whereDate('fecha_venta', today());
                break;
            case 'semana':
                $query->whereBetween('fecha_venta', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'mes':
                $query->whereMonth('fecha_venta', now()->month)
                      ->whereYear('fecha_venta', now()->year);
                break;
            case 'año':
                $query->whereYear('fecha_venta', now()->year);
                break;
        }

        return $query->sum('total');
    }

    public function validarDocumento(): bool
    {
        switch ($this->tipo_documento) {
            case 'DNI':
                return preg_match('/^\d{8}$/', $this->numero_documento);
            case 'RUC':
                return preg_match('/^\d{11}$/', $this->numero_documento);
            case 'CE':
                return preg_match('/^\d{9}$/', $this->numero_documento);
            case 'PASAPORTE':
                return strlen($this->numero_documento) >= 6 && strlen($this->numero_documento) <= 12;
            default:
                return true;
        }
    }
}
