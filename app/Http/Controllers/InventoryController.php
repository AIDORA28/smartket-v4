<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantService;
use App\Models\Inventory\Producto;
use App\Models\Inventory\ProductoStock;
use App\Models\Inventory\InventarioMovimiento;
use App\Models\Inventory\Categoria;

class InventoryController extends Controller
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
            return Inertia::render('Inventory', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        // Filtros
        $filters = [
            'search' => $request->get('search', ''),
            'categoria' => $request->get('categoria', ''),
            'stock_filter' => $request->get('stock_filter', 'todos'), // todos, bajo, sin_stock, exceso
            'sort_by' => $request->get('sort_by', 'nombre'),
            'sort_direction' => $request->get('sort_direction', 'asc'),
        ];

        // Query base de productos
        $query = Producto::with(['categoria', 'stocks'])
            ->where('empresa_id', $empresa->id)
            ->where('activo', true);

        // Aplicar filtros de búsqueda
        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('codigo_interno', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('codigo_barra', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filtro por categoría
        if ($filters['categoria']) {
            $query->where('categoria_id', $filters['categoria']);
        }

        // Filtro por estado de stock
        switch ($filters['stock_filter']) {
            case 'bajo':
                $query->where(function ($q) {
                    $q->whereHas('stocks', function ($stockQuery) {
                        $stockQuery->whereColumn('cantidad_actual', '<=', 'productos.stock_minimo');
                    })->where('stock_minimo', '>', 0);
                });
                break;
            case 'sin_stock':
                $query->whereHas('stocks', function ($q) {
                    $q->where('cantidad_actual', '<=', 0);
                });
                break;
            case 'exceso':
                $query->where(function ($q) {
                    $q->whereHas('stocks', function ($stockQuery) {
                        $stockQuery->whereColumn('cantidad_actual', '>', 'productos.stock_maximo');
                    })->where('stock_maximo', '>', 0);
                });
                break;
        }

        // Ordenamiento
        switch ($filters['sort_by']) {
            case 'categoria':
                $query->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                      ->orderBy('categorias.nombre', $filters['sort_direction'])
                      ->select('productos.*');
                break;
            case 'stock':
                $query->leftJoin('producto_stocks', 'productos.id', '=', 'producto_stocks.producto_id')
                      ->orderBy('producto_stocks.cantidad_actual', $filters['sort_direction'])
                      ->select('productos.*');
                break;
            default:
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
        }

        $products = $query->paginate(20)->withQueryString();

        // Transformar datos para el frontend
        $products->getCollection()->transform(function ($producto) {
            $stockTotal = $producto->stocks->sum('cantidad_actual') ?? 0;
            $stockDisponible = $producto->stocks->sum('cantidad_disponible') ?? 0;
            $stockReservado = $producto->stocks->sum('cantidad_reservada') ?? 0;

            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo_interno' => $producto->codigo_interno,
                'codigo_barra' => $producto->codigo_barra,
                'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                'categoria_id' => $producto->categoria_id,
                'precio_costo' => $producto->precio_costo,
                'precio_venta' => $producto->precio_venta,
                'stock_actual' => $stockTotal,
                'stock_disponible' => $stockDisponible,
                'stock_reservado' => $stockReservado,
                'stock_minimo' => $producto->stock_minimo,
                'stock_maximo' => $producto->stock_maximo,
                'estado_stock' => $this->getStockStatus($stockTotal, $producto->stock_minimo, $producto->stock_maximo),
                'valor_inventario' => $stockTotal * $producto->precio_costo,
                'imagen' => $producto->imagen,
            ];
        });

        // Estadísticas del inventario
        $stats = $this->getInventoryStats($empresa->id);

        // Categorías para filtros
        $categories = Categoria::where('empresa_id', $empresa->id)
            ->where('activa', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return Inertia::render('Inventory', [
            'products' => $products,
            'stats' => $stats,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function movements(Request $request): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if (!$empresa) {
            return Inertia::render('Inventory/Movements', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        $query = InventarioMovimiento::with(['producto', 'usuario'])
            ->where('empresa_id', $empresa->id);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('producto', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_interno', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->get('tipo_movimiento'));
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_movimiento', '>=', $request->get('fecha_inicio'));
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_movimiento', '<=', $request->get('fecha_fin'));
        }

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->get('producto_id'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'fecha_movimiento');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $movements = $query->paginate(20)->withQueryString();

        // Transformar datos para el frontend
        $movements->getCollection()->transform(function ($movimiento) {
            return [
                'id' => $movimiento->id,
                'fecha' => $movimiento->fecha_movimiento,
                'producto' => $movimiento->producto->nombre,
                'tipo_movimiento' => $movimiento->tipo_movimiento,
                'cantidad' => $movimiento->cantidad,
                'costo_unitario' => $movimiento->costo_unitario,
                'stock_anterior' => $movimiento->stock_anterior,
                'stock_posterior' => $movimiento->stock_posterior,
                'referencia_tipo' => $movimiento->referencia_tipo,
                'observaciones' => $movimiento->observaciones,
                'usuario' => $movimiento->usuario->name ?? 'Sistema',
            ];
        });

        // Productos para filtros
        $products = Producto::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return Inertia::render('Inventory/Movements', [
            'movements' => $movements,
            'products' => $products,
            'filters' => $request->only(['search', 'tipo_movimiento', 'fecha_inicio', 'fecha_fin', 'producto_id']),
            'sort' => [
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
            ],
        ]);
    }

    public function adjustStock(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'tipo_ajuste' => 'required|in:entrada,salida,ajuste',
            'motivo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $producto = Producto::where('empresa_id', $empresa->id)
                ->findOrFail($validated['producto_id']);

            $stock = ProductoStock::firstOrCreate([
                'empresa_id' => $empresa->id,
                'producto_id' => $producto->id,
                'sucursal_id' => 1, // Por ahora sucursal fija
            ], [
                'cantidad_actual' => 0,
                'cantidad_reservada' => 0,
                'cantidad_disponible' => 0,
                'costo_promedio' => $producto->precio_costo
            ]);

            $stockAnterior = $stock->cantidad_actual;
            $cantidad = $validated['cantidad'];

            // Aplicar ajuste según tipo
            switch ($validated['tipo_ajuste']) {
                case 'entrada':
                    $stock->cantidad_actual += $cantidad;
                    $tipoMovimiento = 'entrada';
                    break;
                case 'salida':
                    $stock->cantidad_actual = max(0, $stock->cantidad_actual - $cantidad);
                    $tipoMovimiento = 'salida';
                    break;
                case 'ajuste':
                    $stock->cantidad_actual = $cantidad;
                    $tipoMovimiento = 'ajuste';
                    break;
            }

            $stock->cantidad_disponible = max(0, $stock->cantidad_actual - $stock->cantidad_reservada);
            $stock->ultimo_movimiento = now();
            $stock->save();

            // Registrar movimiento
            InventarioMovimiento::create([
                'empresa_id' => $empresa->id,
                'producto_id' => $producto->id,
                'sucursal_id' => 1,
                'usuario_id' => Auth::id(),
                'tipo_movimiento' => $tipoMovimiento,
                'referencia_tipo' => 'ajuste',
                'referencia_id' => null,
                'cantidad' => $cantidad,
                'costo_unitario' => $producto->precio_costo,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $stock->cantidad_actual,
                'observaciones' => $validated['motivo'],
                'fecha_movimiento' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock ajustado exitosamente',
                'nuevo_stock' => $stock->cantidad_actual
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar stock: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getInventoryStats($empresaId)
    {
        $cacheKey = "inventory_stats_{$empresaId}";
        
        return Cache::remember($cacheKey, 300, function () use ($empresaId) {
            $totalProductos = Producto::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->count();

            $productosStockBajo = Producto::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->where('stock_minimo', '>', 0)
                ->whereHas('stocks', function ($q) {
                    $q->whereColumn('cantidad_actual', '<=', 'productos.stock_minimo');
                })
                ->count();

            $productosSinStock = Producto::where('empresa_id', $empresaId)
                ->where('activo', true)
                ->whereHas('stocks', function ($q) {
                    $q->where('cantidad_actual', '<=', 0);
                })
                ->count();

            $valorInventario = ProductoStock::join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
                ->where('productos.empresa_id', $empresaId)
                ->where('producto_stocks.empresa_id', $empresaId)
                ->sum(DB::raw('producto_stocks.cantidad_actual * productos.precio_costo'));

            return [
                'total_productos' => $totalProductos,
                'productos_stock_bajo' => $productosStockBajo,
                'productos_sin_stock' => $productosSinStock,
                'valor_inventario' => $valorInventario,
            ];
        });
    }

    private function getStockStatus($stockActual, $stockMinimo, $stockMaximo)
    {
        if ($stockActual <= 0) {
            return 'sin_stock';
        } elseif ($stockMinimo > 0 && $stockActual <= $stockMinimo) {
            return 'bajo';
        } elseif ($stockMaximo > 0 && $stockActual >= $stockMaximo) {
            return 'exceso';
        }
        return 'normal';
    }
}

