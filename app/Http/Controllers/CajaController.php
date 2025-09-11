<?php

namespace App\Http\Controllers;

use App\Services\CajaService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class CajaController extends Controller
{
    protected CajaService $cajaService;
    protected TenantService $tenantService;

    public function __construct(CajaService $cajaService, TenantService $tenantService)
    {
        $this->cajaService = $cajaService;
        $this->tenantService = $tenantService;
    }

    /**
     * Obtener estado de cajas de la sucursal
     */
    public function estadoCaja(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $sucursal = $this->tenantService->getSucursal();

            $estado = $this->cajaService->obtenerEstadoCaja($empresa->id, $sucursal->id);

            return response()->json([
                'success' => true,
                'data' => $estado
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener estado de caja: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estado de caja',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Abrir sesión de caja
     */
    public function abrirCaja(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'caja_id' => 'required|integer|exists:cajas,id',
                'monto_inicial' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de apertura inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sesion = $this->cajaService->abrirSesion(
                $request->caja_id,
                $request->monto_inicial,
                $request->observaciones
            );

            return response()->json([
                'success' => true,
                'message' => 'Sesión de caja abierta exitosamente',
                'data' => $sesion
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al abrir caja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar sesión de caja
     */
    public function cerrarCaja(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'sesion_id' => 'required|integer|exists:caja_sesiones,id',
                'monto_declarado' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de cierre inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sesion = $this->cajaService->cerrarSesion(
                $request->sesion_id,
                $request->monto_declarado,
                $request->observaciones
            );

            return response()->json([
                'success' => true,
                'message' => 'Sesión de caja cerrada exitosamente',
                'data' => $sesion
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar caja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener arqueo de sesión
     */
    public function arqueo(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'sesion_id' => 'required|integer|exists:caja_sesiones,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión ID requerido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $arqueo = $this->cajaService->getArqueo($request->sesion_id);

            return response()->json([
                'success' => true,
                'data' => $arqueo
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener arqueo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de sesiones
     */
    public function historial(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            $historial = $this->cajaService->getHistorial(
                $empresa->id,
                $request->get('caja_id'),
                $request->get('limit', 20)
            );

            return response()->json([
                'success' => true,
                'data' => $historial
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar apertura de caja
     */
    public function validarApertura(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'caja_id' => 'required|integer|exists:cajas,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Caja ID requerido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validacion = $this->cajaService->validarApertura($request->caja_id);

            return response()->json([
                'success' => true,
                'data' => $validacion
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al validar apertura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar cierre de caja
     */
    public function validarCierre(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'sesion_id' => 'required|integer|exists:caja_sesiones,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión ID requerido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validacion = $this->cajaService->validarCierre($request->sesion_id);

            return response()->json([
                'success' => true,
                'data' => $validacion
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al validar cierre',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar arqueo
     */
    public function exportarArqueo(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'sesion_id' => 'required|integer|exists:caja_sesiones,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión ID requerido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $arqueo = $this->cajaService->exportarArqueo($request->sesion_id);

            return response()->json([
                'success' => true,
                'data' => $arqueo
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar arqueo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}




