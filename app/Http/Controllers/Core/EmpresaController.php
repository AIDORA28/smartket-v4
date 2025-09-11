<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Empresa;
use App\Models\Core\User;
use App\Models\Core\Rubro;
use App\Models\Core\EmpresaAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmpresaController extends Controller
{
    /**
     * Listar empresas (solo para superadmin, por ahora retornamos la del usuario)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Por ahora solo mostramos la empresa del usuario autenticado
        if (!$user->empresa) {
            return response()->json(['empresas' => []]);
        }

        return response()->json([
            'empresas' => [[
                'id' => $user->empresa->id,
                'nombre' => $user->empresa->nombre,
                'rut' => $user->empresa->rut,
                'email' => $user->empresa->email,
                'telefono' => $user->empresa->telefono,
                'direccion' => $user->empresa->direccion,
                'activa' => $user->empresa->activa,
                'plan' => $user->empresa->plan ? [
                    'id' => $user->empresa->plan->id,
                    'nombre' => $user->empresa->plan->nombre,
                    'precio' => $user->empresa->plan->precio
                ] : null,
                'created_at' => $user->empresa->created_at
            ]]
        ]);
    }

    /**
     * Crear una nueva empresa (para registro inicial)
     */
    public function store(Request $request)
    {
        // Esta funcionalidad será implementada más adelante
        return response()->json(['error' => 'Funcionalidad no implementada aún'], 501);
    }

    /**
     * Mostrar información de una empresa
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        // Solo puede ver su propia empresa
        if ($user->empresa_id != $id) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $empresa = $user->empresa;
        $empresa->load(['plan', 'rubros', 'addons.planAddon']);

        return response()->json([
            'empresa' => [
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
                'rut' => $empresa->rut,
                'email' => $empresa->email,
                'telefono' => $empresa->telefono,
                'direccion' => $empresa->direccion,
                'activa' => $empresa->activa,
                'plan' => $empresa->plan ? [
                    'id' => $empresa->plan->id,
                    'nombre' => $empresa->plan->nombre,
                    'precio' => $empresa->plan->precio,
                    'limite_usuarios' => $empresa->plan->limite_usuarios,
                    'limite_sucursales' => $empresa->plan->limite_sucursales
                ] : null,
                'rubros' => $empresa->rubros->map(function ($rubro) {
                    return [
                        'id' => $rubro->id,
                        'nombre' => $rubro->nombre,
                        'descripcion' => $rubro->descripcion
                    ];
                }),
                'addons' => $empresa->addons->map(function ($addon) {
                    return [
                        'id' => $addon->id,
                        'plan_addon_id' => $addon->plan_addon_id,
                        'nombre' => $addon->planAddon->nombre ?? 'N/A',
                        'precio' => $addon->planAddon->precio ?? 0,
                        'activo' => $addon->activo,
                        'fecha_inicio' => $addon->created_at
                    ];
                }),
                'created_at' => $empresa->created_at,
                'updated_at' => $empresa->updated_at
            ]
        ]);
    }

    /**
     * Actualizar información de la empresa
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        // Solo el owner puede actualizar la empresa
        if (!$user->isOwner() || $user->empresa_id != $id) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $empresa = $user->empresa;

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'rut' => [
                'sometimes',
                'required',
                'string',
                'max:12',
                Rule::unique('empresas')->ignore($empresa->id)
            ],
            'email' => 'sometimes|required|email|max:255',
            'telefono' => 'sometimes|nullable|string|max:20',
            'direccion' => 'sometimes|nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $empresa->update($request->only([
            'nombre', 'rut', 'email', 'telefono', 'direccion'
        ]));

        return response()->json([
            'message' => 'Empresa actualizada exitosamente',
            'empresa' => [
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
                'rut' => $empresa->rut,
                'email' => $empresa->email,
                'telefono' => $empresa->telefono,
                'direccion' => $empresa->direccion,
                'updated_at' => $empresa->updated_at
            ]
        ]);
    }

    /**
     * Eliminar empresa (desactivar)
     */
    public function destroy(Request $request, $id)
    {
        // Esta funcionalidad será implementada más adelante con validaciones especiales
        return response()->json(['error' => 'Funcionalidad no implementada aún'], 501);
    }

    /**
     * Obtener usuarios de la empresa
     */
    public function usuarios(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->empresa_id != $id) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $users = User::where('empresa_id', $id)
                    ->with('sucursal')
                    ->when($request->activo !== null, function ($query) use ($request) {
                        return $query->where('activo', $request->boolean('activo'));
                    })
                    ->get();

        return response()->json([
            'usuarios' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol_principal' => $user->rol_principal,
                    'role_info' => $user->getRoleInfo(),
                    'sucursal' => $user->sucursal ? [
                        'id' => $user->sucursal->id,
                        'nombre' => $user->sucursal->nombre
                    ] : null,
                    'activo' => $user->activo,
                    'last_login_at' => $user->last_login_at
                ];
            })
        ]);
    }

    /**
     * Obtener sucursales de la empresa
     */
    public function sucursales(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->empresa_id != $id) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        // Implementación pendiente - requiere SucursalController
        return response()->json(['sucursales' => []]);
    }

    /**
     * Obtener rubros de la empresa
     */
    public function rubros(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->empresa_id != $id) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $empresa = $user->empresa;
        $empresa->load('rubros');

        return response()->json([
            'rubros' => $empresa->rubros->map(function ($rubro) {
                return [
                    'id' => $rubro->id,
                    'nombre' => $rubro->nombre,
                    'descripcion' => $rubro->descripcion,
                    'activo' => $rubro->activo
                ];
            })
        ]);
    }

    /**
     * Obtener addons de la empresa
     */
    public function addons(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->empresa_id != $id) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $empresa = $user->empresa;
        $empresa->load('addons.planAddon');

        return response()->json([
            'addons' => $empresa->addons->map(function ($addon) {
                return [
                    'id' => $addon->id,
                    'plan_addon_id' => $addon->plan_addon_id,
                    'nombre' => $addon->planAddon->nombre ?? 'N/A',
                    'precio' => $addon->planAddon->precio ?? 0,
                    'activo' => $addon->activo,
                    'fecha_inicio' => $addon->created_at
                ];
            })
        ]);
    }

    /**
     * Alternar estado de la empresa
     */
    public function toggleStatus(Request $request, $id)
    {
        // Esta funcionalidad será implementada más adelante
        return response()->json(['error' => 'Funcionalidad no implementada aún'], 501);
    }

    /**
     * Sincronizar rubros de la empresa
     */
    public function syncRubros(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isOwner() || $user->empresa_id != $id) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $validator = Validator::make($request->all(), [
            'rubros' => 'required|array',
            'rubros.*' => 'required|integer|exists:rubros,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $empresa = $user->empresa;
        $empresa->rubros()->sync($request->rubros);

        return response()->json([
            'message' => 'Rubros sincronizados exitosamente',
            'rubros_count' => count($request->rubros)
        ]);
    }

    /**
     * Agregar addon a la empresa
     */
    public function attachAddon(Request $request, $id)
    {
        // Esta funcionalidad será implementada más adelante
        return response()->json(['error' => 'Funcionalidad no implementada aún'], 501);
    }

    /**
     * Quitar addon de la empresa
     */
    public function detachAddon(Request $request, $id, $addonId)
    {
        // Esta funcionalidad será implementada más adelante
        return response()->json(['error' => 'Funcionalidad no implementada aún'], 501);
    }
}
