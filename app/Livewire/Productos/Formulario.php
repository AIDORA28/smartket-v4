<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use App\Models\Categoria;
use App\Services\TenantService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

class Formulario extends Component
{
    use WithFileUploads;

    public ?Producto $producto = null;
    public $editing = false;

    #[Validate('required|string|max:255')]
    public $nombre = '';

    #[Validate('required|string|max:50|unique:productos,codigo')]
    public $codigo = '';

    #[Validate('nullable|string|max:255')]
    public $codigo_barras = '';

    #[Validate('nullable|string')]
    public $descripcion = '';

    #[Validate('required|exists:categorias,id')]
    public $categoria_id = '';

    #[Validate('required|numeric|min:0')]
    public $precio_compra = 0;

    #[Validate('required|numeric|min:0')]
    public $precio_venta = 0;

    #[Validate('required|integer|min:0')]
    public $stock_minimo = 0;

    #[Validate('required|integer|min:0')]
    public $stock_actual = 0;

    #[Validate('nullable|image|max:2048')]
    public $imagen = null;

    public $activo = true;
    public $controla_stock = true;

    public function mount(?Producto $producto = null)
    {
        if ($producto && $producto->exists) {
            $this->editing = true;
            $this->producto = $producto;
            $this->fill($producto->toArray());
        }
    }

    public function updatedCategoriaId()
    {
        $this->validateOnly('categoria_id');
    }

    public function updatedCodigo()
    {
        if ($this->editing) {
            $this->resetValidation('codigo');
        } else {
            $this->validateOnly('codigo');
        }
    }

    public function save()
    {
        // Reglas de validación dinámicas
        $rules = [
            'nombre' => 'required|string|max:255',
            'codigo' => $this->editing 
                ? 'required|string|max:50|unique:productos,codigo,' . $this->producto->id
                : 'required|string|max:50|unique:productos,codigo',
            'codigo_barras' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_actual' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
        ];

        $this->validate($rules);

        $tenantService = app(TenantService::class);
        $empresaId = $tenantService->getEmpresaId();

        if (!$empresaId) {
            session()->flash('error', 'No se ha seleccionado una empresa.');
            return;
        }

        $data = [
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'codigo_barras' => $this->codigo_barras,
            'descripcion' => $this->descripcion,
            'categoria_id' => $this->categoria_id,
            'precio_compra' => $this->precio_compra,
            'precio_venta' => $this->precio_venta,
            'stock_minimo' => $this->stock_minimo,
            'stock_actual' => $this->stock_actual,
            'activo' => $this->activo,
            'controla_stock' => $this->controla_stock,
            'empresa_id' => $empresaId,
        ];

        // Procesar imagen si se subió una nueva
        if ($this->imagen) {
            $imagePath = $this->imagen->store('productos', 'public');
            $data['imagen'] = $imagePath;
            
            // Eliminar imagen anterior si existe
            if ($this->editing && $this->producto->imagen) {
                Storage::disk('public')->delete($this->producto->imagen);
            }
        }

        if ($this->editing) {
            $this->producto->update($data);
            session()->flash('message', 'Producto actualizado correctamente.');
        } else {
            Producto::create($data);
            session()->flash('message', 'Producto creado correctamente.');
        }

        return redirect()->route('productos.index');
    }

    public function cancel()
    {
        return redirect()->route('productos.index');
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

    public function render()
    {
        return view('livewire.productos.formulario');
    }
}
