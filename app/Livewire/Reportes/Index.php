<?php

namespace App\Livewire\Reportes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Services\AnalyticsService;
use App\Services\ReporteService;
use App\Services\DashboardService;
use App\Services\TenantService;
use App\Models\Empresa;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Reportes y Analytics')]
class Index extends Component
{
    // Propiedades principales
    public $empresa;
    public $filtros = [
        'fecha_inicio' => null,
        'fecha_fin' => null,
        'sucursal_id' => null,
        'tipo_reporte' => 'dashboard'
    ];
    
    // Datos del dashboard
    public $kpisResumen = [];
    public $reportesFavoritos = [];
    public $alertas = [];
    public $actividad_reciente = [];
    
    // Estados de UI
    public $loading = false;
    public $vistaActual = 'dashboard';
    public $reporteSeleccionado = null;
    
    public function mount()
    {
        // Obtener empresa con fallback robusto
        $this->empresa = null;
        
        try {
            $tenantService = app(TenantService::class);
            $this->empresa = $tenantService->getEmpresa();
        } catch (\Exception $e) {
            // Intentar fallbacks en orden de prioridad
        }
        
        // Fallback 1: Primera empresa disponible directamente
        if (!$this->empresa) {
            $this->empresa = Empresa::first();
        }
        
        // Fallback final: Lanzar error si no hay empresas
        if (!$this->empresa) {
            session()->flash('error', 'No hay empresas disponibles en el sistema. Contacte al administrador.');
            return;
        }
        
        // Inicializar filtros por defecto
        $this->filtros['fecha_inicio'] = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filtros['fecha_fin'] = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        // Cargar datos iniciales
        $this->cargarDashboard();
    }
    
    public function cargarDashboard()
    {
        $this->loading = true;
        
        try {
            // Verificar que empresa existe y tiene ID válido
            if (!$this->empresa || !$this->empresa->id) {
                $this->empresa = Empresa::first();
                if (!$this->empresa) {
                    throw new \Exception('No hay empresas disponibles en el sistema');
                }
            }

            $dashboardService = app(DashboardService::class);
            $analyticsService = app(AnalyticsService::class);
            
            // Obtener métricas del dashboard
            $dashboardData = $dashboardService->obtenerDatosDashboard(
                $this->empresa->id,
                $this->filtros
            );
            
            $this->kpisResumen = $dashboardData['kpis'] ?? [];
            $this->alertas = $dashboardData['alertas'] ?? [];
            $this->actividad_reciente = $dashboardData['actividad_reciente'] ?? [];
            
            // Reportes favoritos/predefinidos
            $this->reportesFavoritos = [
                [
                    'id' => 'ventas_periodo',
                    'titulo' => 'Ventas por Período',
                    'descripcion' => 'Análisis detallado de ventas con tendencias',
                    'icono' => '📈',
                    'tipo' => 'ventas',
                    'color' => 'blue'
                ],
                [
                    'id' => 'inventario_estado',
                    'titulo' => 'Estado de Inventario',
                    'descripcion' => 'Stock actual, movimientos y alertas',
                    'icono' => '📦',
                    'tipo' => 'inventario', 
                    'color' => 'green'
                ],
                [
                    'id' => 'clientes_analisis',
                    'titulo' => 'Análisis de Clientes',
                    'descripcion' => 'Top clientes y comportamiento de compra',
                    'icono' => '👥',
                    'tipo' => 'clientes',
                    'color' => 'purple'
                ],
                [
                    'id' => 'productos_rentables',
                    'titulo' => 'Productos Más Rentables',
                    'descripcion' => 'Análisis de márgenes y rotación',
                    'icono' => '💰',
                    'tipo' => 'productos',
                    'color' => 'yellow'
                ],
                [
                    'id' => 'dashboard_ejecutivo',
                    'titulo' => 'Dashboard Ejecutivo',
                    'descripcion' => 'KPIs principales para gerencia',
                    'icono' => '📊',
                    'tipo' => 'ejecutivo',
                    'color' => 'red'
                ],
                [
                    'id' => 'analytics_eventos',
                    'titulo' => 'Analytics y Eventos',
                    'descripcion' => 'Seguimiento de actividad del sistema',
                    'icono' => '🔍',
                    'tipo' => 'analytics',
                    'color' => 'indigo'
                ]
            ];
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el dashboard: ' . $e->getMessage());
            
            // Valores por defecto en caso de error
            $this->kpisResumen = [];
            $this->alertas = [];
            $this->actividad_reciente = [];
            $this->reportesFavoritos = [];
            
            // Datos mínimos de fallback
            $this->kpisResumen = [
                'titulo' => 'Dashboard no disponible',
                'estado' => 'error'
            ];
        } finally {
            $this->loading = false;
        }
    }
    
    public function actualizarFiltros()
    {
        $this->cargarDashboard();
        $this->dispatch('filtros-actualizados');
    }
    
    public function seleccionarReporte($reporteId)
    {
        $this->reporteSeleccionado = $reporteId;
        $this->vistaActual = 'reporte';
        
        // Redirect to specific report component
        switch($reporteId) {
            case 'ventas_periodo':
                return redirect()->route('reportes.ventas');
            case 'inventario_estado':
                return redirect()->route('reportes.inventario');
            case 'clientes_analisis':
                return redirect()->route('reportes.clientes');
            case 'productos_rentables':
                return redirect()->route('reportes.productos');
            case 'dashboard_ejecutivo':
                return redirect()->route('dashboard.ejecutivo');
            case 'analytics_eventos':
                return redirect()->route('reportes.analytics');
        }
    }
    
    public function exportarDashboard($formato = 'pdf')
    {
        $this->loading = true;
        
        try {
            $reporteService = app(ReporteService::class);
            
            $archivo = $reporteService->generarReporteDesdeTemplate(
                'dashboard_ejecutivo',
                $this->empresa->id,
                array_merge($this->filtros, ['formato' => $formato])
            );
            
            $this->dispatch('descargar-archivo', archivo: $archivo);
            session()->flash('message', 'Dashboard exportado exitosamente');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }
    
    public function render()
    {
        return view('livewire.reportes.index');
    }
}
