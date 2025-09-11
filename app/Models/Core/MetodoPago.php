<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\EmpresaScope;

class MetodoPago extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'metodos_pago';

    protected static function booted(): void
    {
        static::addGlobalScope(new EmpresaScope);
    }

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
        'comision_porcentaje' => 'decimal:2',
        'comision_fija' => 'decimal:2',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    // Constantes
    const TIPO_EFECTIVO = 'efectivo';
    const TIPO_TARJETA = 'tarjeta';
    const TIPO_TRANSFERENCIA = 'transferencia';
    const TIPO_CREDITO = 'credito';
    const TIPO_DIGITAL = 'digital';

    const TIPOS = [
        self::TIPO_EFECTIVO => 'Efectivo',
        self::TIPO_TARJETA => 'Tarjeta',
        self::TIPO_TRANSFERENCIA => 'Transferencia',
        self::TIPO_CREDITO => 'Crédito',
        self::TIPO_DIGITAL => 'Digital',
    ];

    // Relaciones
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ventaPagos(): HasMany
    {
        return $this->hasMany(\App\Models\Sales\VentaPago::class);
    }

    // Scopes
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos(Builder $query): Builder
    {
        return $query->where('activo', false);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeEfectivo(Builder $query): Builder
    {
        return $query->where('tipo', self::TIPO_EFECTIVO);
    }

    public function scopeTarjeta(Builder $query): Builder
    {
        return $query->where('tipo', self::TIPO_TARJETA);
    }

    public function scopeCredito(Builder $query): Builder
    {
        return $query->where('tipo', self::TIPO_CREDITO);
    }

    public function scopeAfectaCaja(Builder $query): Builder
    {
        return $query->where('afecta_caja', true);
    }

    public function scopeNoAfectaCaja(Builder $query): Builder
    {
        return $query->where('afecta_caja', false);
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // Accessors
    public function getTipoFormateadoAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getTieneComisionAttribute(): bool
    {
        return $this->comision_porcentaje > 0 || $this->comision_fija > 0;
    }

    // Métodos de negocio
    public function calcularComision(float $monto): float
    {
        $comision = 0;

        // Comisión fija
        if ($this->comision_fija > 0) {
            $comision += $this->comision_fija;
        }

        // Comisión porcentual
        if ($this->comision_porcentaje > 0) {
            $comision += ($monto * $this->comision_porcentaje) / 100;
        }

        return round($comision, 2);
    }

    public function getMontoNeto(float $montoBruto): float
    {
        return $montoBruto - $this->calcularComision($montoBruto);
    }

    public function validarReferencia(?string $referencia): bool
    {
        if (!$this->requiere_referencia) {
            return true;
        }

        return !empty($referencia) && strlen(trim($referencia)) >= 3;
    }

    // Métodos estáticos de utilidad
    public static function metodosActivos(int $empresaId): \Illuminate\Database\Eloquent\Collection
    {
        return self::forEmpresa($empresaId)
            ->activos()
            ->ordenados()
            ->get();
    }

    public static function metodosEfectivo(int $empresaId): \Illuminate\Database\Eloquent\Collection
    {
        return self::forEmpresa($empresaId)
            ->activos()
            ->efectivo()
            ->ordenados()
            ->get();
    }

    public static function metodosPorTipo(int $empresaId, string $tipo): \Illuminate\Database\Eloquent\Collection
    {
        return self::forEmpresa($empresaId)
            ->activos()
            ->porTipo($tipo)
            ->ordenados()
            ->get();
    }

    public static function crearMetodosPorDefecto(int $empresaId): void
    {
        $metodosDefecto = [
            [
                'nombre' => 'Efectivo',
                'codigo' => 'EFE',
                'tipo' => self::TIPO_EFECTIVO,
                'requiere_referencia' => false,
                'afecta_caja' => true,
                'orden' => 1,
                'icono' => '💵',
            ],
            [
                'nombre' => 'Tarjeta de Débito',
                'codigo' => 'TDB',
                'tipo' => self::TIPO_TARJETA,
                'requiere_referencia' => true,
                'afecta_caja' => true,
                'orden' => 2,
                'icono' => '💳',
            ],
            [
                'nombre' => 'Tarjeta de Crédito',
                'codigo' => 'TCR',
                'tipo' => self::TIPO_TARJETA,
                'requiere_referencia' => true,
                'afecta_caja' => true,
                'comision_porcentaje' => 3.5,
                'orden' => 3,
                'icono' => '💳',
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'codigo' => 'TRF',
                'tipo' => self::TIPO_TRANSFERENCIA,
                'requiere_referencia' => true,
                'afecta_caja' => true,
                'orden' => 4,
                'icono' => '🏦',
            ],
            [
                'nombre' => 'Crédito',
                'codigo' => 'CRE',
                'tipo' => self::TIPO_CREDITO,
                'requiere_referencia' => false,
                'afecta_caja' => false,
                'orden' => 5,
                'icono' => '📋',
            ],
        ];

        foreach ($metodosDefecto as $metodo) {
            $metodo['empresa_id'] = $empresaId;
            self::firstOrCreate(
                ['empresa_id' => $empresaId, 'codigo' => $metodo['codigo']],
                $metodo
            );
        }
    }
}