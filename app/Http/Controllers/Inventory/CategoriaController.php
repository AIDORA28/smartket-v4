<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\Categoria;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
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
                $query = Categoria::query();

                // Filtros
                if ($request->filled('activa')) {
                    $query->where('activa', $request->boolean('activa'));
                }

                if ($request->filled('search')) {
                    $query->buscar($request->search);
                }

                $categorias = $query->ordenado()
                    ->withCount('productos')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $categorias,
                    'message' => 'Categorías obtenidas exitosamente'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener categorías: ' . $e->getMessage()
                ], 500);
            }
        }

        // Respuesta para Inertia
        $categorias = Categoria::ordenado()
            ->withCount('productos')
            ->get();

        return inertia('Inventory/Categorias/Index', [
            'categorias' => $categorias
        ]);
    }

    public function create()
    {
        return inertia('Inventory/Categorias/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'codigo' => 'nullable|string|max:20|unique:categorias,codigo,NULL,id,empresa_id,' . Auth::user()->empresa_actual_id,
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|size:7',
            'icono' => 'nullable|string|max:50',
        ]);

        try {
            $categoria = Categoria::create([
                'empresa_id' => Auth::user()->empresa_actual_id,
                'nombre' => $request->nombre,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'color' => $request->color ?? '#6B7280',
                'icono' => $request->icono ?? '📦',
                'activa' => $request->boolean('activa', true),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $categoria,
                    'message' => 'Categoría creada exitosamente'
                ], 201);
            }

            return redirect()->route('inventory.categorias.index')
                ->with('success', 'Categoría creada exitosamente');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear categoría: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al crear categoría']);
        }
    }

    public function show(Categoria $categoria)
    {
        try {
            $categoria->load(['productos' => function($query) {
                $query->with(['marca', 'unidadMedida'])->activos();
            }]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $categoria,
                    'message' => 'Categoría obtenida exitosamente'
                ]);
            }

            return inertia('Inventory/Categorias/Show', [
                'categoria' => $categoria
            ]);

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener categoría'
                ], 500);
            }

            return redirect()->route('inventory.categorias.index');
        }
    }

    public function edit(Categoria $categoria)
    {
        return inertia('Inventory/Categorias/Edit', [
            'categoria' => $categoria
        ]);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'codigo' => 'nullable|string|max:20|unique:categorias,codigo,' . $categoria->id . ',id,empresa_id,' . Auth::user()->empresa_actual_id,
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|size:7',
            'icono' => 'nullable|string|max:50',
        ]);

        try {
            $categoria->update([
                'nombre' => $request->nombre,
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'color' => $request->color ?? $categoria->color,
                'icono' => $request->icono ?? $categoria->icono,
                'activa' => $request->boolean('activa', $categoria->activa),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $categoria->fresh(),
                    'message' => 'Categoría actualizada exitosamente'
                ]);
            }

            return redirect()->route('inventory.categorias.index')
                ->with('success', 'Categoría actualizada exitosamente');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar categoría: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al actualizar categoría']);
        }
    }

    public function destroy(Categoria $categoria)
    {
        try {
            if ($categoria->tieneProductos()) {
                $message = 'No se puede eliminar una categoría que tiene productos asociados';
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return back()->withErrors(['error' => $message]);
            }

            $categoria->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría eliminada exitosamente'
                ]);
            }

            return redirect()->route('inventory.categorias.index')
                ->with('success', 'Categoría eliminada exitosamente');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar categoría'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al eliminar categoría']);
        }
    }

    /**
     * API: Toggle estado activa/inactiva
     */
    public function toggle(Categoria $categoria)
    {
        try {
            $categoria->toggleActiva();

            return response()->json([
                'success' => true,
                'data' => $categoria->fresh(),
                'message' => 'Estado de categoría actualizado'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado'
            ], 500);
        }
    }

    /**
     * API: Obtener categorías activas para selects
     */
    public function activas()
    {
        try {
            $categorias = Categoria::activas()
                ->ordenado()
                ->get(['id', 'nombre', 'color', 'icono', 'productos_count'])
                ->map(function($categoria) {
                    return $categoria->toSelectOption();
                });

            return response()->json([
                'success' => true,
                'data' => $categorias,
                'message' => 'Categorías activas obtenidas'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categorías'
            ], 500);
        }
    }
}
