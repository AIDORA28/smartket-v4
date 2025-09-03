<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Services\TenantService;

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
                $empresa = $this->tenantService->getEmpresa();
                
                $query = Categoria::forEmpresa($empresa->id);

                // Filtros
                if ($request->filled('activa')) {
                    if ($request->boolean('activa')) {
                        $query->active();
                    } else {
                        $query->inactive();
                    }
                }

                $categorias = $query->withProductosCount()
                    ->orderBy('nombre')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $categorias,
                    'message' => 'Categorías obtenidas exitosamente'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener categorías'
                ], 500);
            }
        }

        // Respuesta web original
        $empresa = $this->tenantService->getEmpresa();
        
        $categorias = Categoria::forEmpresa($empresa->id)
            ->withProductosCount()
            ->orderBy('nombre')
            ->get();

        return view('categorias.index', compact('categorias', 'empresa'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'icono' => 'required|string|max:2',
            'color' => 'required|string|size:7',
        ]);

        $empresa = $this->tenantService->getEmpresa();

        Categoria::create([
            'empresa_id' => $empresa->id,
            'nombre' => $request->nombre,
            'color' => $request->color,
            'icono' => $request->icono,
            'activa' => $request->boolean('activa', true),
        ]);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Categoria $categoria)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if ($categoria->empresa_id !== $empresa->id) {
            abort(404);
        }

        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if ($categoria->empresa_id !== $empresa->id) {
            abort(404);
        }

        $request->validate([
            'nombre' => 'required|string|max:120',
            'icono' => 'required|string|max:2',
            'color' => 'required|string|size:7',
        ]);

        $categoria->update([
            'nombre' => $request->nombre,
            'color' => $request->color,
            'icono' => $request->icono,
            'activa' => $request->boolean('activa', true),
        ]);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if ($categoria->empresa_id !== $empresa->id) {
            abort(404);
        }

        if ($categoria->productos()->count() > 0) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar una categoría que tiene productos asociados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }

    /**
     * Obtener solo categorías activas para API
     */
    public function activas()
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            $categorias = Categoria::forEmpresa($empresa->id)
                ->active()
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'color', 'icono']);

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

    /**
     * API: Mostrar categoría específica
     */
    public function show(Categoria $categoria)
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            if ($categoria->empresa_id !== $empresa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoría no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $categoria->load('productos'),
                'message' => 'Categoría obtenida exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categoría'
            ], 500);
        }
    }
}
