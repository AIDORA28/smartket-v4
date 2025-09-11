<?php

namespace App\Services;

use App\Models\MetodoPago;
use App\Models\Sales\Venta;
use App\Models\Sales\VentaPago;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagoService
{
    /**
     * Procesar pago único
     */
    public function procesarPago(Venta $venta, int $metodoPagoId, float $monto, string $referencia = null): VentaPago
    {
        return DB::transaction(function () use ($venta, $metodoPagoId, $monto, $referencia) {
            $metodoPago = MetodoPago::findOrFail($metodoPagoId);
            
            // Validaciones
            $this->validarMetodoPago($metodoPago);
            $this->validarMonto($monto);
            $this->validarReferencia($metodoPago, $referencia);

            // Crear el pago
            $pago = $venta->agregarPago($metodoPagoId, $monto, $referencia);

            // Calcular comisión
            $pago->calcularComision();

            // Procesar según el tipo
            if (!$pago->procesar()) {
                throw new \Exception("Error al procesar el pago");
            }

            // Actualizar venta
            $venta->actualizarTotalPagado();

            Log::info("Pago procesado", [
                'venta_id' => $venta->id,
                'pago_id' => $pago->id,
                'metodo' => $metodoPago->nombre,
                'monto' => $monto
            ]);

            return $pago;
        });
    }

    /**
     * Procesar múltiples pagos
     */
    public function procesarPagosMultiples(Venta $venta, array $pagos): array
    {
        return DB::transaction(function () use ($venta, $pagos) {
            $pagosCreados = [];
            $totalPagos = 0;

            foreach ($pagos as $datosPago) {
                $pago = $this->procesarPago(
                    $venta,
                    $datosPago['metodo_pago_id'],
                    $datosPago['monto'],
                    $datosPago['referencia'] ?? null
                );

                $pagosCreados[] = $pago;
                $totalPagos += $datosPago['monto'];
            }

            // Calcular vuelto si hay exceso en efectivo
            $this->calcularVuelto($venta, $totalPagos);

            // Verificar si la venta está completamente pagada
            if ($venta->fresh()->esta_pagada) {
                $venta->marcarComoPagada();
            }

            return $pagosCreados;
        });
    }

    /**
     * Anular pago
     */
    public function anularPago(VentaPago $pago, string $motivo): bool
    {
        return DB::transaction(function () use ($pago, $motivo) {
            if ($pago->estado === VentaPago::ESTADO_ANULADO) {
                throw new \Exception('El pago ya está anulado');
            }

            // Anular el pago
            $pago->anular($motivo);

            // Actualizar total pagado de la venta
            $pago->venta->actualizarTotalPagado();

            // Si la venta queda con saldo pendiente, cambiar estado
            if ($pago->venta->saldo_pendiente > 0) {
                $pago->venta->update(['estado' => Venta::ESTADO_PENDIENTE]);
            }

            Log::info("Pago anulado", [
                'pago_id' => $pago->id,
                'venta_id' => $pago->venta_id,
                'motivo' => $motivo
            ]);

            return true;
        });
    }

    /**
     * Obtener métodos de pago activos
     */
    public function metodosDisponibles(int $empresaId): Collection
    {
        return MetodoPago::forEmpresa($empresaId)
            ->activos()
            ->orderBy('orden')
            ->get();
    }

    /**
     * Validar pago con tarjeta
     */
    public function validarPagoTarjeta(string $numeroTarjeta, string $codigoSeguridad = null): bool
    {
        // Validación básica de número de tarjeta (Algoritmo de Luhn)
        if (!$this->validarLuhn($numeroTarjeta)) {
            return false;
        }

        // Aquí se integrarían las validaciones del procesador de pagos
        return true;
    }

    /**
     * Obtener resumen de pagos del día
     */
    public function resumenPagosDelDia(int $empresaId, int $sucursalId = null): array
    {
        $query = VentaPago::whereHas('venta', function ($q) use ($empresaId, $sucursalId) {
            $q->forEmpresa($empresaId);
            if ($sucursalId) {
                $q->forSucursal($sucursalId);
            }
        })
        ->hoy()
        ->confirmados()
        ->with('metodoPago');

        $pagos = $query->get();

        $resumen = [
            'total_pagos' => $pagos->count(),
            'monto_total' => $pagos->sum('monto'),
            'comision_total' => $pagos->sum('comision_monto'),
            'monto_neto' => $pagos->sum('monto_neto'),
            'por_metodo' => []
        ];

        // Agrupar por método de pago
        $pagosPorMetodo = $pagos->groupBy('metodo_pago_id');
        
        foreach ($pagosPorMetodo as $metodoPagoId => $pagosList) {
            $metodoPago = $pagosList->first()->metodoPago;
            
            $resumen['por_metodo'][] = [
                'metodo' => $metodoPago->nombre,
                'tipo' => $metodoPago->tipo,
                'cantidad' => $pagosList->count(),
                'monto' => $pagosList->sum('monto'),
                'comision' => $pagosList->sum('comision_monto'),
                'neto' => $pagosList->sum('monto_neto'),
            ];
        }

        return $resumen;
    }

    /**
     * Obtener pagos pendientes de confirmación
     */
    public function pagosPendientes(int $empresaId): Collection
    {
        return VentaPago::whereHas('venta', function ($q) use ($empresaId) {
            $q->forEmpresa($empresaId);
        })
        ->pendientes()
        ->with(['venta', 'metodoPago'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Confirmar pago pendiente
     */
    public function confirmarPago(VentaPago $pago): bool
    {
        return DB::transaction(function () use ($pago) {
            if ($pago->estado !== VentaPago::ESTADO_PENDIENTE) {
                throw new \Exception('El pago no está pendiente de confirmación');
            }

            $pago->confirmar();
            $pago->venta->actualizarTotalPagado();

            // Si la venta queda completamente pagada
            if ($pago->venta->fresh()->esta_pagada) {
                $pago->venta->marcarComoPagada();
            }

            Log::info("Pago confirmado", [
                'pago_id' => $pago->id,
                'venta_id' => $pago->venta_id
            ]);

            return true;
        });
    }

    /**
     * Calcular vuelto para pagos en efectivo
     */
    private function calcularVuelto(Venta $venta, float $totalPagado): void
    {
        if ($totalPagado > $venta->total) {
            $vuelto = $totalPagado - $venta->total;
            $venta->update(['vuelto' => $vuelto]);
        }
    }

    /**
     * Validar método de pago
     */
    private function validarMetodoPago(MetodoPago $metodoPago): void
    {
        if (!$metodoPago->activo) {
            throw new \Exception("El método de pago '{$metodoPago->nombre}' no está activo");
        }

        if ($metodoPago->fecha_inicio && $metodoPago->fecha_inicio->isFuture()) {
            throw new \Exception("El método de pago '{$metodoPago->nombre}' aún no está disponible");
        }

        if ($metodoPago->fecha_fin && $metodoPago->fecha_fin->isPast()) {
            throw new \Exception("El método de pago '{$metodoPago->nombre}' ya no está disponible");
        }
    }

    /**
     * Validar monto del pago
     */
    private function validarMonto(float $monto): void
    {
        if ($monto <= 0) {
            throw new \Exception('El monto del pago debe ser mayor a cero');
        }
    }

    /**
     * Validar referencia según tipo de pago
     */
    private function validarReferencia(MetodoPago $metodoPago, ?string $referencia): void
    {
        // Para efectivo no se requiere referencia
        if ($metodoPago->tipo === MetodoPago::TIPO_EFECTIVO) {
            return;
        }

        // Para otros métodos, la referencia es opcional pero recomendada
        if (empty($referencia) && $metodoPago->tipo !== MetodoPago::TIPO_EFECTIVO) {
            Log::warning("Pago sin referencia", [
                'metodo_pago' => $metodoPago->nombre,
                'tipo' => $metodoPago->tipo
            ]);
        }
    }

    /**
     * Validar número de tarjeta usando algoritmo de Luhn
     */
    private function validarLuhn(string $numero): bool
    {
        $numero = preg_replace('/\D/', '', $numero);
        $suma = 0;
        $alternar = false;

        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $n = (int) $numero[$i];

            if ($alternar) {
                $n *= 2;
                if ($n > 9) {
                    $n = ($n % 10) + 1;
                }
            }

            $suma += $n;
            $alternar = !$alternar;
        }

        return ($suma % 10) === 0;
    }
}

