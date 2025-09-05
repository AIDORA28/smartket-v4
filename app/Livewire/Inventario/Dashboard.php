<?php

namespace App\Livewire\Inventario;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Producto;
use App\Models\ProductoStock;
use App\Models\InventarioMovimiento;
use App\Models\Categoria;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    use WithPagination;

    // Filtros y búsqueda
    public $search = '';
    public $categoriaFiltro = '';
    public $stockFiltro = 'todos'; // todos, bajo, sin_stock, exceso
    public $ordenarPor = 'nombre'; // nombre, stock, categoria, movimiento
    public $direccion = 'asc';
    
    // Estadísticas
    public $totalProductos = 0;
    public $productosStockBajo = 0;
    public $productosSinStock = 0;
    public $valorInventario = 0;
    
    // Modal de ajuste de stock
    public $mostrarModalAjuste = false;
    public $productoSeleccionado = null;
    public $tipoAjuste = 'entrada'; // entrada, salida, ajuste
    public $cantidadAjuste = 0;
    public $motivoAjuste = '';
    
    // Servicios
    protected $tenantService;

    public function mount()
    {
        $this->tenantService = app(TenantService::class);
        $this->calcularEstadisticas();
    }

    public function render()
    {
        // Obtener empresa con fallback robusto
        $empresa = null;
        $empresaId = 1; // Fallback por defecto
        
        try {
            if ($this->tenantService) {
                $empresa = $this->tenantService->getEmpresa();
            }
        } catch (\Exception $e) {
            // Log error pero continúa
            Log::warning('Error en TenantService: ' . $e->getMessage());
        }
        
        // Si no hay empresa desde TenantService, intentar desde usuario
        if (!$empresa) {
            $user = Auth::user();
            if ($user) {
                $empresa = $user->empresas?->first();
            }
        }
        
        // Si aún no hay empresa, usar la primera disponible
        if (!$empresa) {
            $empresa = \App\Models\Empresa::first();
        }
        
        $empresaId = $empresa?->id ?? 1;

        $query = Producto::with(['categoria', 'stocks'])
            ->where('empresa_id', $empresaId)
            ->where('activo', true);

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_interno', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_barra', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por categoría
        if ($this->categoriaFiltro) {
            $query->where('categoria_id', $this->categoriaFiltro);
        }

        // Filtro por stock
        switch ($this->stockFiltro) {
            case 'bajo':
                $query->where(function ($q) use ($empresaId) {
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
                $query->where(function ($q) use ($empresaId) {
                    $q->whereHas('stocks', function ($stockQuery) {
                        $stockQuery->whereColumn('cantidad_actual', '>', 'productos.stock_maximo');
                    })->where('stock_maximo', '>', 0);
                });
                break;
        }

        // Ordenamiento
        switch ($this->ordenarPor) {
            case 'categoria':
                $query->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                      ->orderBy('categorias.nombre', $this->direccion)
                      ->select('productos.*');
                break;
            case 'stock':
                $query->leftJoin('producto_stocks', 'productos.id', '=', 'producto_stocks.producto_id')
                      ->orderBy('producto_stocks.cantidad_actual', $this->direccion)
                      ->select('productos.*');
                break;
            default:
                $query->orderBy($this->ordenarPor, $this->direccion);
        }

        $productos = $query->paginate(20);

        // Agregar stock calculado a cada producto
        $productos->getCollection()->transform(function ($producto) {
            $stockTotal = $producto->stocks->sum('cantidad_actual');
            $producto->stock_total = $stockTotal;
            $producto->stock_disponible = $producto->stocks->sum('cantidad_disponible');
            $producto->stock_reservado = $producto->stocks->sum('cantidad_reservada');
            return $producto;
        });

        $categorias = Categoria::where('empresa_id', $empresaId)
                              ->where('activa', true)
                              ->orderBy('nombre')
                              ->get();

        return view('livewire.inventario.dashboard', [
            'productos' => $productos,
            'categorias' => $categorias
        ]);
    }

    private function calcularEstadisticas()
    {
        // Obtener empresa con fallback robusto
        $empresa = null;
        $empresaId = 1; // Fallback por defecto
        
        try {
            if ($this->tenantService) {
                $empresa = $this->tenantService->getEmpresa();
            }
        } catch (\Exception $e) {
            Log::warning('Error en TenantService (calcularEstadisticas): ' . $e->getMessage());
        }
        
        // Si no hay empresa desde TenantService, intentar desde usuario
        if (!$empresa) {
            $user = Auth::user();
            if ($user) {
                $empresa = $user->empresas?->first();
            }
        }
        
        // Si aún no hay empresa, usar la primera disponible
        if (!$empresa) {
            $empresa = \App\Models\Empresa::first();
        }
        
        $empresaId = $empresa?->id ?? 1;

        $this->totalProductos = Producto::where('empresa_id', $empresaId)
                                       ->where('activo', true)
                                       ->count();

        // Productos con stock bajo
        $this->productosStockBajo = Producto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->where('stock_minimo', '>', 0)
            ->whereHas('stocks', function ($q) {
                $q->whereColumn('cantidad_actual', '<=', 'productos.stock_minimo');
            })
            ->count();

        // Productos sin stock
        $this->productosSinStock = Producto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->whereHas('stocks', function ($q) {
                $q->where('cantidad_actual', '<=', 0);
            })
            ->count();

        // Valor total del inventario
        $this->valorInventario = ProductoStock::join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
            ->where('productos.empresa_id', $empresaId)
            ->where('producto_stocks.empresa_id', $empresaId)
            ->sum(DB::raw('producto_stocks.cantidad_actual * productos.precio_costo'));
    }

    public function abrirModalAjuste($productoId)
    {
        $this->productoSeleccionado = Producto::with('stocks')->find($productoId);
        $this->mostrarModalAjuste = true;
        $this->cantidadAjuste = 0;
        $this->motivoAjuste = '';
        $this->tipoAjuste = 'entrada';
    }

    public function cerrarModalAjuste()
    {
        $this->mostrarModalAjuste = false;
        $this->productoSeleccionado = null;
        $this->cantidadAjuste = 0;
        $this->motivoAjuste = '';
    }

    public function ejecutarAjusteStock()
    {
        // Log para debugging
        logger('Ejecutando ajuste de stock', [
            'cantidad' => $this->cantidadAjuste,
            'motivo' => $this->motivoAjuste,
            'tipo' => $this->tipoAjuste,
            'producto_id' => $this->productoSeleccionado?->id
        ]);

        $this->validate([
            'cantidadAjuste' => 'required|numeric|min:0.01',
            'motivoAjuste' => 'required|string|max:255',
            'tipoAjuste' => 'required|in:entrada,salida,ajuste'
        ]);

        if (!$this->productoSeleccionado) {
            $this->addError('general', 'Producto no seleccionado');
            return;
        }

        try {
            DB::transaction(function () {
                // Obtener empresa con fallback robusto
                $empresa = null;
                $empresaId = 1; // Fallback por defecto
                
                try {
                    if ($this->tenantService) {
                        $empresa = $this->tenantService->getEmpresa();
                    }
                } catch (\Exception $e) {
                    Log::warning('Error en TenantService (ejecutarAjusteStock): ' . $e->getMessage());
                }
                
                // Si no hay empresa desde TenantService, intentar desde usuario
                if (!$empresa) {
                    $user = Auth::user();
                    if ($user) {
                        $empresa = $user->empresas?->first();
                    }
                }
                
                // Si aún no hay empresa, usar la primera disponible
                if (!$empresa) {
                    $empresa = \App\Models\Empresa::first();
                }
                
                $empresaId = $empresa?->id ?? 1;
                
                $stock = ProductoStock::firstOrCreate([
                    'empresa_id' => $empresaId,
                    'producto_id' => $this->productoSeleccionado->id,
                    'sucursal_id' => 1, // Por ahora sucursal fija
                ], [
                    'cantidad_actual' => 0,
                    'cantidad_reservada' => 0,
                    'cantidad_disponible' => 0,
                    'costo_promedio' => $this->productoSeleccionado->precio_costo
                ]);

                $stockAnterior = $stock->cantidad_actual;
                $cantidad = $this->cantidadAjuste;

                switch ($this->tipoAjuste) {
                    case 'entrada':
                        $stock->cantidad_actual += $cantidad;
                        $tipoMovimiento = InventarioMovimiento::TIPO_ENTRADA;
                        break;
                    case 'salida':
                        $stock->cantidad_actual -= $cantidad;
                        $tipoMovimiento = InventarioMovimiento::TIPO_SALIDA;
                        break;
                    case 'ajuste':
                        $stock->cantidad_actual = $cantidad;
                        $tipoMovimiento = InventarioMovimiento::TIPO_AJUSTE;
                        break;
                }

                $stock->cantidad_disponible = $stock->cantidad_actual - $stock->cantidad_reservada;
                $stock->ultimo_movimiento = now();
                $stock->save();

                // Registrar movimiento
                InventarioMovimiento::create([
                    'empresa_id' => $empresaId,
                    'producto_id' => $this->productoSeleccionado->id,
                    'sucursal_id' => 1,
                    'usuario_id' => Auth::id(),
                    'tipo_movimiento' => $tipoMovimiento,
                    'referencia_tipo' => InventarioMovimiento::REFERENCIA_AJUSTE,
                    'referencia_id' => null,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $this->productoSeleccionado->precio_costo,
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $stock->cantidad_actual,
                    'observaciones' => $this->motivoAjuste,
                    'fecha_movimiento' => now()
                ]);
            });

            $this->cerrarModalAjuste();
            $this->calcularEstadisticas();
            $this->dispatch('stock-actualizado');
            
            session()->flash('message', 'Stock ajustado exitosamente');
            
        } catch (\Exception $e) {
            logger('Error al ajustar stock', ['error' => $e->getMessage()]);
            $this->addError('general', 'Error al ajustar stock: ' . $e->getMessage());
        }
    }

    // Reset pagination when filters change
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoriaFiltro()
    {
        $this->resetPage();
    }

    public function updatedStockFiltro()
    {
        $this->resetPage();
    }

    public function cambiarOrden($campo)
    {
        if ($this->ordenarPor === $campo) {
            $this->direccion = $this->direccion === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenarPor = $campo;
            $this->direccion = 'asc';
        }
        $this->resetPage();
    }
}
