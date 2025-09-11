<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\InventarioMovimiento;
use App\Models\Inventory\Producto;
use App\Models\Core\Sucursal;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InventarioMovimientoController extends Controller
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
                $query = InventarioMovimiento::with(['producto.categoria', 'sucursal', 'usuario']);

                // Filtros
                if ($request->filled('producto_id')) {
                    $query->where('producto_id', $request->producto_id);
                }

                if ($request->filled('sucursal_id')) {
                    $query->where('sucursal_id', $request->sucursal_id);
                }

                if ($request->filled('tipo_movimiento')) {
                    $query->where('tipo_movimiento', $request->tipo_movimiento);
                }

                if ($request->filled('fecha_desde')) {
                    $query->whereDate('fecha_movimiento', '>=', $request->fecha_desde);
                }

                if ($request->filled('fecha_hasta')) {
                    $query->whereDate('fecha_movimiento', '<=', $request->fecha_hasta);
                }

                if ($request->filled('search')) {
                    $query->where(function($q) use ($request) {
                        $q->where('observaciones', 'ILIKE', "%{$request->search}%")
                          ->orWhereHas('producto', function($subQ) use ($request) {
                              $subQ->buscar($request->search);
                          });
                    });
                }

                // Paginación
                $perPage = $request->input('per_page', 15);
                $movimientos = $query->ordenado()->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'data' => $movimientos,
                    'message' => 'Movimientos obtenidos exitosamente'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener movimientos: ' . $e->getMessage()
                ], 500);
            }
        }

        // Respuesta para Inertia
        $movimientos = InventarioMovimiento::with(['producto.categoria', 'sucursal', 'usuario'])
            ->ordenado()
            ->paginate(15);

        return inertia('Inventory/Movimientos/Index', [
            'movimientos' => $movimientos,
            'productos' => Producto::activos()->ordenado()->get(['id', 'codigo', 'nombre']),
            'sucursales' => Sucursal::activas()->ordenado()->get(['id', 'nombre']),
            'tiposMovimiento' => InventarioMovimiento::TIPOS_MOVIMIENTO,
            'filters' => $request->only([
                'producto_id', 'sucursal_id', 'tipo_movimiento', 
                'fecha_desde', 'fecha_hasta', 'search'
            ])
        ]);
    }

    public function show(InventarioMovimiento $inventarioMovimiento)
    {
        try {
            $inventarioMovimiento->load([
                'producto.categoria',
                'producto.marca',
                'producto.unidadMedida',
                'sucursal',
                'usuario'
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $inventarioMovimiento,
                    'message' => 'Movimiento obtenido exitosamente'
                ]);
            }

            return inertia('Inventory/Movimientos/Show', [
                'movimiento' => $inventarioMovimiento
            ]);

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener movimiento'
                ], 500);
            }

            return redirect()->route('inventory.movimientos.index');
        }
    }

    /**
     * API: Reporte de movimientos por período
     */
    public function reporte(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'producto_id' => 'nullable|exists:productos,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'tipo_movimiento' => 'nullable|string',
        ]);

        try {
            $query = InventarioMovimiento::with(['producto', 'sucursal'])
                ->whereBetween('fecha_movimiento', [
                    $request->fecha_desde,
                    $request->fecha_hasta
                ]);

            if ($request->filled('producto_id')) {
                $query->where('producto_id', $request->producto_id);
            }

            if ($request->filled('sucursal_id')) {
                $query->where('sucursal_id', $request->sucursal_id);
            }

            if ($request->filled('tipo_movimiento')) {
                $query->where('tipo_movimiento', $request->tipo_movimiento);
            }

            $movimientos = $query->ordenado()->get();

            // Agrupar por tipo de movimiento
            $resumen = $movimientos->groupBy('tipo_movimiento')->map(function($grupo) {
                return [
                    'cantidad_movimientos' => $grupo->count(),
                    'total_entradas' => $grupo->where('es_entrada', true)->sum('cantidad'),
                    'total_salidas' => $grupo->where('es_entrada', false)->sum('cantidad'),
                    'movimientos' => $grupo->take(10) // Solo primeros 10 para el resumen
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'movimientos' => $movimientos,
                    'resumen' => $resumen,
                    'totales' => [
                        'total_movimientos' => $movimientos->count(),
                        'total_entradas' => $movimientos->where('es_entrada', true)->sum('cantidad'),
                        'total_salidas' => $movimientos->where('es_entrada', false)->sum('cantidad'),
                    ]
                ],
                'message' => 'Reporte generado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Historial de un producto específico
     */
    public function historialProducto(Request $request, Producto $producto)
    {
        try {
            $query = $producto->movimientos()->with(['sucursal', 'usuario']);

            if ($request->filled('sucursal_id')) {
                $query->where('sucursal_id', $request->sucursal_id);
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_movimiento', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_movimiento', '<=', $request->fecha_hasta);
            }

            $movimientos = $query->ordenado()->paginate(20);

            return response()->json([
                'success' => true,
                'data' => [
                    'producto' => $producto->load(['categoria', 'marca', 'unidadMedida']),
                    'movimientos' => $movimientos
                ],
                'message' => 'Historial obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial'
            ], 500);
        }
    }

    /**
     * API: Estadísticas de movimientos
     */
    public function estadisticas(Request $request)
    {
        try {
            $fechaDesde = $request->input('fecha_desde', Carbon::now()->startOfMonth());
            $fechaHasta = $request->input('fecha_hasta', Carbon::now()->endOfMonth());

            $movimientos = InventarioMovimiento::whereBetween('fecha_movimiento', [$fechaDesde, $fechaHasta]);

            if ($request->filled('sucursal_id')) {
                $movimientos->where('sucursal_id', $request->sucursal_id);
            }

            $estadisticas = [
                'total_movimientos' => $movimientos->count(),
                'movimientos_por_tipo' => $movimientos->get()
                    ->groupBy('tipo_movimiento')
                    ->map->count(),
                'movimientos_por_dia' => $movimientos->get()
                    ->groupBy(function($movimiento) {
                        return Carbon::parse($movimiento->fecha_movimiento)->format('Y-m-d');
                    })
                    ->map->count(),
                'productos_mas_movidos' => $movimientos->get()
                    ->groupBy('producto_id')
                    ->map(function($grupo) {
                        return [
                            'producto' => $grupo->first()->producto->nombre ?? 'N/A',
                            'cantidad_movimientos' => $grupo->count(),
                            'total_cantidad' => $grupo->sum('cantidad')
                        ];
                    })
                    ->sortByDesc('cantidad_movimientos')
                    ->take(10)
                    ->values(),
                'usuarios_mas_activos' => $movimientos->get()
                    ->groupBy('usuario_id')
                    ->map(function($grupo) {
                        return [
                            'usuario' => $grupo->first()->usuario->name ?? 'N/A',
                            'cantidad_movimientos' => $grupo->count()
                        ];
                    })
                    ->sortByDesc('cantidad_movimientos')
                    ->take(5)
                    ->values()
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ], 500);
        }
    }

    /**
     * API: Exportar movimientos a Excel/CSV
     */
    public function exportar(Request $request)
    {
        $request->validate([
            'formato' => 'required|string|in:excel,csv',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        try {
            $query = InventarioMovimiento::with(['producto', 'sucursal', 'usuario'])
                ->whereBetween('fecha_movimiento', [
                    $request->fecha_desde,
                    $request->fecha_hasta
                ]);

            if ($request->filled('producto_id')) {
                $query->where('producto_id', $request->producto_id);
            }

            if ($request->filled('sucursal_id')) {
                $query->where('sucursal_id', $request->sucursal_id);
            }

            $movimientos = $query->ordenado()->get();

            // Aquí implementarías la lógica de exportación
            // Por ahora retornamos los datos para que el frontend maneje la exportación

            return response()->json([
                'success' => true,
                'data' => $movimientos->map(function($movimiento) {
                    return [
                        'fecha' => $movimiento->fecha_movimiento->format('d/m/Y H:i'),
                        'producto' => $movimiento->producto->nombre,
                        'codigo_producto' => $movimiento->producto->codigo,
                        'sucursal' => $movimiento->sucursal->nombre,
                        'tipo_movimiento' => $movimiento->getTipoMovimientoTexto(),
                        'cantidad' => $movimiento->cantidad,
                        'stock_anterior' => $movimiento->stock_anterior,
                        'stock_actual' => $movimiento->stock_actual,
                        'motivo' => $movimiento->referencia_tipo ?? 'N/A',
                        'usuario' => $movimiento->usuario->name,
                    ];
                }),
                'message' => 'Datos preparados para exportación'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar: ' . $e->getMessage()
            ], 500);
        }
    }
}
