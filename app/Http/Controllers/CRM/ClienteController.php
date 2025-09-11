<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Cliente;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * 📋 Obtener lista de clientes
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            $query = Cliente::forEmpresa($empresa->id);

            // 🔍 Filtros de búsqueda
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('numero_documento', 'like', "%{$search}%")
                      ->orWhere('telefono', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // 🎯 Filtros específicos
            if ($request->filled('tipo_documento')) {
                $query->where('tipo_documento', $request->tipo_documento);
            }

            if ($request->filled('activo')) {
                if ($request->boolean('activo')) {
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

            if ($request->filled('es_empresa')) {
                $query->where('es_empresa', $request->boolean('es_empresa'));
            }

            // 📊 Ordenamiento
            $sortBy = $request->get('sort_by', 'nombre');
            $sortDir = $request->get('sort_dir', 'asc');
            $query->orderBy($sortBy, $sortDir);

            // 📄 Paginación
            $perPage = $request->get('per_page', 15);
            $clientes = $query->paginate($perPage);

            // 📈 Agregar información calculada
            $clientes->getCollection()->transform(function ($cliente) {
                $cliente->credito_disponible = $cliente->credito_disponible;
                $cliente->estado_credito = $cliente->estado_credito;
                $cliente->estadisticas = $cliente->getEstadisticas();
                return $cliente;
            });

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
     * ➕ Crear nuevo cliente
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $validator = Validator::make($request->all(), [
                'tipo_documento' => 'required|in:DNI,RUC,CE,PASAPORTE',
                'numero_documento' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:clientes,numero_documento,NULL,id,empresa_id,' . $empresa->id
                ],
                'nombre' => 'required|string|max:150',
                'email' => 'nullable|email|max:150',
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'nullable|string',
                'fecha_nacimiento' => 'nullable|date|before:today',
                'genero' => 'nullable|in:M,F,O',
                'es_empresa' => 'boolean',
                'limite_credito' => 'nullable|numeric|min:0|max:999999.99',
                'permite_credito' => 'boolean',
                'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
                'extras_json' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 🔄 Lógica de negocio
            $clienteData = $request->only([
                'tipo_documento', 'numero_documento', 'nombre', 'email', 'telefono',
                'direccion', 'fecha_nacimiento', 'genero', 'es_empresa',
                'limite_credito', 'permite_credito', 'descuento_porcentaje', 'extras_json'
            ]);

            $clienteData['empresa_id'] = $empresa->id;
            $clienteData['activo'] = true;
            $clienteData['credito_usado'] = 0;

            // Validación de negocio: Si es RUC debe ser empresa
            if ($request->tipo_documento === 'RUC') {
                $clienteData['es_empresa'] = true;
            }

            DB::beginTransaction();

            $cliente = Cliente::create($clienteData);

            DB::commit();

            Log::info('Cliente creado en módulo CRM', [
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa->id,
                'nombre' => $cliente->nombre,
                'tipo_documento' => $cliente->tipo_documento
            ]);

            return response()->json([
                'success' => true,
                'data' => $cliente->fresh()->load('empresa'),
                'message' => 'Cliente creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear cliente en módulo CRM', [
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
     * 👁️ Mostrar cliente específico
     */
    public function show(Cliente $cliente): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // 🔒 Verificar pertenencia a empresa
            if ($cliente->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            // 📊 Cargar relaciones y estadísticas
            $cliente->load(['empresa', 'ventas' => function ($query) {
                $query->orderBy('fecha_venta', 'desc')->limit(10);
            }]);

            // 📈 Agregar información calculada
            $cliente->credito_disponible = $cliente->credito_disponible;
            $cliente->estado_credito = $cliente->estado_credito;
            $cliente->info_credito = $cliente->getInfoCredito();
            $cliente->estadisticas = $cliente->getEstadisticas();

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
     * ✏️ Actualizar cliente
     */
    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // 🔒 Verificar pertenencia a empresa
            if ($cliente->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'tipo_documento' => 'sometimes|required|in:DNI,RUC,CE,PASAPORTE',
                'numero_documento' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:20',
                    'unique:clientes,numero_documento,' . $cliente->id . ',id,empresa_id,' . $empresa->id
                ],
                'nombre' => 'sometimes|required|string|max:150',
                'email' => 'nullable|email|max:150',
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'nullable|string',
                'fecha_nacimiento' => 'nullable|date|before:today',
                'genero' => 'nullable|in:M,F,O',
                'es_empresa' => 'sometimes|boolean',
                'limite_credito' => 'nullable|numeric|min:0|max:999999.99',
                'permite_credito' => 'sometimes|boolean',
                'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
                'activo' => 'sometimes|boolean',
                'extras_json' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $updateData = $request->only([
                'tipo_documento', 'numero_documento', 'nombre', 'email', 'telefono',
                'direccion', 'fecha_nacimiento', 'genero', 'es_empresa',
                'limite_credito', 'permite_credito', 'descuento_porcentaje', 
                'activo', 'extras_json'
            ]);

            // 🔄 Lógica de negocio
            if (isset($updateData['tipo_documento']) && $updateData['tipo_documento'] === 'RUC') {
                $updateData['es_empresa'] = true;
            }

            $cliente->update($updateData);

            DB::commit();

            Log::info('Cliente actualizado en módulo CRM', [
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa->id,
                'cambios' => array_keys($updateData)
            ]);

            return response()->json([
                'success' => true,
                'data' => $cliente->fresh()->load('empresa'),
                'message' => 'Cliente actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar cliente en módulo CRM', [
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
     * 🗑️ Eliminar cliente (soft delete)
     */
    public function destroy(Cliente $cliente): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            // 🔒 Verificar pertenencia a empresa
            if ($cliente->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            // 🔍 Verificar si tiene crédito usado
            if ($cliente->credito_usado > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un cliente con crédito utilizado'
                ], 422);
            }

            // 🔍 Verificar si tiene ventas activas
            $ventasActivas = $cliente->ventas()->where('estado', '!=', 'cancelada')->count();
            if ($ventasActivas > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un cliente con ventas activas'
                ], 422);
            }

            DB::beginTransaction();

            // 🚫 Desactivar en lugar de eliminar
            $cliente->update(['activo' => false]);

            DB::commit();

            Log::info('Cliente desactivado en módulo CRM', [
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa->id,
                'nombre' => $cliente->nombre
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente desactivado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al desactivar cliente en módulo CRM', [
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
     * 🔍 Buscar clientes para autocompletado
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
                          ->orWhere('numero_documento', 'like', "%{$term}%")
                          ->orWhere('telefono', 'like', "%{$term}%");
                })
                ->select([
                    'id', 'nombre', 'numero_documento', 'telefono', 
                    'email', 'limite_credito', 'credito_usado', 
                    'permite_credito', 'descuento_porcentaje'
                ])
                ->limit(10)
                ->get()
                ->map(function ($cliente) {
                    $cliente->credito_disponible = $cliente->credito_disponible;
                    return $cliente;
                });

            return response()->json([
                'success' => true,
                'data' => $clientes,
                'message' => 'Búsqueda completada'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de clientes CRM', [
                'error' => $e->getMessage(),
                'term' => $request->get('term')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
        }
    }

    /**
     * 📊 Obtener estadísticas del cliente
     */
    public function estadisticas(Cliente $cliente): JsonResponse
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            if ($cliente->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $estadisticas = $cliente->getEstadisticas();
            $infoCredito = $cliente->getInfoCredito();

            return response()->json([
                'success' => true,
                'data' => [
                    'estadisticas' => $estadisticas,
                    'credito' => $infoCredito,
                    'estado_credito' => $cliente->estado_credito
                ],
                'message' => 'Estadísticas obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de cliente CRM', [
                'error' => $e->getMessage(),
                'cliente_id' => $cliente->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ], 500);
        }
    }
}
