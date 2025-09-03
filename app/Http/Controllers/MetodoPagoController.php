<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MetodoPagoController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Obtener lista de métodos de pago
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            $query = MetodoPago::forEmpresa($empresa->id);

            // Filtros
            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->filled('activo')) {
                if ($request->boolean('activo')) {
                    $query->activos();
                } else {
                    $query->inactivos();
                }
            }

            // Solo métodos activos por defecto para POS
            if (!$request->has('incluir_inactivos')) {
                $query->activos();
            }

            // Ordenamiento
            $query->orderBy('orden')->orderBy('nombre');

            $metodos = $query->get();

            return response()->json([
                'success' => true,
                'data' => $metodos,
                'message' => 'Métodos de pago obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener métodos de pago', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métodos de pago'
            ], 500);
        }
    }

    /**
     * Crear nuevo método de pago
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100|unique:metodos_pago,nombre,NULL,id,empresa_id,' . $empresa->id,
                'tipo' => 'required|in:efectivo,tarjeta,transferencia,credito,digital',
                'descripcion' => 'nullable|string|max:255',
                'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
                'comision_fija' => 'nullable|numeric|min:0',
                'orden' => 'nullable|integer|min:1',
                'activo' => 'sometimes|boolean',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'configuracion_json' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Establecer orden automático si no se proporciona
            $orden = $request->orden ?? (MetodoPago::forEmpresa($empresa->id)->max('orden') + 1);

            $metodoPago = MetodoPago::create([
                'empresa_id' => $empresa->id,
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'comision_porcentaje' => $request->comision_porcentaje ?? 0,
                'comision_fija' => $request->comision_fija ?? 0,
                'orden' => $orden,
                'activo' => $request->get('activo', true),
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'configuracion_json' => $request->configuracion_json,
            ]);

            Log::info('Método de pago creado', [
                'metodo_id' => $metodoPago->id,
                'empresa_id' => $empresa->id,
                'nombre' => $metodoPago->nombre,
                'tipo' => $metodoPago->tipo
            ]);

            return response()->json([
                'success' => true,
                'data' => $metodoPago,
                'message' => 'Método de pago creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear método de pago', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear método de pago'
            ], 500);
        }
    }

    /**
     * Mostrar método de pago específico
     */
    public function show(MetodoPago $metodoPago): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que el método pertenece a la empresa
            if ($metodoPago->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método de pago no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $metodoPago,
                'message' => 'Método de pago obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener método de pago', [
                'error' => $e->getMessage(),
                'metodo_id' => $metodoPago->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener método de pago'
            ], 500);
        }
    }

    /**
     * Actualizar método de pago
     */
    public function update(Request $request, MetodoPago $metodoPago): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que el método pertenece a la empresa
            if ($metodoPago->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método de pago no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:100|unique:metodos_pago,nombre,' . $metodoPago->id . ',id,empresa_id,' . $empresa->id,
                'tipo' => 'sometimes|required|in:efectivo,tarjeta,transferencia,credito,digital',
                'descripcion' => 'nullable|string|max:255',
                'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
                'comision_fija' => 'nullable|numeric|min:0',
                'orden' => 'nullable|integer|min:1',
                'activo' => 'sometimes|boolean',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'configuracion_json' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $metodoPago->update($request->only([
                'nombre', 'tipo', 'descripcion', 'comision_porcentaje', 'comision_fija',
                'orden', 'activo', 'fecha_inicio', 'fecha_fin', 'configuracion_json'
            ]));

            Log::info('Método de pago actualizado', [
                'metodo_id' => $metodoPago->id,
                'empresa_id' => $empresa->id,
                'cambios' => $request->only([
                    'nombre', 'tipo', 'descripcion', 'comision_porcentaje', 'comision_fija',
                    'orden', 'activo', 'fecha_inicio', 'fecha_fin'
                ])
            ]);

            return response()->json([
                'success' => true,
                'data' => $metodoPago->fresh(),
                'message' => 'Método de pago actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar método de pago', [
                'error' => $e->getMessage(),
                'metodo_id' => $metodoPago->id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar método de pago'
            ], 500);
        }
    }

    /**
     * Eliminar método de pago
     */
    public function destroy(MetodoPago $metodoPago): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que el método pertenece a la empresa
            if ($metodoPago->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método de pago no encontrado'
                ], 404);
            }

            // Verificar si tiene transacciones asociadas
            if ($metodoPago->pagos()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un método de pago con transacciones asociadas'
                ], 422);
            }

            $metodoPago->delete();

            Log::info('Método de pago eliminado', [
                'metodo_id' => $metodoPago->id,
                'empresa_id' => $empresa->id,
                'nombre' => $metodoPago->nombre
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Método de pago eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar método de pago', [
                'error' => $e->getMessage(),
                'metodo_id' => $metodoPago->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar método de pago'
            ], 500);
        }
    }

    /**
     * Obtener métodos de pago activos para POS
     */
    public function activos(): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $metodos = MetodoPago::forEmpresa($empresa->id)
                ->activos()
                ->disponibles()
                ->orderBy('orden')
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'tipo', 'comision_porcentaje', 'orden']);

            return response()->json([
                'success' => true,
                'data' => $metodos,
                'message' => 'Métodos activos obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener métodos activos', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métodos activos'
            ], 500);
        }
    }

    /**
     * Reordenar métodos de pago
     */
    public function reordenar(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $validator = Validator::make($request->all(), [
                'metodos' => 'required|array',
                'metodos.*.id' => 'required|integer|exists:metodos_pago,id',
                'metodos.*.orden' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            foreach ($request->metodos as $metodoData) {
                $metodo = MetodoPago::find($metodoData['id']);
                
                if ($metodo && $metodo->empresa_id === $empresa->id) {
                    $metodo->update(['orden' => $metodoData['orden']]);
                }
            }

            Log::info('Métodos de pago reordenados', [
                'empresa_id' => $empresa->id,
                'cantidad' => count($request->metodos)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al reordenar métodos', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar orden'
            ], 500);
        }
    }

    /**
     * Activar/Desactivar método de pago
     */
    public function toggleEstado(MetodoPago $metodoPago): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            if ($metodoPago->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método de pago no encontrado'
                ], 404);
            }

            $nuevoEstado = !$metodoPago->activo;
            $metodoPago->update(['activo' => $nuevoEstado]);

            Log::info('Estado de método de pago cambiado', [
                'metodo_id' => $metodoPago->id,
                'empresa_id' => $empresa->id,
                'nuevo_estado' => $nuevoEstado ? 'activo' : 'inactivo'
            ]);

            return response()->json([
                'success' => true,
                'data' => $metodoPago->fresh(),
                'message' => $nuevoEstado ? 'Método activado' : 'Método desactivado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del método', [
                'error' => $e->getMessage(),
                'metodo_id' => $metodoPago->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado'
            ], 500);
        }
    }
}
