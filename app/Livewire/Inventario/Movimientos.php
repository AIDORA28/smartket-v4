<?php

namespace App\Livewire\Inventario;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InventarioMovimiento;
use App\Models\Producto;
use App\Models\Categoria;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;

class Movimientos extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $tipoFiltro = '';
    public $categoriaFiltro = '';
    public $fechaDesde = '';
    public $fechaHasta = '';
    public $productoFiltro = '';

    // Datos
    public $estadisticas = [
        'total_movimientos' => 0,
        'entradas_mes' => 0,
        'salidas_mes' => 0,
        'ajustes_mes' => 0
    ];

    protected $tenantService;

    public function mount()
    {
        $this->tenantService = app(TenantService::class);
        $this->fechaHasta = now()->format('Y-m-d');
        $this->fechaDesde = now()->subDays(30)->format('Y-m-d');
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

        // Query principal
        $query = InventarioMovimiento::with(['producto', 'usuario'])
            ->where('empresa_id', $empresaId);

        // Filtros
        if ($this->search) {
            $query->whereHas('producto', function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_interno', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->tipoFiltro) {
            $query->where('tipo_movimiento', $this->tipoFiltro);
        }

        if ($this->categoriaFiltro) {
            $query->whereHas('producto', function ($q) {
                $q->where('categoria_id', $this->categoriaFiltro);
            });
        }

        if ($this->fechaDesde) {
            $query->whereDate('fecha_movimiento', '>=', $this->fechaDesde);
        }

        if ($this->fechaHasta) {
            $query->whereDate('fecha_movimiento', '<=', $this->fechaHasta);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')
                           ->paginate(20);

        $categorias = Categoria::where('empresa_id', $empresaId)
                              ->where('activa', true)
                              ->orderBy('nombre')
                              ->get();

        $productos = Producto::where('empresa_id', $empresaId)
                            ->where('activo', true)
                            ->orderBy('nombre')
                            ->limit(100)
                            ->get();

        return view('livewire.inventario.movimientos', [
            'movimientos' => $movimientos,
            'categorias' => $categorias,
            'productos' => $productos
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
            // Log error pero continúa
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

        $this->estadisticas['total_movimientos'] = InventarioMovimiento::where('empresa_id', $empresaId)->count();

        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        $this->estadisticas['entradas_mes'] = InventarioMovimiento::where('empresa_id', $empresaId)
            ->where('tipo_movimiento', InventarioMovimiento::TIPO_ENTRADA)
            ->whereBetween('fecha_movimiento', [$inicioMes, $finMes])
            ->count();

        $this->estadisticas['salidas_mes'] = InventarioMovimiento::where('empresa_id', $empresaId)
            ->where('tipo_movimiento', InventarioMovimiento::TIPO_SALIDA)
            ->whereBetween('fecha_movimiento', [$inicioMes, $finMes])
            ->count();

        $this->estadisticas['ajustes_mes'] = InventarioMovimiento::where('empresa_id', $empresaId)
            ->where('tipo_movimiento', InventarioMovimiento::TIPO_AJUSTE)
            ->whereBetween('fecha_movimiento', [$inicioMes, $finMes])
            ->count();
    }

    // Reset pagination when filters change
    public function updatedSearch() { $this->resetPage(); }
    public function updatedTipoFiltro() { $this->resetPage(); }
    public function updatedCategoriaFiltro() { $this->resetPage(); }
    public function updatedFechaDesde() { $this->resetPage(); }
    public function updatedFechaHasta() { $this->resetPage(); }
}
