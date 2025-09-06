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

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->categoria_id = '';
        $this->estado = '';
        $this->resetPage();
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
        $empresaId = $tenantService->getEmpresaId();
        
        // Fallback temporal: si no hay empresa, usar la primera
        if (!$empresaId) {
            $empresaId = 1; // Empresa por defecto temporal
        }
        
        return Categoria::where('empresa_id', $empresaId)
                       ->where('activa', true)
                       ->orderBy('nombre')
                       ->get();
    }

    #[Computed]
    public function productos()
    {
        $tenantService = app(TenantService::class);
        $empresaId = $tenantService->getEmpresaId();
        
        // Fallback temporal: si no hay empresa, usar la primera
        if (!$empresaId) {
            $empresaId = 1; // Empresa por defecto temporal
        }
        
        $query = Producto::with(['categoria', 'stocks'])
                        ->selectRaw('productos.*, COALESCE(SUM(producto_stocks.cantidad_actual), 0) as stock_total')
                        ->leftJoin('producto_stocks', 'productos.id', '=', 'producto_stocks.producto_id')
                        ->where('productos.empresa_id', $empresaId)
                        ->groupBy('productos.id', 'productos.empresa_id', 'productos.categoria_id', 
                                'productos.nombre', 'productos.codigo_interno', 'productos.codigo_barra', 
                                'productos.descripcion', 'productos.precio_costo', 'productos.precio_venta', 
                                'productos.margen_ganancia', 'productos.incluye_igv', 'productos.unidad_medida', 
                                'productos.permite_decimales', 'productos.maneja_stock', 'productos.stock_minimo', 
                                'productos.stock_maximo', 'productos.activo', 'productos.imagen_url', 
                                'productos.extras_json', 'productos.created_at', 'productos.updated_at');

        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_interno', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo_barra', 'like', '%' . $this->search . '%');
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
