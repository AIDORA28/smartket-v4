<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\ProductoStock;
use App\Models\Inventory\Producto;
use App\Models\Core\Sucursal;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductoStockController extends Controller
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
                $query = ProductoStock::with(['producto.categoria', 'producto.marca', 'sucursal']);

                // Filtros
                if ($request->filled('sucursal_id')) {
                    $query->where('sucursal_id', $request->sucursal_id);
                }

                if ($request->filled('producto_id')) {
                    $query->where('producto_id', $request->producto_id);
                }

                if ($request->filled('stock_bajo')) {
                    $query->whereRaw('cantidad_actual <= (
                        SELECT COALESCE(stock_minimo, 0) 
                        FROM productos 
                        WHERE productos.id = producto_stocks.producto_id
                    )');
                }

                if ($request->filled('sin_stock')) {
                    $query->where('cantidad_actual', '<=', 0);
                }

                if ($request->filled('search')) {
                    $query->whereHas('producto', function($q) use ($request) {
                        $q->buscar($request->search);
                    });
                }

                // Paginación
                $perPage = $request->input('per_page', 15);
                $stocks = $query->ordenado()->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'data' => $stocks,
                    'message' => 'Stocks obtenidos exitosamente'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener stocks: ' . $e->getMessage()
                ], 500);
            }
        }

        // Respuesta para Inertia
        $stocks = ProductoStock::with(['producto.categoria', 'producto.marca', 'sucursal'])
            ->ordenado()
            ->paginate(15);

        return inertia('Inventory/Stocks/Index', [
            'stocks' => $stocks,
            'sucursales' => Sucursal::activas()->ordenado()->get(['id', 'nombre']),
            'filters' => $request->only(['sucursal_id', 'producto_id', 'stock_bajo', 'sin_stock', 'search'])
        ]);
    }

    public function show(ProductoStock $productoStock)
    {
        try {
            $productoStock->load([
                'producto.categoria',
                'producto.marca',
                'producto.unidadMedida',
                'sucursal',
                'movimientos' => function($query) {
                    $query->with(['usuario'])
                          ->orderBy('created_at', 'desc')
                          ->limit(20);
                }
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $productoStock,
                    'message' => 'Stock obtenido exitosamente'
                ]);
            }

            return inertia('Inventory/Stocks/Show', [
                'stock' => $productoStock
            ]);

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener stock'
                ], 500);
            }

            return redirect()->route('inventory.stocks.index');
        }
    }

    /**
     * API: Ajustar stock de un producto en una sucursal
     */
    public function ajustar(Request $request, ProductoStock $productoStock)
    {
        $request->validate([
            'cantidad' => 'required|numeric',
            'tipo' => 'required|string|in:ajuste_positivo,ajuste_negativo,conteo_fisico',
            'motivo' => 'required|string|max:255',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $cantidadAnterior = $productoStock->cantidad_actual;
            $nuevaCantidad = 0;

            switch ($request->tipo) {
                case 'ajuste_positivo':
                    $nuevaCantidad = $cantidadAnterior + $request->cantidad;
                    break;
                case 'ajuste_negativo':
                    $nuevaCantidad = max(0, $cantidadAnterior - $request->cantidad);
                    break;
                case 'conteo_fisico':
                    $nuevaCantidad = max(0, $request->cantidad);
                    break;
            }

            // Actualizar stock
            $productoStock->update([
                'cantidad_actual' => $nuevaCantidad,
                'updated_at' => now()
            ]);

            // Registrar movimiento
            $productoStock->producto->movimientos()->create([
                'empresa_id' => Auth::user()->empresa_actual_id,
                'sucursal_id' => $productoStock->sucursal_id,
                'usuario_id' => Auth::id(),
                'tipo_movimiento' => $request->tipo,
                'cantidad' => abs($nuevaCantidad - $cantidadAnterior),
                'stock_anterior' => $cantidadAnterior,
                'stock_actual' => $nuevaCantidad,
                'motivo' => $request->motivo,
                'observaciones' => $request->observaciones,
                'fecha_movimiento' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $productoStock->fresh(['producto', 'sucursal']),
                'message' => 'Stock ajustado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Transferir stock entre sucursales
     */
    public function transferir(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'sucursal_origen_id' => 'required|exists:sucursales,id',
            'sucursal_destino_id' => 'required|exists:sucursales,id|different:sucursal_origen_id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Buscar stocks
            $stockOrigen = ProductoStock::where('producto_id', $request->producto_id)
                ->where('sucursal_id', $request->sucursal_origen_id)
                ->first();

            $stockDestino = ProductoStock::firstOrCreate([
                'producto_id' => $request->producto_id,
                'sucursal_id' => $request->sucursal_destino_id,
            ], [
                'stock_actual' => 0,
                'stock_reservado' => 0,
            ]);

            // Verificar stock disponible
            if (!$stockOrigen || $stockOrigen->cantidad_disponible < $request->cantidad) {
                throw new \Exception('Stock insuficiente en sucursal origen');
            }

            // Actualizar stocks
            $stockOrigen->decrement('cantidad_actual', $request->cantidad);
            $stockDestino->increment('cantidad_actual', $request->cantidad);

            // Registrar movimientos
            $producto = Producto::find($request->producto_id);
            
            // Movimiento de salida
            $producto->movimientos()->create([
                'empresa_id' => Auth::user()->empresa_actual_id,
                'sucursal_id' => $request->sucursal_origen_id,
                'usuario_id' => Auth::id(),
                'tipo_movimiento' => 'transferencia_salida',
                'cantidad' => $request->cantidad,
                'stock_anterior' => $stockOrigen->cantidad_actual + $request->cantidad,
                'stock_posterior' => $stockOrigen->cantidad_actual,
                'observaciones' => $request->motivo . ($request->observaciones ? ' - ' . $request->observaciones : ''),
                'fecha_movimiento' => now(),
                'referencia_id' => $request->sucursal_destino_id,
            ]);

            // Movimiento de entrada
            $producto->movimientos()->create([
                'empresa_id' => Auth::user()->empresa_actual_id,
                'sucursal_id' => $request->sucursal_destino_id,
                'usuario_id' => Auth::id(),
                'tipo_movimiento' => 'transferencia_entrada',
                'cantidad' => $request->cantidad,
                'stock_anterior' => $stockDestino->cantidad_actual - $request->cantidad,
                'stock_posterior' => $stockDestino->cantidad_actual,
                'observaciones' => $request->motivo . ($request->observaciones ? ' - ' . $request->observaciones : ''),
                'fecha_movimiento' => now(),
                'referencia_id' => $request->sucursal_origen_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'stock_origen' => $stockOrigen->fresh(['producto', 'sucursal']),
                    'stock_destino' => $stockDestino->fresh(['producto', 'sucursal'])
                ],
                'message' => 'Transferencia realizada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error en transferencia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Resumen de stock por producto
     */
    public function resumen(Request $request)
    {
        try {
            $query = ProductoStock::with(['producto.categoria', 'sucursal'])
                ->select([
                    'producto_id',
                    DB::raw('SUM(cantidad_actual) as stock_total'),
                    DB::raw('SUM(cantidad_reservada) as reservado_total'),
                    DB::raw('COUNT(sucursal_id) as sucursales_count')
                ])
                ->groupBy('producto_id');

            if ($request->filled('categoria_id')) {
                $query->whereHas('producto', function($q) use ($request) {
                    $q->where('categoria_id', $request->categoria_id);
                });
            }

            $resumen = $query->get();

            return response()->json([
                'success' => true,
                'data' => $resumen,
                'message' => 'Resumen de stock obtenido'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen'
            ], 500);
        }
    }

    /**
     * API: Productos con stock bajo
     */
    public function stockBajo()
    {
        try {
            $stocksBajos = ProductoStock::with(['producto.categoria', 'sucursal'])
                ->whereRaw('cantidad_actual <= (
                    SELECT COALESCE(stock_minimo, 0) 
                    FROM productos 
                    WHERE productos.id = producto_stocks.producto_id
                )')
                ->ordenado()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stocksBajos,
                'message' => 'Productos con stock bajo obtenidos'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener stock bajo'
            ], 500);
        }
    }
}
