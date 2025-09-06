<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use App\Models\Categoria;
use App\Services\TenantService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ListaOptimizada extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';
    
    #[Url]
    public $categoria_id = '';
    
    #[Url]
    public $estado = '';

    protected $paginationTheme = 'tailwind';

    // Cache para evitar múltiples consultas
    public $categorias_cache = null;
    public $empresa_id_cache = null;

    public function mount()
    {
        // Cachear empresa_id al inicio
        $this->empresa_id_cache = $this->getEmpresaId();
    }

    private function getEmpresaId()
    {
        if ($this->empresa_id_cache) {
            return $this->empresa_id_cache;
        }

        $tenantService = app(TenantService::class);
        $empresaId = $tenantService->getEmpresaId();
        
        return $empresaId ?: 1; // Fallback
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoriaId()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'categoria_id', 'estado']);
        $this->resetPage();
    }

    public function getCategorias()
    {
        // Cache de categorías para evitar consultas repetidas
        if ($this->categorias_cache === null) {
            $this->categorias_cache = Categoria::where('empresa_id', $this->getEmpresaId())
                                              ->where('activa', true)
                                              ->orderBy('nombre')
                                              ->pluck('nombre', 'id')
                                              ->toArray();
        }
        
        return $this->categorias_cache;
    }

    public function getProductos()
    {
        $query = Producto::select([
                            'id', 'nombre', 'codigo_interno', 'codigo_barra', 'descripcion',
                            'categoria_id', 'precio_costo', 'precio_venta', 'stock_minimo',
                            'activo', 'imagen_url', 'created_at'
                        ])
                        ->with(['categoria:id,nombre']) // Solo cargar campos necesarios
                        ->where('empresa_id', $this->getEmpresaId());

        // Filtros optimizados
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', $search)
                  ->orWhere('codigo_interno', 'like', $search)
                  ->orWhere('codigo_barra', 'like', $search);
            });
        }

        if ($this->categoria_id) {
            $query->where('categoria_id', $this->categoria_id);
        }

        if ($this->estado !== '') {
            $query->where('activo', (bool) $this->estado);
        }

        return $query->orderBy('nombre')
                    ->paginate(12); // Reducir items por página para mejor rendimiento
    }

    public function toggleEstado($id)
    {
        $producto = Producto::where('empresa_id', $this->getEmpresaId())
                           ->findOrFail($id);
        
        $producto->update(['activo' => !$producto->activo]);
        
        session()->flash('message', 'Estado del producto actualizado.');
    }

    public function render()
    {
        return view('livewire.productos.lista-optimizada', [
            'productos' => $this->getProductos(),
            'categorias' => $this->getCategorias(),
        ]);
    }
}
