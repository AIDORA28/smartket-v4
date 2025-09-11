<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Listar usuarios de la empresa del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Solo owners y admins pueden ver la lista de usuarios
        if (!$user->isOwner() && !$user->isAdmin()) {
            return response()->json(['error' => 'Sin permisos para ver usuarios'], 403);
        }

        $users = User::where('empresa_id', $user->empresa_id)
                    ->with(['sucursal'])
                    ->when($request->activo !== null, function ($query) use ($request) {
                        return $query->where('activo', $request->boolean('activo'));
                    })
                    ->get();

        return response()->json([
            'users' => $users->map(function ($user) {
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
                    'last_login_at' => $user->last_login_at,
                    'created_at' => $user->created_at
                ];
            }),
            'available_roles' => $user->getAvailableRolesForAssignment(),
            'user_limit' => $user->getUserLimitForEmpresa(),
            'can_create_new' => $user->canCreateNewUser()
        ]);
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(Request $request)
    {
        $currentUser = $request->user();

        // Solo el owner puede crear usuarios
        if (!$currentUser->isOwner()) {
            return response()->json(['error' => 'Solo el propietario puede crear usuarios'], 403);
        }

        // Verificar límite de usuarios
        if (!$currentUser->canCreateNewUser()) {
            return response()->json([
                'error' => 'Has alcanzado el límite de usuarios de tu plan',
                'current_limit' => $currentUser->getUserLimitForEmpresa()
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($currentUser) {
                    return $query->where('empresa_id', $currentUser->empresa_id);
                })
            ],
            'password' => 'required|string|min:8|confirmed',
            'rol_principal' => 'required|string',
            'sucursal_id' => 'nullable|exists:sucursales,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar que el rol esté disponible
        if (!$currentUser->canAssignRole($request->rol_principal)) {
            return response()->json([
                'error' => 'El rol seleccionado no está disponible para tu empresa',
                'available_roles' => $currentUser->getAvailableRolesForAssignment()
            ], 422);
        }

        // Verificar sucursal si se proporciona
        if ($request->sucursal_id) {
            $sucursal = Sucursal::where('id', $request->sucursal_id)
                               ->where('empresa_id', $currentUser->empresa_id)
                               ->where('activa', true)
                               ->first();
            
            if (!$sucursal) {
                return response()->json(['error' => 'Sucursal no válida'], 422);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'rol_principal' => $request->rol_principal,
            'empresa_id' => $currentUser->empresa_id,
            'sucursal_id' => $request->sucursal_id,
            'activo' => true
        ]);

        $user->load('sucursal');

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => [
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
                'created_at' => $user->created_at
            ]
        ], 201);
    }

    /**
     * Mostrar información de un usuario específico
     */
    public function show(Request $request, $id)
    {
        $currentUser = $request->user();
        
        $user = User::where('id', $id)
                   ->where('empresa_id', $currentUser->empresa_id)
                   ->with('sucursal')
                   ->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Solo owners, admins o el mismo usuario pueden ver los detalles
        if (!$currentUser->isOwner() && !$currentUser->isAdmin() && $currentUser->id !== $user->id) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        return response()->json([
            'user' => [
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
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]
        ]);
    }

    /**
     * Actualizar información de un usuario
     */
    public function update(Request $request, $id)
    {
        $currentUser = $request->user();
        
        $user = User::where('id', $id)
                   ->where('empresa_id', $currentUser->empresa_id)
                   ->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Solo el owner puede actualizar otros usuarios
        // Los usuarios pueden actualizar su propia información básica
        $canUpdateRole = $currentUser->isOwner() && $user->id !== $currentUser->id;
        $canUpdateBasicInfo = $currentUser->isOwner() || $currentUser->id === $user->id;

        if (!$canUpdateBasicInfo) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)->where(function ($query) use ($currentUser) {
                    return $query->where('empresa_id', $currentUser->empresa_id);
                })
            ],
            'password' => 'sometimes|nullable|string|min:8|confirmed'
        ];

        if ($canUpdateRole) {
            $rules['rol_principal'] = 'sometimes|required|string';
            $rules['sucursal_id'] = 'sometimes|nullable|exists:sucursales,id';
            $rules['activo'] = 'sometimes|boolean';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar rol si se está actualizando
        if ($canUpdateRole && $request->has('rol_principal')) {
            if (!$currentUser->canAssignRole($request->rol_principal)) {
                return response()->json([
                    'error' => 'El rol seleccionado no está disponible',
                    'available_roles' => $currentUser->getAvailableRolesForAssignment()
                ], 422);
            }
        }

        // Verificar sucursal si se está actualizando
        if ($request->has('sucursal_id') && $request->sucursal_id) {
            $sucursal = Sucursal::where('id', $request->sucursal_id)
                               ->where('empresa_id', $currentUser->empresa_id)
                               ->where('activa', true)
                               ->first();
            
            if (!$sucursal) {
                return response()->json(['error' => 'Sucursal no válida'], 422);
            }
        }

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        
        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }
        
        if ($request->has('password') && $request->password) {
            $updateData['password_hash'] = Hash::make($request->password);
        }

        if ($canUpdateRole) {
            if ($request->has('rol_principal')) {
                $updateData['rol_principal'] = $request->rol_principal;
            }
            
            if ($request->has('sucursal_id')) {
                $updateData['sucursal_id'] = $request->sucursal_id;
            }
            
            if ($request->has('activo')) {
                $updateData['activo'] = $request->activo;
            }
        }

        $user->update($updateData);
        $user->load('sucursal');

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => [
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
                'updated_at' => $user->updated_at
            ]
        ]);
    }

    /**
     * Eliminar (desactivar) un usuario
     */
    public function destroy(Request $request, $id)
    {
        $currentUser = $request->user();
        
        // Solo el owner puede eliminar usuarios
        if (!$currentUser->isOwner()) {
            return response()->json(['error' => 'Solo el propietario puede eliminar usuarios'], 403);
        }

        $user = User::where('id', $id)
                   ->where('empresa_id', $currentUser->empresa_id)
                   ->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // No se puede eliminar a sí mismo
        if ($user->id === $currentUser->id) {
            return response()->json(['error' => 'No puedes eliminarte a ti mismo'], 422);
        }

        // Desactivar en lugar de eliminar para mantener integridad referencial
        $user->update(['activo' => false]);

        return response()->json(['message' => 'Usuario desactivado exitosamente']);
    }

    /**
     * Obtener roles disponibles para el usuario actual
     */
    public function availableRoles(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isOwner()) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        return response()->json([
            'roles' => $user->getAvailableRolesForAssignment(),
            'user_limit' => $user->getUserLimitForEmpresa(),
            'current_count' => User::where('empresa_id', $user->empresa_id)
                                  ->where('activo', true)
                                  ->count(),
            'can_create_new' => $user->canCreateNewUser()
        ]);
    }

    /**
     * Obtener información del perfil del usuario actual
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load('empresa', 'sucursal');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol_principal' => $user->rol_principal,
                'role_info' => $user->getRoleInfo(),
                'empresa' => $user->empresa ? [
                    'id' => $user->empresa->id,
                    'nombre' => $user->empresa->nombre,
                    'rut' => $user->empresa->rut
                ] : null,
                'sucursal' => $user->sucursal ? [
                    'id' => $user->sucursal->id,
                    'nombre' => $user->sucursal->nombre
                ] : null,
                'permissions' => $user->getRoleInfo()['permissions'] ?? [],
                'last_login_at' => $user->last_login_at
            ]
        ]);
    }
}
