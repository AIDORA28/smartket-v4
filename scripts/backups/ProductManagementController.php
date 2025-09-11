<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\TenantService;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\Log;

class ProductManagementController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Versión con datos reales pero manejando errores paso a paso
     */
    public function index(Request $request): Response
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            if (!$empresa) {
                return Inertia::render('Products', [
                    'error' => 'No hay empresa configurada',
                    'categorias' => [],
                    'marcas' => [],
                    'unidades' => [],
                    'productos' => [],
                    'stats' => [
                        'total_productos' => 0,
                        'total_categorias' => 0,
                        'total_marcas' => 0,
                        'total_unidades' => 0,
                        'productos_activos' => 0,
                        'stock_bajo' => 0
                    ],
                    'empresa' => [
                        'id' => 0,
                        'nombre' => 'Error'
                    ]
                ]);
            }

            Log::info('ProductManagementController: Empresa encontrada: ' . $empresa->id);

            // Paso 1: Obtener categorías básicas
            try {
                $categorias = Categoria::where('empresa_id', $empresa->id)
                    ->select('id', 'nombre', 'descripcion', 'activo', 'created_at')
                    ->orderBy('nombre')
                    ->get()
                    ->map(function ($categoria) {
                        return [
                            'id' => $categoria->id,
                            'nombre' => $categoria->nombre,
                            'descripcion' => $categoria->descripcion,
                            'activo' => $categoria->activo,
                            'productos_count' => 0, // Por ahora 0
                            'created_at' => $categoria->created_at->format('Y-m-d H:i:s')
                        ];
                    });
                Log::info('ProductManagementController: Categorías obtenidas: ' . $categorias->count());
            } catch (\Exception $e) {
                Log::error('Error obteniendo categorías: ' . $e->getMessage());
                $categorias = collect([]);
            }

            // Paso 2: Obtener marcas básicas
            try {
                $marcas = Marca::where('empresa_id', $empresa->id)
                    ->select('id', 'nombre', 'descripcion', 'activo', 'created_at')
                    ->orderBy('nombre')
                    ->get()
                    ->map(function ($marca) {
                        return [
                            'id' => $marca->id,
                            'nombre' => $marca->nombre,
                            'descripcion' => $marca->descripcion,
                            'activo' => $marca->activo,
                            'productos_count' => 0, // Por ahora 0
                            'created_at' => $marca->created_at->format('Y-m-d H:i:s')
                        ];
                    });
                Log::info('ProductManagementController: Marcas obtenidas: ' . $marcas->count());
            } catch (\Exception $e) {
                Log::error('Error obteniendo marcas: ' . $e->getMessage());
                $marcas = collect([]);
            }

            // Paso 3: Obtener unidades básicas
            try {
                $unidades = UnidadMedida::where('empresa_id', $empresa->id)
                    ->select('id', 'nombre', 'simbolo', 'activo', 'created_at')
                    ->orderBy('nombre')
                    ->get()
                    ->map(function ($unidad) {
                        return [
                            'id' => $unidad->id,
                            'nombre' => $unidad->nombre,
                            'simbolo' => $unidad->simbolo,
                            'abreviacion' => $unidad->simbolo,
                            'tipo' => 'General',
                            'activo' => $unidad->activo,
                            'productos_count' => 0, // Por ahora 0
                            'created_at' => $unidad->created_at->format('Y-m-d H:i:s')
                        ];
                    });
                Log::info('ProductManagementController: Unidades obtenidas: ' . $unidades->count());
            } catch (\Exception $e) {
                Log::error('Error obteniendo unidades: ' . $e->getMessage());
                $unidades = collect([]);
            }

            // Por ahora, productos vacío para evitar errores complejos
            $productos = collect([]);

            $stats = [
                'total_productos' => $productos->count(),
                'total_categorias' => $categorias->count(),
                'total_marcas' => $marcas->count(),
                'total_unidades' => $unidades->count(),
                'productos_activos' => 0,
                'stock_bajo' => 0
            ];

            Log::info('ProductManagementController: Enviando respuesta con stats: ', $stats);

            return Inertia::render('Products', [
                'productos' => $productos,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'unidades' => $unidades,
                'categoriasParaFiltros' => $categorias->pluck('nombre')->toArray(),
                'filters' => [
                    'search' => $request->get('search', ''),
                    'category' => $request->get('category', ''),
                    'status' => $request->get('status', ''),
                ],
                'empresa' => [
                    'id' => $empresa->id,
                    'nombre' => $empresa->nombre,
                ],
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en ProductManagementController: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return Inertia::render('Products', [
                'error' => 'Error interno: ' . $e->getMessage(),
                'categorias' => [],
                'marcas' => [],
                'unidades' => [],
                'productos' => [],
                'stats' => [
                    'total_productos' => 0,
                    'total_categorias' => 0,
                    'total_marcas' => 0,
                    'total_unidades' => 0,
                    'productos_activos' => 0,
                    'stock_bajo' => 0
                ],
                'empresa' => [
                    'id' => 0,
                    'nombre' => 'Error'
                ]
            ]);
        }
    }
}

