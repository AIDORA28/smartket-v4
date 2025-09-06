<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use App\Models\Categoria;
use App\Services\TenantService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormularioFixed extends Component
{
    use WithFileUploads;

    public $producto = null;
    public $editing = false;

    public $nombre = '';
    public $codigo_interno = '';
    public $codigo_barra = '';
    public $descripcion = '';
    public $categoria_id = '';
    public $precio_costo = 0;
    public $precio_venta = 0;
    public $stock_minimo = 0;
    public $imagen_url = null;
    public $activo = true;
    public $maneja_stock = true;

    public function mount($producto = null)
    {
        if ($producto && $producto->exists) {
            $this->editing = true;
            $this->producto = $producto;
            $this->fill($producto->toArray());
        }
    }

    public function save()
    {
        // Reglas de validación sin atributos
        $rules = [
            'nombre' => 'required|string|max:255',
            'codigo_interno' => $this->editing 
                ? 'nullable|string|max:50|unique:productos,codigo_interno,' . $this->producto->id
                : 'nullable|string|max:50|unique:productos,codigo_interno',
            'codigo_barra' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'imagen_url' => 'nullable|image|max:2048',
        ];

        $this->validate($rules);

        $tenantService = app(TenantService::class);
        $empresaId = $tenantService->getEmpresaId();
        
        // Fallback temporal: si no hay empresa, usar la primera
        if (!$empresaId) {
            $empresaId = 1; // Empresa por defecto temporal
        }

        if (!$empresaId) {
            session()->flash('error', 'No se ha seleccionado una empresa.');
            return;
        }

        $data = [
            'nombre' => $this->nombre,
            'codigo_interno' => $this->codigo_interno,
            'codigo_barra' => $this->codigo_barra,
            'descripcion' => $this->descripcion,
            'categoria_id' => $this->categoria_id,
            'precio_costo' => $this->precio_costo,
            'precio_venta' => $this->precio_venta,
            'stock_minimo' => $this->stock_minimo,
            'activo' => $this->activo,
            'maneja_stock' => $this->maneja_stock,
            'empresa_id' => $empresaId,
        ];

        // Procesar imagen si se subió una nueva
        if ($this->imagen_url) {
            $imagePath = $this->imagen_url->store('productos', 'public');
            $data['imagen_url'] = Storage::url($imagePath);
            
            // Eliminar imagen anterior si existe
            if ($this->editing && $this->producto->imagen_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $this->producto->imagen_url));
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

    public function getCategorias()
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

    public function render()
    {
        return view('livewire.productos.formulario-fixed', [
            'categorias' => $this->getCategorias()
        ]);
    }
}
