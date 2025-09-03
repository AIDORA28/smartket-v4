<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaPago extends Model
{
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
        return $this->metodoPago && $this->metodoPago->tipo === MetodoPago::TIPO_EFECTIVO;
    }

    public function getEsTarjetaAttribute(): bool
    {
        return $this->metodoPago && $this->metodoPago->tipo === MetodoPago::TIPO_TARJETA;
    }

    public function getEsTransferenciaAttribute(): bool
    {
        return $this->metodoPago && $this->metodoPago->tipo === MetodoPago::TIPO_TRANSFERENCIA;
    }

    public function getRequiereReferenciaAttribute(): bool
    {
        return !$this->es_efectivo;
    }

    public function getReferenciaFormateadaAttribute(): string
    {
        if ($this->referencia) {
            // Formatear según el tipo de pago
            if ($this->es_tarjeta) {
                return '**** **** **** ' . substr($this->referencia, -4);
            }
            return $this->referencia;
        }
        return 'Sin referencia';
    }

    // Métodos de negocio
    public function calcularComision(): void
    {
        if ($this->metodoPago) {
            $comisionPorcentaje = $this->metodoPago->comision_porcentaje;
            $comisionMonto = ($this->monto * $comisionPorcentaje) / 100;
            $montoNeto = $this->monto - $comisionMonto;

            $this->update([
                'comision_porcentaje' => $comisionPorcentaje,
                'comision_monto' => $comisionMonto,
                'monto_neto' => $montoNeto,
            ]);
        }
    }

    public function confirmar(): void
    {
        $this->update([
            'estado' => self::ESTADO_CONFIRMADO,
            'fecha_pago' => now(),
        ]);
    }

    public function anular(string $motivo = null): void
    {
        $extras = $this->extras_json ?? [];
        $extras['motivo_anulacion'] = $motivo;
        $extras['fecha_anulacion'] = now()->toISOString();

        $this->update([
            'estado' => self::ESTADO_ANULADO,
            'extras_json' => $extras,
        ]);
    }

    public function procesar(): bool
    {
        try {
            // Aquí se integrarían los procesadores de pago específicos
            switch ($this->metodoPago->tipo) {
                case MetodoPago::TIPO_EFECTIVO:
                    return $this->procesarEfectivo();
                
                case MetodoPago::TIPO_TARJETA:
                    return $this->procesarTarjeta();
                
                case MetodoPago::TIPO_TRANSFERENCIA:
                    return $this->procesarTransferencia();
                
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
        // Para efectivo, solo confirmamos
        $this->confirmar();
        return true;
    }

    private function procesarTarjeta(): bool
    {
        // Aquí se integraría con el procesador de tarjetas
        // Por ahora solo simulamos
        if ($this->validarReferenciaTarjeta()) {
            $this->confirmar();
            return true;
        }
        return false;
    }

    private function procesarTransferencia(): bool
    {
        // Aquí se integraría con el banco para verificar la transferencia
        // Por ahora marcamos como pendiente
        $this->update(['estado' => self::ESTADO_PENDIENTE]);
        return true;
    }

    private function procesarOtro(): bool
    {
        // Para otros métodos, marcamos como pendiente
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
        ];

        $this->update(['extras_json' => $extras]);
    }

    // Scopes
    public function scopeByEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeByMetodoPago($query, int $metodoPagoId)
    {
        return $query->where('metodo_pago_id', $metodoPagoId);
    }

    public function scopeConfirmados($query)
    {
        return $query->where('estado', self::ESTADO_CONFIRMADO);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeByFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_pago', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_pago', $fechaInicio);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_pago', today());
    }
}
