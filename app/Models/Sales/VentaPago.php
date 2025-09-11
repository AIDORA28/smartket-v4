<?php

namespace App\Models\Sales;

use App\Models\Core\MetodoPago;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VentaPago extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'venta_id',
        'metodo_pago_id',
        'monto',
        'referencia',
        'fecha_pago',
        'estado',
        'comision_porcentaje',
        'comision_monto',
        'monto_neto',
        'observaciones',
        'extras_json',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
        'comision_porcentaje' => 'decimal:2',
        'comision_monto' => 'decimal:2',
        'monto_neto' => 'decimal:2',
        'extras_json' => 'array',
    ];

    // Constantes
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_CONFIRMADO = 'confirmado';
    const ESTADO_ANULADO = 'anulado';
    const ESTADO_DEVUELTO = 'devuelto';

    const ESTADOS = [
        self::ESTADO_PENDIENTE => 'Pendiente',
        self::ESTADO_CONFIRMADO => 'Confirmado',
        self::ESTADO_ANULADO => 'Anulado',
        self::ESTADO_DEVUELTO => 'Devuelto',
    ];

    // Relaciones
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class);
    }

    // Accessors
    public function getEsEfectivoAttribute(): bool
    {
        return $this->metodoPago && $this->metodoPago->tipo === 'efectivo';
    }

    public function getEsTarjetaAttribute(): bool
    {
        return $this->metodoPago && in_array($this->metodoPago->tipo, ['tarjeta_credito', 'tarjeta_debito']);
    }

    public function getEsTransferenciaAttribute(): bool
    {
        return $this->metodoPago && $this->metodoPago->tipo === 'transferencia';
    }

    public function getEsDigitalAttribute(): bool
    {
        return $this->metodoPago && in_array($this->metodoPago->tipo, ['yape', 'plin', 'billetera_digital']);
    }

    public function getRequiereReferenciaAttribute(): bool
    {
        return !$this->es_efectivo;
    }

    public function getReferenciaFormateadaAttribute(): string
    {
        if (!$this->referencia) {
            return 'Sin referencia';
        }

        // Formatear según el tipo de pago
        if ($this->es_tarjeta && strlen($this->referencia) >= 4) {
            return '**** **** **** ' . substr($this->referencia, -4);
        }

        return $this->referencia;
    }

    public function getEstadoFormateadoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getEsPendienteAttribute(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function getEsConfirmadoAttribute(): bool
    {
        return $this->estado === self::ESTADO_CONFIRMADO;
    }

    public function getEsAnuladoAttribute(): bool
    {
        return $this->estado === self::ESTADO_ANULADO;
    }

    public function getMontoConComisionAttribute(): float
    {
        return $this->monto + $this->comision_monto;
    }

    // Scopes
    public function scopeByEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }

    public function scopeByMetodoPago(Builder $query, int $metodoPagoId): Builder
    {
        return $query->where('metodo_pago_id', $metodoPagoId);
    }

    public function scopeConfirmados(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_CONFIRMADO);
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeAnulados(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_ANULADO);
    }

    public function scopeEfectivo(Builder $query): Builder
    {
        return $query->whereHas('metodoPago', function ($q) {
            $q->where('tipo', 'efectivo');
        });
    }

    public function scopeTarjetas(Builder $query): Builder
    {
        return $query->whereHas('metodoPago', function ($q) {
            $q->whereIn('tipo', ['tarjeta_credito', 'tarjeta_debito']);
        });
    }

    public function scopeDigitales(Builder $query): Builder
    {
        return $query->whereHas('metodoPago', function ($q) {
            $q->whereIn('tipo', ['yape', 'plin', 'billetera_digital']);
        });
    }

    public function scopeByFecha(Builder $query, $fechaInicio, $fechaFin = null): Builder
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_pago', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_pago', $fechaInicio);
    }

    public function scopeHoy(Builder $query): Builder
    {
        return $query->whereDate('fecha_pago', today());
    }

    public function scopeByVenta(Builder $query, int $ventaId): Builder
    {
        return $query->where('venta_id', $ventaId);
    }

    // Métodos de negocio
    public function calcularComision(): self
    {
        if ($this->metodoPago) {
            $comisionPorcentaje = $this->metodoPago->comision_porcentaje ?? 0;
            $comisionMonto = ($this->monto * $comisionPorcentaje) / 100;
            $montoNeto = $this->monto - $comisionMonto;

            $this->update([
                'comision_porcentaje' => $comisionPorcentaje,
                'comision_monto' => $comisionMonto,
                'monto_neto' => $montoNeto,
            ]);
        }

        return $this;
    }

    public function confirmar(string $observaciones = null): self
    {
        $updateData = [
            'estado' => self::ESTADO_CONFIRMADO,
            'fecha_pago' => now(),
        ];

        if ($observaciones) {
            $updateData['observaciones'] = $observaciones;
        }

        $this->update($updateData);

        return $this;
    }

    public function anular(string $motivo = null): self
    {
        $extras = $this->extras_json ?? [];
        $extras['motivo_anulacion'] = $motivo;
        $extras['fecha_anulacion'] = now()->toISOString();
        $extras['usuario_anulacion'] = Auth::id();

        $this->update([
            'estado' => self::ESTADO_ANULADO,
            'extras_json' => $extras,
        ]);

        return $this;
    }

    public function devolver(string $motivo = null): self
    {
        $extras = $this->extras_json ?? [];
        $extras['motivo_devolucion'] = $motivo;
        $extras['fecha_devolucion'] = now()->toISOString();
        $extras['usuario_devolucion'] = Auth::id();

        $this->update([
            'estado' => self::ESTADO_DEVUELTO,
            'extras_json' => $extras,
        ]);

        return $this;
    }

    public function procesar(): bool
    {
        try {
            // Calcular comisión automáticamente
            $this->calcularComision();

            // Procesar según el tipo de pago
            switch ($this->metodoPago->tipo) {
                case 'efectivo':
                    return $this->procesarEfectivo();
                
                case 'tarjeta_credito':
                case 'tarjeta_debito':
                    return $this->procesarTarjeta();
                
                case 'transferencia':
                    return $this->procesarTransferencia();
                
                case 'yape':
                case 'plin':
                case 'billetera_digital':
                    return $this->procesarDigital();
                
                default:
                    return $this->procesarOtro();
            }
        } catch (\Exception $e) {
            $this->registrarError($e->getMessage());
            return false;
        }
    }

    private function procesarEfectivo(): bool
    {
        // Para efectivo, confirmamos inmediatamente
        $this->confirmar('Pago en efectivo procesado automáticamente');
        return true;
    }

    private function procesarTarjeta(): bool
    {
        // Validar que tenga referencia para tarjetas
        if (!$this->validarReferenciaTarjeta()) {
            $this->registrarError('Referencia de tarjeta inválida');
            return false;
        }

        // Aquí se integraría con el procesador de tarjetas (Visa, Mastercard, etc.)
        // Por ahora confirmamos automáticamente
        $this->confirmar('Pago con tarjeta procesado');
        return true;
    }

    private function procesarTransferencia(): bool
    {
        // Para transferencias, marcar como pendiente hasta confirmar
        $this->update(['estado' => self::ESTADO_PENDIENTE]);
        return true;
    }

    private function procesarDigital(): bool
    {
        // Para pagos digitales, validar referencia
        if (!$this->referencia) {
            $this->registrarError('Se requiere número de operación');
            return false;
        }

        // Marcar como pendiente hasta validar con la plataforma
        $this->update(['estado' => self::ESTADO_PENDIENTE]);
        return true;
    }

    private function procesarOtro(): bool
    {
        // Para otros métodos, marcar como pendiente
        $this->update(['estado' => self::ESTADO_PENDIENTE]);
        return true;
    }

    private function validarReferenciaTarjeta(): bool
    {
        return !empty($this->referencia) && strlen($this->referencia) >= 4;
    }

    private function registrarError(string $mensaje): void
    {
        $extras = $this->extras_json ?? [];
        $extras['errores'] = $extras['errores'] ?? [];
        $extras['errores'][] = [
            'mensaje' => $mensaje,
            'fecha' => now()->toISOString(),
            'usuario' => Auth::id(),
        ];

        $this->update(['extras_json' => $extras]);
    }

    // Métodos de validación
    public function validarMonto(): bool
    {
        return $this->monto > 0;
    }

    public function validarMetodoPago(): bool
    {
        return $this->metodoPago && $this->metodoPago->activo;
    }

    public function validarReferencia(): bool
    {
        // Si no requiere referencia (efectivo), siempre es válido
        if (!$this->requiere_referencia) {
            return true;
        }

        // Si requiere referencia, debe tenerla
        return !empty($this->referencia);
    }

    public function puedeAnular(): bool
    {
        return in_array($this->estado, [self::ESTADO_PENDIENTE, self::ESTADO_CONFIRMADO]);
    }

    public function puedeDevolver(): bool
    {
        return $this->estado === self::ESTADO_CONFIRMADO && $this->es_efectivo;
    }

    // Métodos estáticos de utilidad
    public static function totalDelDia(string $metodoPago = null): float
    {
        $query = self::hoy()->confirmados();
        
        if ($metodoPago) {
            $query->whereHas('metodoPago', function ($q) use ($metodoPago) {
                $q->where('tipo', $metodoPago);
            });
        }

        return $query->sum('monto');
    }

    public static function resumenDelDia(): array
    {
        return [
            'efectivo' => self::totalDelDia('efectivo'),
            'tarjetas' => self::hoy()->confirmados()->tarjetas()->sum('monto'),
            'digitales' => self::hoy()->confirmados()->digitales()->sum('monto'),
            'transferencias' => self::totalDelDia('transferencia'),
            'total' => self::totalDelDia(),
            'comisiones' => self::hoy()->confirmados()->sum('comision_monto'),
        ];
    }

    public static function pagosPendientes(): Builder
    {
        return self::pendientes()->with(['venta', 'metodoPago']);
    }
}
