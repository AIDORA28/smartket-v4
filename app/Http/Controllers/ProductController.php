<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantService;
use App\Models\Producto;
use App\Models\Categoria;

class ProductController extends Controller
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
            return Inertia::render('Products', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        // Filtros
        $search = $request->get('search', '');
        $category = $request->get('category', '');
        $status = $request->get('status', '');

        // Query base
        $query = Producto::where('empresa_id', $empresa->id)
            ->with(['categoria', 'stocks']);

        // Aplicar filtros
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('codigo_barras', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('categoria_id', $category);
        }

        if ($status === 'active') {
            $query->where('activo', true);
        } elseif ($status === 'inactive') {
            $query->where('activo', false);
        } elseif ($status === 'low_stock') {
            $query->whereHas('stocks', function($q) {
                $q->havingRaw('SUM(cantidad_actual) <= (SELECT stock_minimo FROM productos WHERE productos.id = producto_stocks.producto_id)');
            });
        }

        // Obtener productos paginados
        $products = $query->orderBy('nombre')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($producto) {
                $stockTotal = $producto->stocks->sum('cantidad_actual') ?? 0;
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio_venta,
                    'stock' => $stockTotal,
                    'minimo' => $producto->stock_minimo,
                    'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                    'imagen' => $producto->imagen,
                    'activo' => $producto->activo,
                    'created_at' => $producto->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $producto->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        // Obtener categorías para filtros
        $categories = Cache::remember("categories_{$empresa->id}", 3600, function () use ($empresa) {
            return Categoria::where('empresa_id', $empresa->id)
                ->orderBy('nombre')
                ->pluck('nombre', 'id')
                ->toArray();
        });

        return Inertia::render('Products', [
            'products' => $products,
            'categories' => array_values($categories),
            'filters' => [
                'search' => $search,
                'category' => $category,
                'status' => $status,
            ]
        ]);
    }

    public function show($id): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $product = Producto::where('empresa_id', $empresa->id)
            ->with(['categoria', 'stocks', 'lotes'])
            ->findOrFail($id);

        $stockTotal = $product->stocks->sum('cantidad_actual') ?? 0;
        
        $productData = [
            'id' => $product->id,
            'nombre' => $product->nombre,
            'descripcion' => $product->descripcion,
            'codigo_barras' => $product->codigo_barras,
            'precio_compra' => $product->precio_compra,
            'precio_venta' => $product->precio_venta,
            'stock_actual' => $stockTotal,
            'stock_minimo' => $product->stock_minimo,
            'categoria' => $product->categoria->nombre ?? 'Sin categoría',
            'imagen' => $product->imagen,
            'activo' => $product->activo,
            'created_at' => $product->created_at->format('Y-m-d H:i:s'),
            'lotes' => $product->lotes->map(function($lote) {
                return [
                    'id' => $lote->id,
                    'codigo' => $lote->codigo_lote,
                    'fecha_vencimiento' => $lote->fecha_vencimiento->format('Y-m-d'),
                    'cantidad' => $lote->getStockActual(),
                ];
            }),
        ];

        return Inertia::render('ProductDetail', [
            'product' => $productData
        ]);
    }

    public function create(): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $categories = Categoria::where('empresa_id', $empresa->id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return Inertia::render('ProductForm', [
            'categories' => $categories,
            'product' => null
        ]);
    }

    public function store(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'codigo_barras' => 'nullable|string|unique:productos,codigo_barras',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|max:2048',
            'activo' => 'boolean',
        ]);

        $validated['empresa_id'] = $empresa->id;
        
        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $product = Producto::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function edit($id): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $product = Producto::where('empresa_id', $empresa->id)
            ->with('categoria')
            ->findOrFail($id);

        $categories = Categoria::where('empresa_id', $empresa->id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return Inertia::render('ProductForm', [
            'categories' => $categories,
            'product' => [
                'id' => $product->id,
                'nombre' => $product->nombre,
                'descripcion' => $product->descripcion,
                'codigo_barras' => $product->codigo_barras,
                'precio_compra' => $product->precio_compra,
                'precio_venta' => $product->precio_venta,
                'stock_minimo' => $product->stock_minimo,
                'categoria_id' => $product->categoria_id,
                'imagen' => $product->imagen,
                'activo' => $product->activo,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $product = Producto::where('empresa_id', $empresa->id)->findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'codigo_barras' => 'nullable|string|unique:productos,codigo_barras,' . $id,
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|max:2048',
            'activo' => 'boolean',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy($id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $product = Producto::where('empresa_id', $empresa->id)->findOrFail($id);
        
        // Verificar si tiene ventas asociadas
        if ($product->ventaDetalles()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el producto porque tiene ventas asociadas');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
