<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\TenantService;
use App\Services\DashboardService;
use App\Services\AnalyticsService;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\ProductoStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $empresa;
    public $sucursal;
    
    // KPIs principales
    public $kpis = [];
    
    // Datos para gráficos
    public $ventasSemana = [];
    public $productosTop = [];
    public $ventasPorHora = [];
    public $ventasMes = [];
    
    // Listas
    public $ventasRecientes = [];
    public $productosStockBajo = [];
    public $lotesVencimiento = [];
    public $clientesTop = [];
    
    // Alertas
    public $alertas = [];
    
    // Filtros
    public $fechaInicio;
    public $fechaFin;
    public $dateRange = ['start' => null, 'end' => null];
    public $loading = false;

    protected $tenantService;
    protected $dashboardService;

    /**
     * Obtener color para KPI según su tipo
     */
    public function getKpiColor($key)
    {
        $colors = [
            'ventas_hoy' => 'green',
            'ventas_mes' => 'blue',
            'productos_vendidos' => 'purple',
            'clientes_nuevos' => 'indigo',
            'stock_bajo' => 'red',
            'lotes_vencer' => 'yellow',
            'caja_actual' => 'green',
            'promedio_venta' => 'blue'
        ];
        
        return $colors[$key] ?? 'blue';
    }
    
    /**
     * Obtener URL para KPI según su tipo
     */
    public function getKpiUrl($key)
    {
        $urls = [
            'ventas_hoy' => route('ventas.index'),
            'ventas_mes' => route('ventas.index'),
            'productos_vendidos' => route('productos.index'),
            'clientes_nuevos' => route('clientes.index'),
            'stock_bajo' => route('productos.index') . '?filter=stock_bajo',
            'lotes_vencer' => route('lotes.index'),
            'caja_actual' => route('caja.index'),
            'promedio_venta' => route('reportes.index')
        ];
        
        return $urls[$key] ?? null;
    }

    public function mount()
    {
        $this->tenantService = app(TenantService::class);
        $this->dashboardService = app(DashboardService::class);
        
        // Inicializar el contexto si no está ya inicializado
        if (!$this->tenantService->getEmpresa() && Auth::user() && Auth::user()->empresa_id) {
            $this->tenantService->setEmpresa(Auth::user()->empresa_id);
        }
        
        $this->empresa = $this->tenantService->getEmpresa();
        $this->sucursal = $this->tenantService->getSucursal();
        
        // Inicializar fechas
        $this->fechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = Carbon::now()->format('Y-m-d');
        
        if ($this->empresa) {
            $this->cargarDatos();
        }
    }

    public function cargarDatos()
    {
        $this->loading = true;
        
        try {
            $this->cargarKPIs();
            $this->cargarGraficos();
            $this->cargarListas();
            $this->cargarAlertas();
        } catch (\Exception $e) {
            $this->alertas = [
                ['tipo' => 'error', 'mensaje' => 'Error cargando datos: ' . $e->getMessage()]
            ];
        }
        
        $this->loading = false;
    }

    public function cargarKPIs()
    {
        $empresaId = $this->empresa->id;
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        
        // Ventas de hoy
        $ventasHoy = Venta::where('empresa_id', $empresaId)
            ->whereDate('fecha_venta', $hoy)
            ->get();
            
        // Ventas del mes
        $ventasMes = Venta::where('empresa_id', $empresaId)
            ->whereBetween('fecha_venta', [$inicioMes, $finMes])
            ->get();
            
        // Productos vendidos hoy
        $productosVendidosHoy = VentaDetalle::join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->where('ventas.empresa_id', $empresaId)
            ->whereDate('ventas.fecha_venta', $hoy)
            ->sum('venta_detalles.cantidad');
            
        // Stock bajo
        $stockBajo = Producto::where('empresa_id', $empresaId)
            ->whereHas('stocks', function($query) {
                $query->whereRaw('cantidad_actual <= (SELECT stock_minimo FROM productos WHERE productos.id = producto_stocks.producto_id)');
            })
            ->count();
            
        // Lotes próximos a vencer
        $lotesVencen = Lote::where('empresa_id', $empresaId)
            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(7))
            ->where('estado_lote', 'activo')
            ->count();

        $this->kpis = [
            'ventas_hoy' => [
                'cantidad' => $ventasHoy->count(),
                'monto' => $ventasHoy->sum('total'),
                'icono' => '💰',
                'color' => 'green'
            ],
            'ventas_mes' => [
                'cantidad' => $ventasMes->count(),
                'monto' => $ventasMes->sum('total'),
                'icono' => '📊',
                'color' => 'blue'
            ],
            'productos_vendidos' => [
                'cantidad' => (int)$productosVendidosHoy,
                'icono' => '📦',
                'color' => 'purple'
            ],
            'productos_total' => [
                'cantidad' => Producto::where('empresa_id', $empresaId)->count(),
                'icono' => '🏪',
                'color' => 'indigo'
            ],
            'clientes_total' => [
                'cantidad' => Cliente::where('empresa_id', $empresaId)->count(),
                'icono' => '👥',
                'color' => 'pink'
            ],
            'stock_bajo' => [
                'cantidad' => $stockBajo,
                'icono' => '⚠️',
                'color' => $stockBajo > 0 ? 'red' : 'gray'
            ],
            'lotes_vencen' => [
                'cantidad' => $lotesVencen,
                'icono' => '📅',
                'color' => $lotesVencen > 0 ? 'orange' : 'gray'
            ],
            'ticket_promedio' => [
                'monto' => $ventasHoy->count() > 0 ? $ventasHoy->sum('total') / $ventasHoy->count() : 0,
                'icono' => '🎫',
                'color' => 'cyan'
            ]
        ];
    }

    public function cargarGraficos()
    {
        $empresaId = $this->empresa->id;
        
        // Ventas de los últimos 7 días
        $this->ventasSemana = $this->obtenerVentasUltimos7Dias($empresaId);
        
        // Productos más vendidos
        $this->productosTop = $this->obtenerProductosTop($empresaId);
        
        // Ventas por hora del día
        $this->ventasPorHora = $this->obtenerVentasPorHora($empresaId);
        
        // Ventas del mes actual
        $this->ventasMes = $this->obtenerVentasDelMes($empresaId);
    }

    public function cargarListas()
    {
        $empresaId = $this->empresa->id;
        
        // Ventas recientes
        $this->ventasRecientes = Venta::where('empresa_id', $empresaId)
            ->with(['cliente'])
            ->orderBy('fecha_venta', 'desc')
            ->limit(5)
            ->get()
            ->map(function($venta) {
                return [
                    'id' => $venta->id,
                    'numero' => $venta->numero_venta,
                    'cliente' => $venta->cliente->nombre ?? 'Cliente Anónimo',
                    'total' => $venta->total,
                    'fecha' => $venta->fecha_venta->format('d/m H:i'),
                    'fecha_completa' => $venta->fecha_venta->diffForHumans()
                ];
            });
            
        // Productos con stock bajo
        $this->productosStockBajo = Producto::where('empresa_id', $empresaId)
            ->with(['stocks' => function($query) {
                $query->selectRaw('producto_id, SUM(cantidad_actual) as total_stock')
                      ->groupBy('producto_id');
            }])
            ->get()
            ->filter(function($producto) {
                $totalStock = $producto->stocks->sum('cantidad_actual');
                return $totalStock <= $producto->stock_minimo;
            })
            ->sortBy(function($producto) {
                return $producto->stocks->sum('cantidad_actual');
            })
            ->take(5)
            ->map(function($producto) {
                $stockActual = $producto->stocks->sum('cantidad_actual');
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'stock_actual' => $stockActual,
                    'stock_minimo' => $producto->stock_minimo,
                    'diferencia' => $producto->stock_minimo - $stockActual
                ];
            });
            
        // Lotes próximos a vencer
        $this->lotesVencimiento = Lote::where('empresa_id', $empresaId)
            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->where('estado_lote', 'activo')
            ->with('producto')
            ->orderBy('fecha_vencimiento')
            ->limit(5)
            ->get()
            ->filter(function($lote) {
                return $lote->getStockActual() > 0;
            })
            ->map(function($lote) {
                return [
                    'id' => $lote->id,
                    'producto' => $lote->producto->nombre,
                    'codigo' => $lote->codigo_lote,
                    'cantidad' => $lote->getStockActual(),
                    'vencimiento' => $lote->fecha_vencimiento->format('d/m/Y'),
                    'dias_restantes' => $lote->fecha_vencimiento->diffInDays(Carbon::now()),
                    'urgente' => $lote->fecha_vencimiento->diffInDays(Carbon::now()) <= 7
                ];
            })
            ->values();
            
        // Clientes top
        $this->clientesTop = Cliente::where('empresa_id', $empresaId)
            ->withCount(['ventas' => function($query) {
                $query->whereBetween('fecha_venta', [Carbon::now()->startOfMonth(), Carbon::now()]);
            }])
            ->withSum(['ventas' => function($query) {
                $query->whereBetween('fecha_venta', [Carbon::now()->startOfMonth(), Carbon::now()]);
            }], 'total')
            ->having('ventas_count', '>', 0)
            ->orderBy('ventas_sum_total', 'desc')
            ->limit(5)
            ->get()
            ->map(function($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'ventas_count' => $cliente->ventas_count,
                    'ventas_total' => $cliente->ventas_sum_total ?? 0
                ];
            });
    }

    public function cargarAlertas()
    {
        $alertas = [];
        
        // Stock bajo
        if (!empty($this->productosStockBajo)) {
            $alertas[] = [
                'tipo' => 'warning',
                'mensaje' => count($this->productosStockBajo) . ' productos con stock bajo',
                'accion' => 'Ver productos',
                'url' => route('productos.index')
            ];
        }
        
        // Lotes venciendo
        $lotesUrgentes = collect($this->lotesVencimiento)->where('urgente', true);
        if ($lotesUrgentes->count() > 0) {
            $alertas[] = [
                'tipo' => 'error',
                'mensaje' => $lotesUrgentes->count() . ' lotes vencen en menos de 7 días',
                'accion' => 'Ver lotes',
                'url' => route('lotes.index')
            ];
        }
        
        // Sin ventas hoy
        if ($this->kpis['ventas_hoy']['cantidad'] == 0) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => 'No hay ventas registradas hoy',
                'accion' => 'Ir al POS',
                'url' => route('pos.index')
            ];
        }
        
        $this->alertas = $alertas;
    }

    // Métodos auxiliares para gráficos
    private function obtenerVentasUltimos7Dias($empresaId)
    {
        $datos = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $ventas = Venta::where('empresa_id', $empresaId)
                ->whereDate('fecha_venta', $fecha)
                ->get();
                
            $datos[] = [
                'fecha' => $fecha->format('d/m'),
                'dia' => $fecha->format('D'),
                'cantidad' => $ventas->count(),
                'monto' => $ventas->sum('total')
            ];
        }
        return $datos;
    }

    private function obtenerProductosTop($empresaId)
    {
        return DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
            ->where('ventas.empresa_id', $empresaId)
            ->whereBetween('ventas.fecha_venta', [Carbon::now()->startOfMonth(), Carbon::now()])
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(venta_detalles.cantidad) as cantidad_vendida'),
                DB::raw('SUM(venta_detalles.total) as total_vendido')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('cantidad_vendida', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function obtenerVentasPorHora($empresaId)
    {
        $datos = [];
        for ($hora = 8; $hora <= 22; $hora++) {
            $ventas = Venta::where('empresa_id', $empresaId)
                ->whereDate('fecha_venta', Carbon::today())
                ->whereRaw('HOUR(fecha_venta) = ?', [$hora])
                ->count();
                
            $datos[] = [
                'hora' => $hora . ':00',
                'ventas' => $ventas
            ];
        }
        return $datos;
    }

    private function obtenerVentasDelMes($empresaId)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        
        return Venta::where('empresa_id', $empresaId)
            ->whereBetween('fecha_venta', [$inicioMes, $finMes])
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as monto')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(function($item) {
                return [
                    'fecha' => Carbon::parse($item->fecha)->format('d/m'),
                    'cantidad' => $item->cantidad,
                    'monto' => $item->monto
                ];
            })
            ->toArray();
    }

    public function refrescarDatos()
    {
        $this->cargarDatos();
        $this->dispatch('refreshCharts');
        session()->flash('status', 'Datos actualizados correctamente');
    }

    public function filtrarPorFechas()
    {
        $this->cargarDatos();
        $this->dispatch('refreshCharts');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
