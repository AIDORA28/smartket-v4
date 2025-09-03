<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use App\Models\Categoria;
use App\Services\TenantService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Lista extends Component
{
    use WithPagination;

    public $search = '';
    public $categoria_id = '';
    public $estado = '';
    public $sort_field = 'nombre';
    public $sort_direction = 'asc';
    public $per_page = 10;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // $this->authorize('producto.read');
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

    public function sortBy($field)
    {
        if ($this->sort_field === $field) {
            $this->sort_direction = $this->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_direction = 'asc';
        }
        $this->sort_field = $field;
        $this->resetPage();
    }

    public function delete($id)
    {
        // $this->authorize('producto.delete');
        
        $tenantService = app(TenantService::class);
        $producto = Producto::where('empresa_id', $tenantService->getEmpresaId())
                           ->findOrFail($id);
        
        // Verificar si el producto tiene ventas
        if ($producto->ventaItems()->exists()) {
            session()->flash('error', 'No se puede eliminar el producto porque tiene ventas asociadas.');
            return;
        }

        $producto->delete();
        session()->flash('message', 'Producto eliminado correctamente.');
    }

    public function toggleEstado($id)
    {
        // $this->authorize('producto.update');
        
        $tenantService = app(TenantService::class);
        $producto = Producto::where('empresa_id', $tenantService->getEmpresaId())
                           ->findOrFail($id);
        
        $producto->update(['activo' => !$producto->activo]);
        
        session()->flash('message', 'Estado del producto actualizado.');
    }

    #[Computed]
    public function categorias()
    {
        $tenantService = app(TenantService::class);
        return Categoria::where('empresa_id', $tenantService->getEmpresaId())
                       ->where('activo', true)
                       ->orderBy('nombre')
                       ->get();
    }

    #[Computed]
    public function productos()
    {
        $tenantService = app(TenantService::class);
        $query = Producto::with('categoria')
                        ->where('empresa_id', $tenantService->getEmpresaId());

        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_barras', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por categoría
        if ($this->categoria_id) {
            $query->where('categoria_id', $this->categoria_id);
        }

        // Filtro por estado
        if ($this->estado !== '') {
            $query->where('activo', $this->estado);
        }

        // Ordenamiento
        $query->orderBy($this->sort_field, $this->sort_direction);

        return $query->paginate($this->per_page);
    }

    public function render()
    {
        return view('livewire.productos.lista');
    }
}
