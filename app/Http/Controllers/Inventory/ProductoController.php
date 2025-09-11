<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\Producto;
use App\Models\Inventory\Categoria;
use App\Models\Inventory\Marca;
use App\Models\Inventory\UnidadMedida;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductoController extends Controller
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
                $query = Producto::with(['categoria', 'marca', 'unidadMedida', 'stockPrincipal']);

                // Filtros
                if ($request->filled('categoria_id')) {
                    $query->where('categoria_id', $request->categoria_id);
                }

                if ($request->filled('marca_id')) {
                    $query->where('marca_id', $request->marca_id);
                }

                if ($request->filled('activo')) {
                    $query->where('activo', $request->boolean('activo'));
                }

                if ($request->filled('search')) {
                    $query->buscar($request->search);
                }

                // Paginación
                $perPage = $request->input('per_page', 15);
                $productos = $query->ordenado()->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'data' => $productos,
                    'message' => 'Productos obtenidos exitosamente'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener productos: ' . $e->getMessage()
                ], 500);
            }
        }

        // Respuesta para Inertia
        $productos = Producto::with(['categoria', 'marca', 'unidadMedida', 'stockPrincipal'])
            ->ordenado()
            ->paginate(15);

        return inertia('Inventory/Productos/Index', [
            'productos' => $productos,
            'categorias' => Categoria::activas()->ordenado()->get(['id', 'nombre', 'color']),
            'marcas' => Marca::activas()->ordenado()->get(['id', 'nombre']),
            'filters' => $request->only(['categoria_id', 'marca_id', 'activo', 'search'])
        ]);
    }

    public function create()
    {
        return inertia('Inventory/Productos/Create', [
            'categorias' => Categoria::activas()->ordenado()->get(['id', 'nombre', 'color']),
            'marcas' => Marca::activas()->ordenado()->get(['id', 'nombre']),
            'unidadesMedida' => UnidadMedida::activas()->ordenado()->get(['id', 'nombre', 'abreviacion'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'nullable|string|max:50|unique:productos,codigo,NULL,id,empresa_id,' . Auth::user()->empresa_actual_id,
            'codigo_barra' => 'nullable|string|max:50|unique:productos,codigo_barra,NULL,id,empresa_id,' . Auth::user()->empresa_actual_id,
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'precio_compra' => 'required|numeric|min:0',
            'margen_ganancia' => 'required|numeric|min:0|max:100',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'aplica_igv' => 'boolean',
            'usa_lotes' => 'boolean',
            'usa_series' => 'boolean',
            'perecedero' => 'boolean',
            'activo' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $producto = Producto::create([
                'empresa_id' => Auth::user()->empresa_actual_id,
                'codigo' => $request->codigo ?: $this->generarCodigo(),
                'codigo_barra' => $request->codigo_barra,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'marca_id' => $request->marca_id,
                'unidad_medida_id' => $request->unidad_medida_id,
                'precio_compra' => $request->precio_compra,
                'margen_ganancia' => $request->margen_ganancia,
                'precio_venta' => $request->precio_venta,
                'stock_minimo' => $request->stock_minimo ?? 0,
                'aplica_igv' => $request->boolean('aplica_igv', true),
                'usa_lotes' => $request->boolean('usa_lotes', false),
                'usa_series' => $request->boolean('usa_series', false),
                'perecedero' => $request->boolean('perecedero', false),
                'activo' => $request->boolean('activo', true),
            ]);

            // Crear stock inicial en sucursal principal
            $user = Auth::user();
            $sucursalPrincipal = $user->empresaActual->sucursalPrincipal();
            if ($sucursalPrincipal) {
                $producto->stockPorSucursal()->create([
                    'sucursal_id' => $sucursalPrincipal->id,
                    'stock_actual' => 0,
                    'stock_reservado' => 0,
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $producto->load(['categoria', 'marca', 'unidadMedida']),
                    'message' => 'Producto creado exitosamente'
                ], 201);
            }

            return redirect()->route('inventory.productos.index')
                ->with('success', 'Producto creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear producto: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al crear producto']);
        }
    }

    public function show(Producto $producto)
    {
        try {
            $producto->load([
                'categoria',
                'marca',
                'unidadMedida',
                'stockPorSucursal.sucursal',
                'movimientos' => function($query) {
                    $query->with(['usuario', 'sucursal'])
                          ->orderBy('created_at', 'desc')
                          ->limit(10);
                }
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $producto,
                    'message' => 'Producto obtenido exitosamente'
                ]);
            }

            return inertia('Inventory/Productos/Show', [
                'producto' => $producto
            ]);

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener producto'
                ], 500);
            }

            return redirect()->route('inventory.productos.index');
        }
    }

    public function edit(Producto $producto)
    {
        $producto->load(['categoria', 'marca', 'unidadMedida']);

        return inertia('Inventory/Productos/Edit', [
            'producto' => $producto,
            'categorias' => Categoria::activas()->ordenado()->get(['id', 'nombre', 'color']),
            'marcas' => Marca::activas()->ordenado()->get(['id', 'nombre']),
            'unidadesMedida' => UnidadMedida::activas()->ordenado()->get(['id', 'nombre', 'abreviacion'])
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'codigo' => 'nullable|string|max:50|unique:productos,codigo,' . $producto->id . ',id,empresa_id,' . Auth::user()->empresa_actual_id,
            'codigo_barra' => 'nullable|string|max:50|unique:productos,codigo_barra,' . $producto->id . ',id,empresa_id,' . Auth::user()->empresa_actual_id,
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'precio_compra' => 'required|numeric|min:0',
            'margen_ganancia' => 'required|numeric|min:0|max:100',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'aplica_igv' => 'boolean',
            'usa_lotes' => 'boolean',
            'usa_series' => 'boolean',
            'perecedero' => 'boolean',
            'activo' => 'boolean',
        ]);

        try {
            $producto->update([
                'codigo' => $request->codigo ?: $producto->codigo,
                'codigo_barra' => $request->codigo_barra,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'marca_id' => $request->marca_id,
                'unidad_medida_id' => $request->unidad_medida_id,
                'precio_compra' => $request->precio_compra,
                'margen_ganancia' => $request->margen_ganancia,
                'precio_venta' => $request->precio_venta,
                'stock_minimo' => $request->stock_minimo ?? $producto->stock_minimo,
                'aplica_igv' => $request->boolean('aplica_igv', $producto->aplica_igv),
                'usa_lotes' => $request->boolean('usa_lotes', $producto->usa_lotes),
                'usa_series' => $request->boolean('usa_series', $producto->usa_series),
                'perecedero' => $request->boolean('perecedero', $producto->perecedero),
                'activo' => $request->boolean('activo', $producto->activo),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $producto->fresh(['categoria', 'marca', 'unidadMedida']),
                    'message' => 'Producto actualizado exitosamente'
                ]);
            }

            return redirect()->route('inventory.productos.index')
                ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar producto: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al actualizar producto']);
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            // Verificar si el producto tiene movimientos
            if ($producto->tieneMovimientos()) {
                $message = 'No se puede eliminar un producto con movimientos de inventario';
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return back()->withErrors(['error' => $message]);
            }

            DB::beginTransaction();

            // Eliminar stocks
            $producto->stockPorSucursal()->delete();
            
            // Eliminar producto
            $producto->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado exitosamente'
                ]);
            }

            return redirect()->route('inventory.productos.index')
                ->with('success', 'Producto eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar producto'
                ], 500);
            }

            return back()->withErrors(['error' => 'Error al eliminar producto']);
        }
    }

    /**
     * API: Toggle estado activo/inactivo
     */
    public function toggle(Producto $producto)
    {
        try {
            $producto->toggleActivo();

            return response()->json([
                'success' => true,
                'data' => $producto->fresh(),
                'message' => 'Estado del producto actualizado'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado'
            ], 500);
        }
    }

    /**
     * API: Buscar productos para autocomplete
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            
            $productos = Producto::activos()
                ->buscar($query)
                ->with(['categoria', 'marca', 'unidadMedida'])
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $productos->map(function($producto) {
                    return [
                        'id' => $producto->id,
                        'codigo' => $producto->codigo,
                        'nombre' => $producto->nombre,
                        'precio_venta' => $producto->precio_venta,
                        'precio_con_igv' => $producto->getPrecioConIgv(),
                        'stock_total' => $producto->getStockTotal(),
                        'categoria' => $producto->categoria?->nombre,
                        'marca' => $producto->marca?->nombre,
                        'unidad' => $producto->unidadMedida?->abreviacion,
                    ];
                }),
                'message' => 'Productos encontrados'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en búsqueda'
            ], 500);
        }
    }

    /**
     * Generar código automático
     */
    private function generarCodigo(): string
    {
        do {
            $codigo = 'PROD-' . Str::random(8);
        } while (Producto::where('empresa_id', Auth::user()->empresa_actual_id)
                          ->where('codigo', $codigo)
                          ->exists());

        return $codigo;
    }
}
