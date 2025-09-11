<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserWebController extends Controller
{
    /**
     * Mostrar la lista de usuarios
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Solo owners y admins pueden ver la lista de usuarios
        if (!$user->isOwner() && !$user->isAdmin()) {
            abort(403, 'Sin permisos para ver usuarios');
        }

        // Construir la consulta base
        $query = User::where('empresa_id', $user->empresa_id)
                    ->with(['empresa', 'sucursal']);

        // Aplicar filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('rol_principal', $request->role);
        }

        if ($request->filled('empresa')) {
            $query->where('empresa_id', $request->empresa);
        }

        if ($request->filled('status')) {
            $active = $request->status === 'active';
            $query->where('activo', $active);
        }

        // Paginación
        $users = $query->paginate(15)->withQueryString();

        // Formatear datos para el frontend
        $usersData = $users->through(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol_principal' => $user->rol_principal,
                'empresa' => $user->empresa ? [
                    'id' => $user->empresa->id,
                    'nombre' => $user->empresa->nombre
                ] : null,
                'sucursal' => $user->sucursal ? [
                    'id' => $user->sucursal->id,
                    'nombre' => $user->sucursal->nombre
                ] : null,
                'activo' => $user->activo,
                'created_at' => $user->created_at->toISOString(),
                'last_login' => $user->last_login_at ? $user->last_login_at->toISOString() : null
            ];
        });

        // Roles disponibles (usando key como identificador único)
        $roles = [
            ['key' => 'owner', 'name' => 'owner', 'display_name' => 'Propietario', 'description' => 'Acceso completo al sistema', 'permissions' => [], 'level' => 1],
            ['key' => 'admin', 'name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Gestión completa excepto configuración', 'permissions' => [], 'level' => 2],
            ['key' => 'vendedor', 'name' => 'vendedor', 'display_name' => 'Vendedor', 'description' => 'Gestión de ventas y clientes', 'permissions' => [], 'level' => 3],
            ['key' => 'cajero', 'name' => 'cajero', 'display_name' => 'Cajero', 'description' => 'Operación de caja y ventas', 'permissions' => [], 'level' => 4],
            ['key' => 'almacenero', 'name' => 'almacenero', 'display_name' => 'Almacenero', 'description' => 'Gestión de inventario', 'permissions' => [], 'level' => 5]
        ];

        // Empresas disponibles (para filtros)
        $empresas = Empresa::where('activo', true)->get(['id', 'nombre']);

        return Inertia::render('Core/Users/Index', [
            'users' => $usersData,
            'filters' => [
                'search' => $request->search,
                'role' => $request->role,
                'empresa' => $request->empresa,
                'status' => $request->status,
            ],
            'roles' => $roles,
            'empresas' => $empresas
        ]);
    }

    /**
     * Mostrar el formulario de creación
     */
    public function create(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isOwner()) {
            abort(403, 'Solo el propietario puede crear usuarios');
        }

        $roles = [
            ['key' => 'admin', 'name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Gestión completa excepto configuración', 'permissions' => [], 'level' => 2],
            ['key' => 'vendedor', 'name' => 'vendedor', 'display_name' => 'Vendedor', 'description' => 'Gestión de ventas y clientes', 'permissions' => [], 'level' => 3],
            ['key' => 'cajero', 'name' => 'cajero', 'display_name' => 'Cajero', 'description' => 'Operación de caja y ventas', 'permissions' => [], 'level' => 4],
            ['key' => 'almacenero', 'name' => 'almacenero', 'display_name' => 'Almacenero', 'description' => 'Gestión de inventario', 'permissions' => [], 'level' => 5]
        ];

        $empresas = Empresa::where('activo', true)->get(['id', 'nombre']);
        $sucursales = Sucursal::where('empresa_id', $user->empresa_id)
                             ->where('activo', true)
                             ->get(['id', 'nombre']);

        return Inertia::render('Core/Users/Create', [
            'roles' => $roles,
            'empresas' => $empresas,
            'sucursales' => $sucursales
        ]);
    }

    /**
     * Almacenar un nuevo usuario
     */
    public function store(Request $request)
    {
        $currentUser = $request->user();
        
        if (!$currentUser->isOwner()) {
            abort(403, 'Solo el propietario puede crear usuarios');
        }

        // Validación
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'rol_principal' => 'required|string|in:admin,vendedor,cajero,almacenero',
            'empresa_id' => 'required|exists:empresas,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'activo' => 'boolean'
        ]);

        // Verificar límites del plan
        if (!$currentUser->canCreateNewUser()) {
            return redirect()->back()
                           ->withErrors(['error' => 'Has alcanzado el límite de usuarios de tu plan'])
                           ->withInput();
        }

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => bcrypt($request->password),
            'empresa_id' => $request->empresa_id,
            'sucursal_id' => $request->sucursal_id,
            'rol_principal' => $request->rol_principal,
            'activo' => $request->boolean('activo', true),
            'email_verified_at' => now()
        ]);

        return redirect()->route('core.users.index')
                       ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Mostrar el formulario de edición
     */
    public function edit(Request $request, User $user)
    {
        $currentUser = $request->user();
        
        if (!$currentUser->isOwner() && !$currentUser->isAdmin()) {
            abort(403, 'Sin permisos para editar usuarios');
        }

        // No se puede editar a sí mismo con este formulario
        if ($currentUser->id === $user->id) {
            return redirect()->route('profile.edit')
                           ->with('info', 'Para editar tu propio perfil, usa la sección de perfil');
        }

        $roles = [
            ['key' => 'admin', 'name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Gestión completa excepto configuración', 'permissions' => [], 'level' => 2],
            ['key' => 'vendedor', 'name' => 'vendedor', 'display_name' => 'Vendedor', 'description' => 'Gestión de ventas y clientes', 'permissions' => [], 'level' => 3],
            ['key' => 'cajero', 'name' => 'cajero', 'display_name' => 'Cajero', 'description' => 'Operación de caja y ventas', 'permissions' => [], 'level' => 4],
            ['key' => 'almacenero', 'name' => 'almacenero', 'display_name' => 'Almacenero', 'description' => 'Gestión de inventario', 'permissions' => [], 'level' => 5]
        ];

        $empresas = Empresa::where('activo', true)->get(['id', 'nombre']);
        $sucursales = Sucursal::where('empresa_id', $currentUser->empresa_id)
                             ->where('activo', true)
                             ->get(['id', 'nombre']);

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'rol_principal' => $user->rol_principal,
            'empresa_id' => $user->empresa_id,
            'sucursal_id' => $user->sucursal_id,
            'activo' => $user->activo
        ];

        return Inertia::render('Core/Users/Edit', [
            'user' => $userData,
            'roles' => $roles,
            'empresas' => $empresas,
            'sucursales' => $sucursales,
            'isEditing' => true
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $currentUser = $request->user();
        
        if (!$currentUser->isOwner() && !$currentUser->isAdmin()) {
            abort(403, 'Sin permisos para actualizar usuarios');
        }

        // No se puede editar a sí mismo
        if ($currentUser->id === $user->id) {
            return redirect()->back()
                           ->withErrors(['error' => 'No puedes editarte a ti mismo'])
                           ->withInput();
        }

        // Validación
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'rol_principal' => 'required|string|in:admin,vendedor,cajero,almacenero',
            'empresa_id' => 'required|exists:empresas,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'activo' => 'boolean'
        ]);

        // Actualizar usuario
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'rol_principal' => $request->rol_principal,
            'empresa_id' => $request->empresa_id,
            'sucursal_id' => $request->sucursal_id,
            'activo' => $request->boolean('activo', true)
        ];

        if ($request->filled('password')) {
            $updateData['password_hash'] = bcrypt($request->password);
        }

        $user->update($updateData);

        return redirect()->route('core.users.index')
                       ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(Request $request, User $user)
    {
        $currentUser = $request->user();
        
        if (!$currentUser->isOwner()) {
            abort(403, 'Solo el propietario puede eliminar usuarios');
        }

        // No se puede eliminar a sí mismo
        if ($currentUser->id === $user->id) {
            return redirect()->back()
                           ->with('error', 'No puedes eliminarte a ti mismo');
        }

        // No se puede eliminar al owner principal
        if ($user->isOwner()) {
            return redirect()->back()
                           ->with('error', 'No se puede eliminar al propietario');
        }

        $user->delete();

        return redirect()->route('core.users.index')
                       ->with('success', 'Usuario eliminado exitosamente');
    }
}
