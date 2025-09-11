<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\TenantService;
use App\Models\Core\Empresa;
use App\Models\Core\FeatureFlag;
use App\Models\Core\User;
use App\Models\Core\Sucursal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ConfigurationController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index(Request $request): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if (!$empresa) {
            return Inertia::render('Configurations', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        $activeTab = $request->get('tab', 'general');

        // Estadísticas generales
        $stats = [
            'total_usuarios' => User::where('empresa_id', $empresa->id)->count(),
            'total_sucursales' => Sucursal::where('empresa_id', $empresa->id)->count(),
            'features_activos' => FeatureFlag::where('empresa_id', $empresa->id)
                ->where('enabled', true)
                ->count(),
        ];

        // Datos según la pestaña activa
        $data = [];
        
        switch ($activeTab) {
            case 'empresa':
                $data = [
                    'empresa' => [
                        'id' => $empresa->id,
                        'nombre' => $empresa->nombre,
                        'nit' => $empresa->nit,
                        'direccion' => $empresa->direccion,
                        'telefono' => $empresa->telefono,
                        'email' => $empresa->email,
                        'logo' => $empresa->logo,
                        'moneda' => $empresa->moneda ?? 'USD',
                        'timezone' => $empresa->timezone ?? 'America/Bogota',
                    ]
                ];
                break;
                
            case 'usuarios':
                $users = User::where('empresa_id', $empresa->id)
                    ->with('roles')
                    ->paginate(10);
                    
                $data = [
                    'usuarios' => $users
                ];
                break;
                
            case 'sucursales':
                $sucursales = Sucursal::where('empresa_id', $empresa->id)
                    ->orderBy('nombre')
                    ->get();
                    
                $data = [
                    'sucursales' => $sucursales
                ];
                break;
                
            case 'features':
                $features = FeatureFlag::where('empresa_id', $empresa->id)
                    ->orderBy('feature_name')
                    ->get();
                    
                $data = [
                    'features' => $features
                ];
                break;
                
            case 'sistema':
                $data = [
                    'sistema' => [
                        'version' => config('app.version', '4.0.0'),
                        'laravel_version' => app()->version(),
                        'php_version' => PHP_VERSION,
                        'database' => config('database.default'),
                        'cache_driver' => config('cache.default'),
                        'queue_driver' => config('queue.default'),
                        'mail_driver' => config('mail.default'),
                        'storage_driver' => config('filesystems.default'),
                    ]
                ];
                break;
        }

        return Inertia::render('Configurations', [
            'activeTab' => $activeTab,
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function updateEmpresa(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'required|string|max:50',
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'moneda' => 'required|string|in:USD,COP,EUR,MXN',
            'timezone' => 'required|string',
        ]);

        $empresa->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Información de empresa actualizada exitosamente'
        ]);
    }

    public function createUser(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|string|in:admin,manager,employee',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'empresa_id' => $empresa->id,
        ]);

        // Asignar rol
        $user->assignRole($validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'user' => $user
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $user = User::where('empresa_id', $empresa->id)->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,manager,employee',
            'active' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'active' => $validated['active'] ?? true,
        ]);

        // Actualizar rol
        $user->syncRoles([$validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente'
        ]);
    }

    public function deleteUser($id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $user = User::where('empresa_id', $empresa->id)->findOrFail($id);
        
        // No permitir eliminar el último admin
        if ($user->hasRole('admin')) {
            $adminCount = User::where('empresa_id', $empresa->id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'admin');
                })
                ->count();
                
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el último administrador'
                ], 400);
            }
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }

    public function createSucursal(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'activa' => 'boolean',
        ]);

        $sucursal = Sucursal::create([
            'nombre' => $validated['nombre'],
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono'],
            'activa' => $validated['activa'] ?? true,
            'empresa_id' => $empresa->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sucursal creada exitosamente',
            'sucursal' => $sucursal
        ]);
    }

    public function updateSucursal(Request $request, $id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $sucursal = Sucursal::where('empresa_id', $empresa->id)->findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'activa' => 'boolean',
        ]);

        $sucursal->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sucursal actualizada exitosamente'
        ]);
    }

    public function deleteSucursal($id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $sucursal = Sucursal::where('empresa_id', $empresa->id)->findOrFail($id);
        
        // No permitir eliminar si es la única sucursal
        $sucursalCount = Sucursal::where('empresa_id', $empresa->id)->count();
        if ($sucursalCount <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la única sucursal'
            ], 400);
        }

        $sucursal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sucursal eliminada exitosamente'
        ]);
    }

    public function updateFeature(Request $request, $id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $feature = FeatureFlag::where('empresa_id', $empresa->id)->findOrFail($id);
        
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $feature->update(['enabled' => $validated['enabled']]);

        return response()->json([
            'success' => true,
            'message' => 'Feature actualizado exitosamente'
        ]);
    }
}

