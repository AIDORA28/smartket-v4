<?php

namespace App\Http\Controllers;

use App\Models\CRM\Cliente;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Obtener lista de clientes
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            $query = Cliente::forEmpresa($empresa->id);

            // Filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('documento', 'like', "%{$search}%")
                      ->orWhere('telefono', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('estado')) {
                if ($request->estado === 'activo') {
                    $query->activos();
                } else {
                    $query->inactivos();
                }
            }

            if ($request->filled('con_credito')) {
                if ($request->boolean('con_credito')) {
                    $query->conCredito();
                } else {
                    $query->sinCredito();
                }
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'nombre');
            $sortDir = $request->get('sort_dir', 'asc');
            $query->orderBy($sortBy, $sortDir);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $clientes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $clientes,
                'message' => 'Clientes obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener clientes', [
                'error' => $e->getMessage(),
                'empresa_id' => $empresa->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes'
            ], 500);
        }
    }

    /**
     * Crear nuevo cliente
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'tipo_documento' => 'required|in:dni,ce,ruc,pasaporte',
                'documento' => 'required|string|max:20|unique:clientes,documento,NULL,id,empresa_id,' . $empresa->id,
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'direccion' => 'nullable|string|max:500',
                'limite_credito' => 'nullable|numeric|min:0',
                'dias_credito' => 'nullable|integer|min:1|max:365',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cliente = Cliente::create([
                'empresa_id' => $empresa->id,
                'nombre' => $request->nombre,
                'tipo_documento' => $request->tipo_documento,
                'documento' => $request->documento,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'direccion' => $request->direccion,
                'limite_credito' => $request->limite_credito ?? 0,
                'dias_credito' => $request->dias_credito ?? 30,
                'activo' => true,
                'observaciones' => $request->observaciones,
            ]);

            Log::info('Cliente creado', [
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa->id,
                'nombre' => $cliente->nombre
            ]);

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear cliente', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear cliente'
            ], 500);
        }
    }

    /**
     * Mostrar cliente específico
     */
    public function show(Cliente $cliente): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que el cliente pertenece a la empresa
            if ($cliente->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            // Cargar relaciones adicionales
            $cliente->load(['ventasRecientes' => function ($query) {
                $query->orderBy('fecha_venta', 'desc')->limit(5);
            }]);

            // Agregar información calculada
            $cliente->saldo_pendiente = $cliente->saldo_pendiente;
            $cliente->estado_credito = $cliente->estado_credito;
            $cliente->dias_mora_promedio = $cliente->dias_mora_promedio;

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener cliente', [
                'error' => $e->getMessage(),
                'cliente_id' => $cliente->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener cliente'
            ], 500);
        }
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que el cliente pertenece a la empresa
            if ($cliente->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:255',
                'tipo_documento' => 'sometimes|required|in:dni,ce,ruc,pasaporte',
                'documento' => 'sometimes|required|string|max:20|unique:clientes,documento,' . $cliente->id . ',id,empresa_id,' . $empresa->id,
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'direccion' => 'nullable|string|max:500',
                'limite_credito' => 'nullable|numeric|min:0',
                'dias_credito' => 'nullable|integer|min:1|max:365',
                'activo' => 'sometimes|boolean',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cliente->update($request->only([
                'nombre', 'tipo_documento', 'documento', 'telefono', 'email',
                'direccion', 'limite_credito', 'dias_credito', 'activo', 'observaciones'
            ]));

            Log::info('Cliente actualizado', [
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa->id,
                'cambios' => $request->only([
                    'nombre', 'tipo_documento', 'documento', 'telefono', 'email',
                    'direccion', 'limite_credito', 'dias_credito', 'activo'
                ])
            ]);

            return response()->json([
                'success' => true,
                'data' => $cliente->fresh(),
                'message' => 'Cliente actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar cliente', [
                'error' => $e->getMessage(),
                'cliente_id' => $cliente->id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cliente'
            ], 500);
        }
    }

    /**
     * Eliminar cliente (soft delete)
     */
    public function destroy(Cliente $cliente): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // Verificar que el cliente pertenece a la empresa
            if ($cliente->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            // Verificar si tiene ventas pendientes
            if ($cliente->saldo_pendiente > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un cliente con saldo pendiente'
                ], 422);
            }

            // Desactivar en lugar de eliminar
            $cliente->update(['activo' => false]);

            Log::info('Cliente desactivado', [
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa->id,
                'nombre' => $cliente->nombre
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente desactivado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al desactivar cliente', [
                'error' => $e->getMessage(),
                'cliente_id' => $cliente->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar cliente'
            ], 500);
        }
    }

    /**
     * Obtener historial de compras del cliente
     */
    public function historialCompras(Cliente $cliente): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            if ($cliente->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $ventas = $cliente->ventas()
                ->with(['detalles.producto', 'pagos.metodoPago'])
                ->orderBy('fecha_venta', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $ventas,
                'message' => 'Historial obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener historial de compras', [
                'error' => $e->getMessage(),
                'cliente_id' => $cliente->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial'
            ], 500);
        }
    }

    /**
     * Buscar clientes para autocompletado
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            $term = $request->get('term', '');

            if (strlen($term) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Ingrese al menos 2 caracteres'
                ]);
            }

            $clientes = Cliente::forEmpresa($empresa->id)
                ->activos()
                ->where(function ($query) use ($term) {
                    $query->where('nombre', 'like', "%{$term}%")
                          ->orWhere('documento', 'like', "%{$term}%")
                          ->orWhere('telefono', 'like', "%{$term}%");
                })
                ->select('id', 'nombre', 'documento', 'telefono', 'limite_credito')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $clientes,
                'message' => 'Búsqueda completada'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de clientes', [
                'error' => $e->getMessage(),
                'term' => $request->get('term')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
        }
    }
}

