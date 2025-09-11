<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantService;
use App\Models\Inventory\Producto;
use App\Models\Inventory\Categoria;
use App\Models\Inventory\Marca;
use App\Models\Inventory\UnidadMedida;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * 📦 Lista principal de productos con datos reales
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Verificación de autenticación
            if (!$user) {
                return redirect()->route('login');
            }
            
            // Obtener empresa_id del usuario autenticado
            $empresaId = $user->empresa_id ?? 1;
            
            // Cargar productos con relaciones - con manejo de errores
            $productos = Producto::with(['categoria', 'marca', 'unidadMedida'])
                ->forEmpresa($empresaId)
                ->active()
                ->paginate(15);
            
            // Si no hay productos para esta empresa, mostrar mensaje informativo
            if ($productos->isEmpty()) {
                Log::info("No se encontraron productos para empresa_id: {$empresaId}");
            }
            
            // Respuesta con datos reales
            return Inertia::render('Products', [
                'auth' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'rol_principal' => $user->rol_principal ?? 'cajero',
                    ]
                ],
                'productos' => [
                    'data' => $productos->items(),
                    'current_page' => $productos->currentPage(),
                    'last_page' => $productos->lastPage(),
                    'per_page' => $productos->perPage(),
                    'total' => $productos->total(),
                ],
                'categorias' => Categoria::where('empresa_id', $empresaId)->active()->get(),
                'marcas' => Marca::where('empresa_id', $empresaId)->activas()->get(),
                'unidades' => UnidadMedida::where('empresa_id', $empresaId)->activas()->get(),
                'stats' => [
                    'total_productos' => Producto::forEmpresa($empresaId)->count(),
                    'total_categorias' => Categoria::where('empresa_id', $empresaId)->count(),
                    'total_marcas' => Marca::where('empresa_id', $empresaId)->count(),
                    'total_unidades' => UnidadMedida::where('empresa_id', $empresaId)->count(),
                    'productos_activos' => Producto::forEmpresa($empresaId)->active()->count(),
                    'stock_bajo' => Producto::forEmpresa($empresaId)->conStockBajo()->count(),
                ],
                'empresa' => [
                    'id' => $user->empresa_id ?? 1,
                    'nombre' => 'SmartKet'
                ]
            ]);
            
        } catch (\Exception $e) {
            // En caso de error, devolver estructura vacía pero correcta
            return Inertia::render('Products', [
                'auth' => [
                    'user' => [
                        'id' => 0,
                        'name' => 'Usuario',
                        'email' => '',
                        'rol_principal' => 'cajero',
                    ]
                ],
                'productos' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 15,
                    'total' => 0,
                ],
                'categorias' => [],
                'marcas' => [],
                'unidades' => [],
                'stats' => [
                    'total_productos' => 0,
                    'total_categorias' => 0,
                    'total_marcas' => 0,
                    'total_unidades' => 0,
                    'productos_activos' => 0,
                    'stock_bajo' => 0,
                ],
                'empresa' => [
                    'id' => 1,
                    'nombre' => 'SmartKet'
                ],
                'error' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 👁️ Mostrar detalle de un producto específico
     */
    public function show(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Verificación de autenticación
            if (!$user) {
                return redirect()->route('login');
            }
            
            // Obtener empresa_id del usuario autenticado
            $empresaId = $user->empresa_id ?? 1;
            
            // Buscar el producto con todas sus relaciones
            $producto = Producto::with([
                'categoria', 
                'marca', 
                'unidadMedida', 
                'stocks.sucursal',
                'movimientos' => function($query) {
                    $query->latest()->limit(10);
                }
            ])
            ->forEmpresa($empresaId)
            ->findOrFail($id);
            
            // Calcular stock total
            $stockTotal = $producto->getStockTotal();
            
            return Inertia::render('ProductDetail', [
                'auth' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'rol_principal' => $user->rol_principal ?? 'cajero',
                    ]
                ],
                'producto' => $producto,
                'stock_total' => $stockTotal,
                'empresa' => [
                    'id' => $user->empresa_id ?? 1,
                    'nombre' => 'SmartKet'
                ]
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'Producto no encontrado: ' . $e->getMessage());
        }
    }
}

