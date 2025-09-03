<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Services\TenantService;
use App\Services\InventarioService;
use App\Services\FeatureFlagService;

class ProductoController extends Controller
{
    protected $tenantService;
    protected $inventarioService;
    protected $featureFlagService;

    public function __construct(
        TenantService $tenantService,
        InventarioService $inventarioService,
        FeatureFlagService $featureFlagService
    ) {
        $this->tenantService = $tenantService;
        $this->inventarioService = $inventarioService;
        $this->featureFlagService = $featureFlagService;
    }

    public function index(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        $sucursal = $this->tenantService->getSucursal();

        $query = Producto::forEmpresa($empresa->id)
            ->with(['categoria', 'stocks' => function ($q) use ($sucursal) {
                $q->forSucursal($sucursal->id);
            }]);

        // Filtros
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_interno', 'like', "%{$search}%")
                  ->orWhere('codigo_barra', 'like', "%{$search}%");
            });
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        // Ordenamiento
        $query->orderBy($request->get('sort', 'nombre'), $request->get('direction', 'asc'));

        $productos = $query->paginate(20)->appends($request->query());

        $categorias = Categoria::forEmpresa($empresa->id)->active()->get();

        $stats = [
            'total_productos' => Producto::forEmpresa($empresa->id)->active()->count(),
            'productos_stock_bajo' => $this->inventarioService->getProductosStockBajo()->count(),
            'categorias_count' => $categorias->count(),
            'valor_inventario' => $this->calculateValorInventario($sucursal->id),
        ];

        return view('productos.index', compact('productos', 'categorias', 'stats', 'empresa', 'sucursal'));
    }

    public function create()
    {
        $empresa = $this->tenantService->getEmpresa();
        $categorias = Categoria::forEmpresa($empresa->id)->active()->get();

        if ($categorias->isEmpty()) {
            return redirect()->route('productos.index')
                ->with('warning', 'Debes crear al menos una categoría antes de agregar productos.');
        }

        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:150',
            'codigo_interno' => 'nullable|string|max:30',
            'codigo_barra' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:20',
            'stock_minimo' => 'nullable|numeric|min:0',
            'stock_maximo' => 'nullable|numeric|min:0',
            'stock_actual' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $empresa = $this->tenantService->getEmpresa();
        $sucursal = $this->tenantService->getSucursal();

        // Subir imagen si existe
        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->move(public_path('img/productos'), $nombreImagen);
            $imagenUrl = asset('img/productos/' . $nombreImagen);
        }

        $producto = Producto::create([
            'empresa_id' => $empresa->id,
            'categoria_id' => $request->categoria_id,
            'nombre' => $request->nombre,
            'codigo_interno' => $request->codigo_interno,
            'codigo_barra' => $request->codigo_barra,
            'descripcion' => $request->descripcion,
            'precio_costo' => $request->precio_costo,
            'precio_venta' => $request->precio_venta,
            'incluye_igv' => $empresa->precio_incluye_igv ?? false,
            'unidad_medida' => $request->unidad_medida,
            'permite_decimales' => in_array($request->unidad_medida, ['kg', 'gr', 'lt', 'ml']),
            'maneja_stock' => $request->boolean('maneja_stock'),
            'stock_minimo' => $request->stock_minimo ?? 0,
            'stock_maximo' => $request->stock_maximo ?? 0,
            'imagen_url' => $imagenUrl,
            'activo' => $request->boolean('activo', true),
        ]);

        // Crear stock inicial si maneja stock
        if ($producto->maneja_stock && $request->filled('stock_actual') && $request->stock_actual > 0) {
            $this->inventarioService->registrarMovimiento(
                $producto->id,
                $sucursal->id,
                'entrada',
                $request->stock_actual,
                $producto->precio_costo,
                'Stock inicial',
                request()->user()->id
            );
        }

        // Actualizar contador de productos en categoría
        $producto->categoria->updateProductosCount();

        return redirect()->route('productos.show', $producto)
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Producto $producto)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        // Verificar que el producto pertenece a la empresa
        if ($producto->empresa_id !== $empresa->id) {
            abort(404);
        }

        $sucursal = $this->tenantService->getSucursal();

        $producto->load([
            'categoria',
            'stocks' => function ($query) use ($sucursal) {
                $query->forSucursal($sucursal->id);
            },
            'movimientos' => function ($query) use ($sucursal) {
                $query->forSucursal($sucursal->id)
                      ->latest('fecha_movimiento')
                      ->limit(10)
                      ->with('usuario');
            }
        ]);

        $stockActual = $producto->getStockSucursal($sucursal->id);

        return view('productos.show', compact('producto', 'stockActual', 'empresa', 'sucursal'));
    }

    public function edit(Producto $producto)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if ($producto->empresa_id !== $empresa->id) {
            abort(404);
        }

        $categorias = Categoria::forEmpresa($empresa->id)->active()->get();

        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if ($producto->empresa_id !== $empresa->id) {
            abort(404);
        }

        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:150',
            'codigo_interno' => 'nullable|string|max:30',
            'codigo_barra' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:20',
            'stock_minimo' => 'nullable|numeric|min:0',
            'stock_maximo' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $categoriaAnterior = $producto->categoria_id;

        // Subir nueva imagen si existe
        $imagenUrl = $producto->imagen_url;
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen_url && file_exists(public_path('img/productos/' . basename($producto->imagen_url)))) {
                unlink(public_path('img/productos/' . basename($producto->imagen_url)));
            }
            
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->move(public_path('img/productos'), $nombreImagen);
            $imagenUrl = asset('img/productos/' . $nombreImagen);
        }

        $producto->update([
            'categoria_id' => $request->categoria_id,
            'nombre' => $request->nombre,
            'codigo_interno' => $request->codigo_interno,
            'codigo_barra' => $request->codigo_barra,
            'descripcion' => $request->descripcion,
            'precio_costo' => $request->precio_costo,
            'precio_venta' => $request->precio_venta,
            'unidad_medida' => $request->unidad_medida,
            'permite_decimales' => in_array($request->unidad_medida, ['kg', 'gr', 'lt', 'ml']),
            'maneja_stock' => $request->boolean('maneja_stock'),
            'stock_minimo' => $request->stock_minimo ?? 0,
            'stock_maximo' => $request->stock_maximo ?? 0,
            'imagen_url' => $imagenUrl,
            'activo' => $request->boolean('activo', true),
        ]);

        // Actualizar contadores de categorías si cambió
        if ($categoriaAnterior !== $producto->categoria_id) {
            Categoria::find($categoriaAnterior)?->updateProductosCount();
            $producto->categoria->updateProductosCount();
        }

        return redirect()->route('productos.show', $producto)
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if ($producto->empresa_id !== $empresa->id) {
            abort(404);
        }

        // Verificar que no tenga stock
        $tieneStock = $producto->stocks()->where('cantidad_actual', '>', 0)->exists();
        
        if ($tieneStock) {
            return redirect()->route('productos.show', $producto)
                ->with('error', 'No se puede eliminar un producto que tiene stock.');
        }

        $categoria = $producto->categoria;
        $producto->delete();
        
        $categoria->updateProductosCount();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function ajustarStock(Request $request, Producto $producto)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if ($producto->empresa_id !== $empresa->id) {
            abort(404);
        }

        if (!$producto->maneja_stock) {
            return redirect()->route('productos.show', $producto)
                ->with('error', 'Este producto no maneja control de stock.');
        }

        $request->validate([
            'tipo' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        $sucursal = $this->tenantService->getSucursal();
        
        try {
            $this->inventarioService->registrarMovimiento(
                $producto->id,
                $sucursal->id,
                $request->tipo,
                $request->cantidad,
                $producto->precio_costo,
                $request->motivo,
                request()->user()->id
            );

            return redirect()->route('productos.show', $producto)
                ->with('success', 'Stock ajustado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('productos.show', $producto)
                ->with('error', 'Error al ajustar el stock: ' . $e->getMessage());
        }
    }

    /**
     * Buscar productos para autocompletado y POS
     */
    public function search(Request $request)
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

            $productos = Producto::forEmpresa($empresa->id)
                ->active()
                ->where(function ($query) use ($term) {
                    $query->where('nombre', 'like', "%{$term}%")
                          ->orWhere('codigo_barras', 'like', "%{$term}%")
                          ->orWhere('codigo_interno', 'like', "%{$term}%");
                })
                ->with(['categoria', 'stocks'])
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $productos,
                'message' => 'Búsqueda completada'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
        }
    }

    /**
     * Buscar producto por código de barras
     */
    public function buscarPorCodigoBarras(string $codigo)
    {
        try {
            $empresa = $this->tenantService->getEmpresa();

            $producto = Producto::forEmpresa($empresa->id)
                ->active()
                ->where('codigo_barras', $codigo)
                ->with(['categoria', 'stocks'])
                ->first();

            if (!$producto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $producto,
                'message' => 'Producto encontrado'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar producto'
            ], 500);
        }
    }

    /**
     * Obtener productos con stock bajo
     */
    public function stockBajo()
    {
        try {
            $productos = $this->inventarioService->getProductosStockBajo();

            return response()->json([
                'success' => true,
                'data' => $productos,
                'message' => 'Productos con stock bajo obtenidos'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos'
            ], 500);
        }
    }

    private function calculateValorInventario(int $sucursalId): float
    {
        $empresa = $this->tenantService->getEmpresa();
        
        return Producto::forEmpresa($empresa->id)
            ->whereHas('stocks', function ($query) use ($sucursalId) {
                $query->forSucursal($sucursalId)->where('cantidad_actual', '>', 0);
            })
            ->with(['stocks' => function ($query) use ($sucursalId) {
                $query->forSucursal($sucursalId);
            }])
            ->get()
            ->sum(function ($producto) {
                $stock = $producto->stocks->first();
                return $stock ? $stock->cantidad_actual * $stock->costo_promedio : 0;
            });
    }
}
