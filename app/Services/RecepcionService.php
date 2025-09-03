<?php

namespace App\Services;

use App\Models\Recepcion;
use App\Models\Compra;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecepcionService
{
    /**
     * Crear nueva recepción
     */
    public function crearRecepcion(array $datos): Recepcion
    {
        return Recepcion::create([
            'empresa_id' => $datos['empresa_id'],
            'compra_id' => $datos['compra_id'],
            'sucursal_id' => $datos['sucursal_id'],
            'user_id' => $datos['user_id'] ?? Auth::id(),
            'fecha_recepcion' => $datos['fecha_recepcion'] ?? Carbon::now(),
            'estado' => $datos['estado'] ?? 'parcial',
            'observaciones' => $datos['observaciones'] ?? null
        ]);
    }

    /**
     * Actualizar estado de recepción
     */
    public function actualizarEstado(int $id, string $estado, ?string $observaciones = null): bool
    {
        $recepcion = Recepcion::findOrFail($id);
        
        $recepcion->estado = $estado;
        if ($observaciones) {
            $recepcion->observaciones = $observaciones;
        }
        
        return $recepcion->save();
    }

    /**
     * Obtener recepciones de una compra
     */
    public function getRecepcionesPorCompra(int $compraId): \Illuminate\Database\Eloquent\Collection
    {
        return Recepcion::where('compra_id', $compraId)
            ->with(['usuario', 'sucursal'])
            ->orderBy('fecha_recepcion', 'desc')
            ->get();
    }

    /**
     * Verificar si compra está completamente recibida
     */
    public function estaCompleta(int $compraId): bool
    {
        $recepcion = Recepcion::where('compra_id', $compraId)
            ->where('estado', 'completa')
            ->exists();
            
        return $recepcion;
    }
}
