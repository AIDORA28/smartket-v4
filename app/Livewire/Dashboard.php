<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\TenantService;
use App\Services\DashboardService;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Lote;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $empresa;
    public $kpis = [];
    public $ventasHoy = [];
    public $productosPopulares = [];
    public $lotesVencimiento = [];
    public $alertas = [];

    protected $tenantService;
    protected $dashboardService;

    public function mount()
    {
        $this->tenantService = app(TenantService::class);
        $this->dashboardService = app(DashboardService::class);
        
        // Inicializar el contexto si no está ya inicializado
        if (!$this->tenantService->getEmpresa() && Auth::user() && Auth::user()->empresa_id) {
            $this->tenantService->setEmpresa(Auth::user()->empresa_id);
        }
        
        $this->empresa = $this->tenantService->getEmpresa();
        
        if ($this->empresa) {
            $this->cargarDatos();
        }
    }

    public function cargarDatos()
    {
        try {
            // KPIs básicos
            $this->kpis = [
                'productos_total' => Producto::count(),
                'ventas_hoy' => Venta::whereDate('created_at', today())->count(),
                'ingresos_hoy' => Venta::whereDate('created_at', today())->sum('total'),
                'lotes_vencen_semana' => Lote::where('fecha_vencimiento', '<=', now()->addWeek())->count()
            ];

            // Ventas de hoy
            $this->ventasHoy = Venta::with(['cliente', 'items'])
                ->whereDate('created_at', today())
                ->latest()
                ->limit(5)
                ->get();

            // Productos más vendidos - usar datos básicos por ahora
            $this->productosPopulares = collect(); // Placeholder vacío por ahora

            // Lotes próximos a vencer
            $this->lotesVencimiento = Lote::with('producto')
                ->where('fecha_vencimiento', '<=', now()->addWeek())
                ->where('estado', 'activo')
                ->orderBy('fecha_vencimiento')
                ->limit(5)
                ->get();

            // Alertas del sistema
            $this->alertas = $this->generarAlertas();

        } catch (\Exception $e) {
            $this->kpis = [
                'productos_total' => 0,
                'ventas_hoy' => 0,
                'ingresos_hoy' => 0,
                'lotes_vencen_semana' => 0
            ];
            $this->alertas = [
                ['tipo' => 'error', 'mensaje' => 'Error cargando datos del dashboard']
            ];
        }
    }

    private function generarAlertas()
    {
        $alertas = [];

        // Stock bajo
        $productosStockBajo = Producto::whereRaw('stock_actual <= stock_minimo')->count();
        if ($productosStockBajo > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'mensaje' => "{$productosStockBajo} productos con stock bajo"
            ];
        }

        // Lotes venciendo
        $lotesVencen = $this->kpis['lotes_vencen_semana'];
        if ($lotesVencen > 0) {
            $alertas[] = [
                'tipo' => 'warning', 
                'mensaje' => "{$lotesVencen} lotes vencen esta semana"
            ];
        }

        // Facturas pendientes (simulado)
        if (rand(0, 1)) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => "Tienes " . rand(1, 5) . " facturas pendientes de envío"
            ];
        }

        return $alertas;
    }

    public function refrescarDatos()
    {
        $this->cargarDatos();
        session()->flash('status', 'Datos actualizados correctamente');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
