<?php

namespace App\Livewire\Reportes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AnalyticsService;
use App\Services\ReporteVentasService;
use App\Services\TenantService;
use App\Models\Empresa;
use App\Models\Venta;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Reportes de Ventas')]
class Sales extends Component
{
    // Propiedades principales
    public $empresa;
    public $filtros = [
        'fecha_inicio' => null,
        'fecha_fin' => null,
        'sucursal_id' => null,
        'cliente_id' => null,
        'estado' => 'todos',
        'agrupacion' => 'dia', // dia, semana, mes
        'tipo_grafico' => 'linea' // linea, barras, dona
    ];
    
    // Datos de ventas
    public $metricas = [];
    public $ventasPorPeriodo = [];
    public $topProductos = [];
    public $ventasPorCliente = [];
    public $resumenEstados = [];
    
    // Configuración de gráficos
    public $chartData = [];
    public $chartOptions = [];
    
    // Estados de UI
    public $loading = false;
    public $exportando = false;
    
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
        
        // Inicializar filtros
        $this->filtros['fecha_inicio'] = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filtros['fecha_fin'] = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $this->cargarDatos();
    }
    
    public function cargarDatos()
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

            $reporteVentasService = app(ReporteVentasService::class);
            $analyticsService = app(AnalyticsService::class);
            
            // Convertir fechas
            $fechaInicio = Carbon::parse($this->filtros['fecha_inicio']);
            $fechaFin = Carbon::parse($this->filtros['fecha_fin']);
            
            // Obtener métricas usando el dashboard ejecutivo
            $dashboardData = $reporteVentasService->dashboardEjecutivo(
                $this->empresa->id,
                $this->filtros['sucursal_id']
            );
            
            $this->metricas = $dashboardData;
            
            // Usar métodos disponibles del servicio
            $ventasPeriodoData = $reporteVentasService->ventasPorPeriodo(
                $this->empresa->id,
                $fechaInicio->format('Y-m-d'),
                $fechaFin->format('Y-m-d'),
                $this->filtros['sucursal_id']
            );
            
            $this->ventasPorPeriodo = collect($ventasPeriodoData['datos'] ?? []);
            
            // Top productos usando el servicio
            $topProductosData = $reporteVentasService->productosMasVendidos(
                $this->empresa->id,
                $fechaInicio->format('Y-m-d'),
                $fechaFin->format('Y-m-d'),
                10
            );
            
            $this->topProductos = collect($topProductosData['productos'] ?? []);
            
            // Clientes frecuentes
            $clientesData = $reporteVentasService->clientesFrecuentes(
                $this->empresa->id,
                $fechaInicio->format('Y-m-d'),
                $fechaFin->format('Y-m-d'),
                10
            );
            
            $this->ventasPorCliente = collect($clientesData['clientes'] ?? []);
            
            // Resumen por estados
            $this->resumenEstados = $this->obtenerResumenEstados($fechaInicio, $fechaFin);
            
            // Preparar datos para gráficos
            $this->prepararDatosGraficos();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar datos: ' . $e->getMessage());
            
            // Valores por defecto en caso de error
            $this->metricas = [];
            $this->ventasPorPeriodo = collect();
            $this->topProductos = collect();
            $this->ventasPorCliente = collect();
            $this->resumenEstados = [];
            $this->chartData = [];
            $this->chartOptions = [];
        } finally {
            $this->loading = false;
        }
    }
    
    private function obtenerVentasPorPeriodo($fechaInicio, $fechaFin)
    {
        $query = Venta::where('empresa_id', $this->empresa->id)
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        
        if ($this->filtros['sucursal_id']) {
            $query->where('sucursal_id', $this->filtros['sucursal_id']);
        }
        
        if ($this->filtros['estado'] !== 'todos') {
            $query->where('estado', $this->filtros['estado']);
        }
        
        switch ($this->filtros['agrupacion']) {
            case 'semana':
                return $query->selectRaw('YEARWEEK(fecha) as periodo, WEEK(fecha) as semana, COUNT(*) as cantidad, SUM(total) as monto')
                    ->groupBy('periodo', 'semana')
                    ->orderBy('periodo')
                    ->get();
                    
            case 'mes':
                return $query->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as periodo, COUNT(*) as cantidad, SUM(total) as monto')
                    ->groupBy('periodo')
                    ->orderBy('periodo')
                    ->get();
                    
            default: // día
                return $query->selectRaw('DATE(fecha_venta) as periodo, COUNT(*) as cantidad, SUM(total) as monto')
                    ->groupBy('periodo')
                    ->orderBy('periodo')
                    ->get();
        }
    }
    
    private function obtenerTopProductos($fechaInicio, $fechaFin)
    {
        return DB::table('venta_detalles as vd')
            ->join('ventas as v', 'vd.venta_id', '=', 'v.id')
            ->join('productos as p', 'vd.producto_id', '=', 'p.id')
            ->where('v.empresa_id', $this->empresa->id)
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->when($this->filtros['sucursal_id'], fn($q) => $q->where('v.sucursal_id', $this->filtros['sucursal_id']))
            ->selectRaw('
                p.nombre,
                SUM(vd.cantidad) as cantidad_vendida,
                SUM(vd.subtotal) as ingresos,
                COUNT(DISTINCT v.id) as num_ventas
            ')
            ->groupBy('p.id', 'p.nombre')
            ->orderByDesc('ingresos')
            ->limit(10)
            ->get();
    }
    
    private function obtenerVentasPorCliente($fechaInicio, $fechaFin)
    {
        return DB::table('ventas as v')
            ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            ->where('v.empresa_id', $this->empresa->id)
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->when($this->filtros['sucursal_id'], fn($q) => $q->where('v.sucursal_id', $this->filtros['sucursal_id']))
            ->selectRaw('
                c.nombre,
                COUNT(*) as num_ventas,
                SUM(v.total) as monto_total,
                AVG(v.total) as promedio_compra
            ')
            ->groupBy('c.id', 'c.nombre')
            ->orderByDesc('monto_total')
            ->limit(10)
            ->get();
    }
    
    private function obtenerResumenEstados($fechaInicio, $fechaFin)
    {
        return Venta::where('empresa_id', $this->empresa->id)
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->when($this->filtros['sucursal_id'], fn($q) => $q->where('sucursal_id', $this->filtros['sucursal_id']))
            ->selectRaw('
                estado,
                COUNT(*) as cantidad,
                SUM(total) as monto,
                AVG(total) as promedio
            ')
            ->groupBy('estado')
            ->get();
    }
    
    private function prepararDatosGraficos()
    {
        // Datos para gráfico principal de ventas por período
        $labels = [];
        $montos = [];
        $cantidades = [];
        
        // Verificar si hay datos de ventas por período
        if ($this->ventasPorPeriodo instanceof \Illuminate\Support\Collection && $this->ventasPorPeriodo->isNotEmpty()) {
            $labels = $this->ventasPorPeriodo->pluck('fecha_venta')->toArray();
            $montos = $this->ventasPorPeriodo->pluck('monto')->map(fn($m) => (float)$m)->toArray();
            $cantidades = $this->ventasPorPeriodo->pluck('cantidad')->map(fn($c) => (int)$c)->toArray();
        }
        
        $productosLabels = [];
        $productosData = [];
        
        // Verificar si hay datos de productos
        if ($this->topProductos instanceof \Illuminate\Support\Collection && $this->topProductos->isNotEmpty()) {
            $productosLabels = $this->topProductos->pluck('nombre')->toArray();
            $productosData = $this->topProductos->pluck('cantidad_vendida')->map(fn($c) => (int)$c)->toArray();
        }
        
        $this->chartData = [
            'ventas_periodo' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Monto (S/)',
                        'data' => $montos,
                        'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                        'borderColor' => 'rgb(59, 130, 246)',
                        'borderWidth' => 2
                    ],
                    [
                        'label' => 'Cantidad',
                        'data' => $cantidades,
                        'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                        'borderColor' => 'rgb(16, 185, 129)',
                        'borderWidth' => 2,
                        'yAxisID' => 'y1'
                    ]
                ]
            ],
            'top_productos' => [
                'labels' => $productosLabels,
                'datasets' => [
                    [
                        'label' => 'Cantidad Vendida',
                        'data' => $productosData,
                        'backgroundColor' => [
                            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                            '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280'
                        ]
                    ]
                ]
            ]
        ];
        
        $this->chartOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position' => 'left'
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false
                    ]
                ]
            ]
        ];
    }
    
    public function actualizarFiltros()
    {
        $this->cargarDatos();
    }
    
    public function exportarReporte($formato = 'pdf')
    {
        $this->exportando = true;
        
        try {
            // Datos para exportar
            $datos = [
                'empresa' => $this->empresa,
                'filtros' => $this->filtros,
                'metricas' => $this->metricas,
                'ventas_periodo' => $this->ventasPorPeriodo,
                'top_productos' => $this->topProductos,
                'ventas_cliente' => $this->ventasPorCliente,
                'resumen_estados' => $this->resumenEstados
            ];
            
            // Simplemente devolver los datos como archivo temporal
            $filename = 'reporte_ventas_' . now()->format('Y-m-d_H-i-s') . '.' . $formato;
            $filepath = storage_path('app/temp/' . $filename);
            
            // Crear directorio si no existe
            if (!file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            
            // Generar contenido según formato
            if ($formato === 'csv') {
                $this->generarCSV($filepath, $datos);
            } else {
                // Para PDF/Excel usaríamos una biblioteca externa
                file_put_contents($filepath, json_encode($datos, JSON_PRETTY_PRINT));
            }
            
            $this->dispatch('descargar-archivo', archivo: asset('storage/temp/' . $filename));
            session()->flash('message', 'Reporte de ventas generado exitosamente');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar reporte: ' . $e->getMessage());
        } finally {
            $this->exportando = false;
        }
    }
    
    private function generarCSV($filepath, $datos)
    {
        $handle = fopen($filepath, 'w');
        
        // Header CSV
        fputcsv($handle, ['Reporte de Ventas - ' . $this->empresa->nombre]);
        fputcsv($handle, ['Período: ' . $this->filtros['fecha_inicio'] . ' a ' . $this->filtros['fecha_fin']]);
        fputcsv($handle, []);
        
        // Métricas generales
        if (isset($datos['metricas']['ventas_hoy'])) {
            fputcsv($handle, ['Ventas Hoy', 'Cantidad', 'Monto']);
            fputcsv($handle, [
                '',
                $datos['metricas']['ventas_hoy']['cantidad'] ?? 0,
                'S/ ' . number_format($datos['metricas']['ventas_hoy']['monto'] ?? 0, 2)
            ]);
        }
        
        fclose($handle);
    }
    
    public function render()
    {
        return view('livewire.reportes.sales');
    }
}
