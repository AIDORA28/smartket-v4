<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\CajaSesion;
use App\Models\CajaMovimiento;
use App\Models\Sales\Venta;
use App\Models\Sales\VentaPago;
use App\Models\MetodoPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class CajaService
{
    /**
     * Obtener cajas activas de una sucursal
     */
    public function getCajasActivas(int $empresaId, int $sucursalId): \Illuminate\Database\Eloquent\Collection
    {
        return Caja::where('empresa_id', $empresaId)
            ->where('sucursal_id', $sucursalId)
            ->where('activa', true)
            ->with(['sesionActual', 'sucursal'])
            ->get();
    }

    /**
     * Abrir sesión de caja
     */
    public function abrirSesion(
        int $cajaId, 
        float $montoInicial, 
        ?string $observaciones = null
    ): CajaSesion {
        return DB::transaction(function() use ($cajaId, $montoInicial, $observaciones) {
            $caja = Caja::findOrFail($cajaId);
            
            // Verificar que no tenga sesión abierta
            if ($caja->tieneSesionAbierta()) {
                throw new Exception('La caja ya tiene una sesión abierta');
            }

            // Crear nueva sesión
            $sesion = CajaSesion::create([
                'empresa_id' => $caja->empresa_id,
                'caja_id' => $cajaId,
                'user_apertura_id' => Auth::id(),
                'codigo' => CajaSesion::generarCodigo($cajaId),
                'apertura_at' => now(),
                'monto_inicial' => $montoInicial,
                'estado' => 'abierta',
                'observaciones' => $observaciones,
            ]);

            // Registrar movimiento inicial si hay monto
            if ($montoInicial > 0) {
                $this->registrarMovimiento(
                    $sesion->id,
                    'ingreso',
                    $montoInicial,
                    'Monto inicial de apertura',
                    'apertura'
                );
            }

            Log::info("Sesión de caja abierta", [
                'caja_id' => $cajaId,
                'sesion_id' => $sesion->id,
                'usuario_id' => Auth::id(),
                'monto_inicial' => $montoInicial
            ]);

            return $sesion->load(['caja', 'usuarioApertura']);
        });
    }

    /**
     * Cerrar sesión de caja
     */
    public function cerrarSesion(
        int $sesionId, 
        float $montoDeclarado, 
        ?string $observaciones = null
    ): CajaSesion {
        return DB::transaction(function() use ($sesionId, $montoDeclarado, $observaciones) {
            $sesion = CajaSesion::findOrFail($sesionId);

            // Verificar que esté abierta
            if (!$sesion->estaAbierta()) {
                throw new Exception('La sesión ya está cerrada');
            }

            // Actualizar montos de ventas en efectivo
            $sesion->actualizarVentasEfectivo();

            // Calcular diferencia
            $montoCalculado = $sesion->monto_calculado;
            $diferencia = $montoDeclarado - $montoCalculado;

            // Cerrar sesión
            $sesion->update([
                'user_cierre_id' => Auth::id(),
                'cierre_at' => now(),
                'monto_declarado_cierre' => $montoDeclarado,
                'diferencia' => $diferencia,
                'estado' => 'cerrada',
                'observaciones' => trim(($sesion->observaciones ?? '') . "\n" . ($observaciones ?? '')),
            ]);

            Log::info("Sesión de caja cerrada", [
                'sesion_id' => $sesionId,
                'usuario_id' => Auth::id(),
                'diferencia' => $diferencia
            ]);

            return $sesion->load(['caja', 'usuarioApertura', 'usuarioCierre']);
        });
    }

    /**
     * Registrar movimiento de caja
     */
    public function registrarMovimiento(
        int $sesionId,
        string $tipo,
        float $monto,
        string $concepto,
        ?string $referenciaTipo = null,
        ?int $referenciaId = null
    ): CajaMovimiento {
        $sesion = CajaSesion::findOrFail($sesionId);

        if (!$sesion->estaAbierta()) {
            throw new Exception('No se pueden registrar movimientos en una sesión cerrada');
        }

        $movimiento = CajaMovimiento::create([
            'empresa_id' => $sesion->empresa_id,
            'caja_sesion_id' => $sesionId,
            'tipo' => $tipo,
            'monto' => $monto,
            'concepto' => $concepto,
            'referencia_tipo' => $referenciaTipo,
            'referencia_id' => $referenciaId,
            'user_id' => Auth::id(),
            'fecha' => now(),
        ]);

        // Actualizar totales de la sesión
        $this->actualizarTotalesSesion($sesion);

        return $movimiento->load(['usuario']);
    }

    /**
     * Obtener arqueo de sesión
     */
    public function getArqueo(int $sesionId): array
    {
        $sesion = CajaSesion::with([
            'caja',
            'usuarioApertura',
            'usuarioCierre',
            'movimientos.usuario',
            'pagosEfectivo'
        ])->findOrFail($sesionId);

        $movimientos = $sesion->movimientos()
            ->orderBy('fecha')
            ->get()
            ->groupBy('tipo');

        return [
            'sesion' => $sesion,
            'resumen' => [
                'monto_inicial' => $sesion->monto_inicial,
                'total_ingresos' => $movimientos->get('ingreso', collect())->sum('monto'),
                'total_retiros' => $movimientos->get('retiro', collect())->sum('monto'),
                'total_ventas_efectivo' => $sesion->monto_ventas_efectivo,
                'monto_calculado' => $sesion->monto_calculado,
                'monto_declarado' => $sesion->monto_declarado_cierre,
                'diferencia' => $sesion->diferencia,
            ],
            'movimientos' => [
                'ingresos' => $movimientos->get('ingreso', collect()),
                'retiros' => $movimientos->get('retiro', collect()),
                'ventas_efectivo' => $movimientos->get('venta_efectivo', collect()),
            ],
            'pagos_efectivo' => $sesion->pagosEfectivo()
                ->with(['venta', 'metodoPago'])
                ->get(),
        ];
    }

    /**
     * Obtener estado actual de caja/sesión
     */
    public function obtenerEstadoCaja(int $empresaId, int $sucursalId): array
    {
        $cajas = $this->getCajasActivas($empresaId, $sucursalId);
        
        $estadoCajas = [];
        
        foreach ($cajas as $caja) {
            $sesionActual = $caja->getSesionAbierta();
            
            $estadoCajas[] = [
                'caja' => $caja,
                'tiene_sesion_abierta' => $caja->tieneSesionAbierta(),
                'sesion_actual' => $sesionActual,
                'puede_abrir' => !$caja->tieneSesionAbierta() && $caja->activa,
            ];
        }

        return [
            'cajas' => $estadoCajas,
            'total_cajas' => count($cajas),
            'cajas_abiertas' => count(array_filter($estadoCajas, fn($c) => $c['tiene_sesion_abierta'])),
        ];
    }

    /**
     * Validar apertura de caja
     */
    public function validarApertura(int $cajaId): array
    {
        $caja = Caja::findOrFail($cajaId);
        
        $errors = [];

        // Verificar que la caja esté activa
        if (!$caja->activa) {
            $errors[] = 'La caja no está activa';
        }

        // Verificar que no tenga sesión abierta
        if ($caja->tieneSesionAbierta()) {
            $errors[] = 'La caja ya tiene una sesión abierta';
        }

        return [
            'puede_abrir' => empty($errors),
            'errores' => $errors,
            'caja' => $caja,
        ];
    }

    /**
     * Validar cierre de caja
     */
    public function validarCierre(int $sesionId): array
    {
        $sesion = CajaSesion::findOrFail($sesionId);
        
        $errors = [];
        $warnings = [];

        // Verificar que esté abierta
        if (!$sesion->estaAbierta()) {
            $errors[] = 'La sesión ya está cerrada';
        }

        // Verificar tiempo mínimo de sesión (opcional)
        if ($sesion->apertura_at && $sesion->apertura_at->diffInMinutes(now()) < 5) {
            $warnings[] = 'La sesión tiene menos de 5 minutos abierta';
        }

        return [
            'puede_cerrar' => empty($errors),
            'errores' => $errors,
            'advertencias' => $warnings,
            'sesion' => $sesion,
        ];
    }

    /**
     * Obtener historial de sesiones
     */
    public function getHistorial(int $empresaId, ?int $cajaId = null, ?int $limit = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = CajaSesion::empresa($empresaId)
            ->with(['caja', 'usuarioApertura', 'usuarioCierre'])
            ->orderBy('apertura_at', 'desc');

        if ($cajaId) {
            $query->where('caja_id', $cajaId);
        }

        return $query->paginate($limit);
    }

    /**
     * Actualizar totales de sesión
     */
    private function actualizarTotalesSesion(CajaSesion $sesion): void
    {
        $movimientos = $sesion->movimientos()
            ->selectRaw('tipo, SUM(monto) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $sesion->update([
            'monto_ingresos' => $movimientos->get('ingreso', 0),
            'monto_retiros' => $movimientos->get('retiro', 0),
        ]);
    }

    /**
     * Exportar arqueo a array para reportes
     */
    public function exportarArqueo(int $sesionId): array
    {
        $arqueo = $this->getArqueo($sesionId);
        
        return [
            'titulo' => 'Arqueo de Caja - Sesión ' . $arqueo['sesion']->codigo,
            'fecha' => $arqueo['sesion']->apertura_at->format('d/m/Y'),
            'caja' => $arqueo['sesion']->caja->nombre,
            'datos' => $arqueo,
            'generado_por' => Auth::user()?->name ?? 'Sistema',
            'generado_en' => now()->format('d/m/Y H:i:s'),
        ];
    }
}

