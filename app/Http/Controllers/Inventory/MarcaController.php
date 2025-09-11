<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\Marca;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;

class MarcaController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index(Request $request)
    {
        // Si es una petición API, devolver JSON
        if ($request->expectsJson()) {
            try {
                $query = Marca::query();

                // Filtros
                if ($request->filled('activa')) {
                    $query->where('activa', $request->boolean('activa'));
                }

                if ($request->filled('search')) {
                    $query->buscar($request->search);
                }

                $marcas = $query->ordenado()
                    ->withCount('productos')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $marcas,
                    'message' => 'Marcas obtenidas exitosamente'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener marcas: ' . $e->getMessage()
                ], 500);
            }
        }

        // Respuesta para Inertia
        $marcas = Marca::ordenado()
            ->withCount('productos')
            ->paginate(15);

        return inertia('Inventory/Marcas/Index', [
            'marcas' => $marcas
        ]);
    }

    public function create()
    {
        return inertia('Inventory/Marcas/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'codigo' => 'nullable|string|max:20|unique:marcas,codigo,NULL,id,empresa_id,' . Auth::user()->empresa_actual_id,
            'descripcion' => 'nullable|string|max:500',
            'pais_origen' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        try {
            $marca = Marca::create([
                'empresa_id' => Auth::user()->empresa_actual_id,
                'nombre' => $request->nombre,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'pais_origen' => $request->pais_origen,
                'website' => $request->website,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'activa' => $request->boolean('activa', true),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $marca,
                    'message' => 'Marca creada exitosamente'
                ], 201);
            }

            return redirect()->route('inventory.marcas.index')
                ->with('success', 'Marca creada exitosamente');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear marca: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al crear marca']);
        }
    }

    public function show(Marca $marca)
    {
        try {
            $marca->load(['productos' => function($query) {
                $query->with(['categoria', 'unidadMedida'])->activos();
            }]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $marca,
                    'message' => 'Marca obtenida exitosamente'
                ]);
            }

            return inertia('Inventory/Marcas/Show', [
                'marca' => $marca
            ]);

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener marca'
                ], 500);
            }

            return redirect()->route('inventory.marcas.index');
        }
    }

    public function edit(Marca $marca)
    {
        return inertia('Inventory/Marcas/Edit', [
            'marca' => $marca
        ]);
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'codigo' => 'nullable|string|max:20|unique:marcas,codigo,' . $marca->id . ',id,empresa_id,' . Auth::user()->empresa_actual_id,
            'descripcion' => 'nullable|string|max:500',
            'pais_origen' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        try {
            $marca->update([
                'nombre' => $request->nombre,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'pais_origen' => $request->pais_origen,
                'website' => $request->website,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'activa' => $request->boolean('activa', $marca->activa),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $marca->fresh(),
                    'message' => 'Marca actualizada exitosamente'
                ]);
            }

            return redirect()->route('inventory.marcas.index')
                ->with('success', 'Marca actualizada exitosamente');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar marca: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al actualizar marca']);
        }
    }

    public function destroy(Marca $marca)
    {
        try {
            if ($marca->tieneProductos()) {
                $message = 'No se puede eliminar una marca que tiene productos asociados';
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return back()->withErrors(['error' => $message]);
            }

            $marca->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Marca eliminada exitosamente'
                ]);
            }

            return redirect()->route('inventory.marcas.index')
                ->with('success', 'Marca eliminada exitosamente');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar marca'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al eliminar marca']);
        }
    }

    /**
     * API: Toggle estado activa/inactiva
     */
    public function toggle(Marca $marca)
    {
        try {
            $marca->toggleActiva();

            return response()->json([
                'success' => true,
                'data' => $marca->fresh(),
                'message' => 'Estado de marca actualizado'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado'
            ], 500);
        }
    }

    /**
     * API: Obtener marcas activas para selects
     */
    public function activas()
    {
        try {
            $marcas = Marca::activas()
                ->ordenado()
                ->get(['id', 'nombre', 'pais_origen', 'productos_count'])
                ->map(function($marca) {
                    return $marca->toSelectOption();
                });

            return response()->json([
                'success' => true,
                'data' => $marcas,
                'message' => 'Marcas activas obtenidas'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener marcas'
            ], 500);
        }
    }
}
