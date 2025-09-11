<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\UnidadMedida;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;

class UnidadMedidaController extends Controller
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
                $query = UnidadMedida::query();

                // Filtros
                if ($request->filled('activa')) {
                    $query->where('activa', $request->boolean('activa'));
                }

                if ($request->filled('tipo')) {
                    $query->where('tipo', $request->tipo);
                }

                if ($request->filled('search')) {
                    $query->buscar($request->search);
                }

                $unidades = $query->ordenado()
                    ->withCount('productos')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $unidades,
                    'message' => 'Unidades de medida obtenidas exitosamente'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener unidades de medida: ' . $e->getMessage()
                ], 500);
            }
        }

        // Respuesta para Inertia
        $unidades = UnidadMedida::ordenado()
            ->withCount('productos')
            ->paginate(15);

        return inertia('Inventory/UnidadesMedida/Index', [
            'unidades' => $unidades,
            'tipos' => UnidadMedida::TIPOS
        ]);
    }

    public function create()
    {
        return inertia('Inventory/UnidadesMedida/Create', [
            'tipos' => UnidadMedida::TIPOS
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'abreviacion' => 'required|string|max:10|unique:unidades_medida,abreviacion,NULL,id,empresa_id,' . Auth::user()->empresa_actual_id,
            'tipo' => 'required|string|in:' . implode(',', array_keys(UnidadMedida::TIPOS)),
            'icono' => 'nullable|string|max:50',
        ]);

        try {
            $unidadMedida = UnidadMedida::create([
                'empresa_id' => Auth::user()->empresa_actual_id,
                'nombre' => $request->nombre,
                'abreviacion' => strtoupper($request->abreviacion),
                'tipo' => $request->tipo,
                'icono' => $request->icono ?? '⚖️',
                'activa' => $request->boolean('activa', true),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $unidadMedida,
                    'message' => 'Unidad de medida creada exitosamente'
                ], 201);
            }

            return redirect()->route('inventory.unidades-medida.index')
                ->with('success', 'Unidad de medida creada exitosamente');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear unidad de medida: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al crear unidad de medida']);
        }
    }

    public function show(UnidadMedida $unidadMedida)
    {
        try {
            $unidadMedida->load([
                'productos' => function($query) {
                    $query->with(['categoria', 'marca'])->activos();
                }
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $unidadMedida,
                    'message' => 'Unidad de medida obtenida exitosamente'
                ]);
            }

            return inertia('Inventory/UnidadesMedida/Show', [
                'unidadMedida' => $unidadMedida
            ]);

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener unidad de medida'
                ], 500);
            }

            return redirect()->route('inventory.unidades-medida.index');
        }
    }

    public function edit(UnidadMedida $unidadMedida)
    {
        return inertia('Inventory/UnidadesMedida/Edit', [
            'unidadMedida' => $unidadMedida,
            'tipos' => UnidadMedida::TIPOS
        ]);
    }

    public function update(Request $request, UnidadMedida $unidadMedida)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'abreviacion' => 'required|string|max:10|unique:unidades_medida,abreviacion,' . $unidadMedida->id . ',id,empresa_id,' . Auth::user()->empresa_actual_id,
            'tipo' => 'required|string|in:' . implode(',', array_keys(UnidadMedida::TIPOS)),
            'icono' => 'nullable|string|max:50',
        ]);

        try {
            $unidadMedida->update([
                'nombre' => $request->nombre,
                'abreviacion' => strtoupper($request->abreviacion),
                'tipo' => $request->tipo,
                'icono' => $request->icono ?? $unidadMedida->icono,
                'activa' => $request->boolean('activa', $unidadMedida->activa),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $unidadMedida->fresh(),
                    'message' => 'Unidad de medida actualizada exitosamente'
                ]);
            }

            return redirect()->route('inventory.unidades-medida.index')
                ->with('success', 'Unidad de medida actualizada exitosamente');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar unidad de medida: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al actualizar unidad de medida']);
        }
    }

    public function destroy(UnidadMedida $unidadMedida)
    {
        try {
            if ($unidadMedida->tieneProductos()) {
                $message = 'No se puede eliminar una unidad de medida que tiene productos asociados';
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return back()->withErrors(['error' => $message]);
            }

            $unidadMedida->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Unidad de medida eliminada exitosamente'
                ]);
            }

            return redirect()->route('inventory.unidades-medida.index')
                ->with('success', 'Unidad de medida eliminada exitosamente');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar unidad de medida'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al eliminar unidad de medida']);
        }
    }

    /**
     * API: Toggle estado activa/inactiva
     */
    public function toggle(UnidadMedida $unidadMedida)
    {
        try {
            $unidadMedida->toggleActiva();

            return response()->json([
                'success' => true,
                'data' => $unidadMedida->fresh(),
                'message' => 'Estado de unidad de medida actualizado'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado'
            ], 500);
        }
    }

    /**
     * API: Obtener unidades activas para selects
     */
    public function activas()
    {
        try {
            $unidades = UnidadMedida::activas()
                ->ordenado()
                ->get(['id', 'nombre', 'abreviacion', 'tipo', 'productos_count'])
                ->map(function($unidad) {
                    return $unidad->toSelectOption();
                });

            return response()->json([
                'success' => true,
                'data' => $unidades,
                'message' => 'Unidades de medida activas obtenidas'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener unidades de medida'
            ], 500);
        }
    }
}
