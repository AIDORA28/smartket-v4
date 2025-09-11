<?php

namespace App\Http\Controllers;

use App\Models\Sales\Venta;
use App\Models\Sales\VentaPago;
use App\Services\PagoService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    protected PagoService $pagoService;
    protected TenantService $tenantService;

    public function __construct(PagoService $pagoService, TenantService $tenantService)
    {
        $this->pagoService = $pagoService;
        $this->tenantService = $tenantService;
    }

    /**
     * Procesar pago para una venta
     */
    public function procesarPago(Request $request, Venta $venta): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que la venta pertenece a la empresa
            if ($venta->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'metodo_pago_id' => 'required|exists:metodos_pago,id',
                'monto' => 'required|numeric|min:0.01',
                'referencia' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pago = $this->pagoService->procesarPago(
                $venta,
                $request->metodo_pago_id,
                $request->monto,
                $request->referencia
            );

            return response()->json([
                'success' => true,
                'data' => $pago->load('metodoPago'),
                'message' => 'Pago procesado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al procesar pago', [
                'error' => $e->getMessage(),
                'venta_id' => $venta->id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar múltiples pagos para una venta
     */
    public function procesarPagosMultiples(Request $request, Venta $venta): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            if ($venta->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'pagos' => 'required|array|min:1',
                'pagos.*.metodo_pago_id' => 'required|exists:metodos_pago,id',
                'pagos.*.monto' => 'required|numeric|min:0.01',
                'pagos.*.referencia' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pagos = $this->pagoService->procesarPagosMultiples($venta, $request->pagos);

            return response()->json([
                'success' => true,
                'data' => $pagos,
                'message' => 'Pagos procesados exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al procesar pagos múltiples', [
                'error' => $e->getMessage(),
                'venta_id' => $venta->id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener pagos de una venta
     */
    public function pagosPorVenta(Venta $venta): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            if ($venta->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            $pagos = $venta->pagos()->with('metodoPago')->orderBy('created_at')->get();

            return response()->json([
                'success' => true,
                'data' => $pagos,
                'message' => 'Pagos obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener pagos de venta', [
                'error' => $e->getMessage(),
                'venta_id' => $venta->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pagos'
            ], 500);
        }
    }

    /**
     * Anular pago
     */
    public function anularPago(Request $request, VentaPago $pago): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que el pago pertenece a la empresa
            if ($pago->venta->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'motivo' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motivo de anulación requerido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->pagoService->anularPago($pago, $request->motivo);

            return response()->json([
                'success' => true,
                'data' => $pago->fresh(['metodoPago', 'venta']),
                'message' => 'Pago anulado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al anular pago', [
                'error' => $e->getMessage(),
                'pago_id' => $pago->id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar pago pendiente
     */
    public function confirmarPago(VentaPago $pago): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            if ($pago->venta->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ], 404);
            }

            $this->pagoService->confirmarPago($pago);

            return response()->json([
                'success' => true,
                'data' => $pago->fresh(['metodoPago', 'venta']),
                'message' => 'Pago confirmado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al confirmar pago', [
                'error' => $e->getMessage(),
                'pago_id' => $pago->id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener métodos de pago disponibles
     */
    public function metodosDisponibles(): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $metodos = $this->pagoService->metodosDisponibles($empresa->id);

            return response()->json([
                'success' => true,
                'data' => $metodos,
                'message' => 'Métodos disponibles obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener métodos disponibles', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métodos'
            ], 500);
        }
    }

    /**
     * Obtener resumen de pagos del día
     */
    public function resumenDelDia(): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $sucursal = $this->tenantService->getSucursal();

            $resumen = $this->pagoService->resumenPagosDelDia($empresa->id, $sucursal->id);

            return response()->json([
                'success' => true,
                'data' => $resumen,
                'message' => 'Resumen obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener resumen de pagos', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen'
            ], 500);
        }
    }

    /**
     * Obtener pagos pendientes de confirmación
     */
    public function pagosPendientes(): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $pagos = $this->pagoService->pagosPendientes($empresa->id);

            return response()->json([
                'success' => true,
                'data' => $pagos,
                'message' => 'Pagos pendientes obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener pagos pendientes', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pagos pendientes'
            ], 500);
        }
    }

    /**
     * Validar pago con tarjeta
     */
    public function validarTarjeta(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'numero_tarjeta' => 'required|string|min:13|max:19',
                'codigo_seguridad' => 'nullable|string|min:3|max:4',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de tarjeta incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $esValida = $this->pagoService->validarPagoTarjeta(
                $request->numero_tarjeta,
                $request->codigo_seguridad
            );

            return response()->json([
                'success' => true,
                'data' => ['valida' => $esValida],
                'message' => $esValida ? 'Tarjeta válida' : 'Tarjeta inválida'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al validar tarjeta', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al validar tarjeta'
            ], 500);
        }
    }
}

