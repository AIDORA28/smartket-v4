<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use App\Models\Categoria;
use Livewire\Component;

class FormularioSimple extends Component
{
    public $nombre = '';
    public $descripcion = '';
    public $categoria_id = '';
    public $precio_costo = 0;
    public $precio_venta = 0;

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
        ]);

        Producto::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria_id' => $this->categoria_id,
            'precio_costo' => $this->precio_costo,
            'precio_venta' => $this->precio_venta,
            'empresa_id' => 1, // Empresa por defecto
            'activo' => true,
            'maneja_stock' => true,
            'stock_minimo' => 0,
        ]);

        session()->flash('message', 'Producto creado correctamente.');
        return redirect()->route('productos.index');
    }

    public function cancel()
    {
        return redirect()->route('productos.index');
    }

    public function render()
    {
        $categorias = Categoria::where('empresa_id', 1)->where('activa', true)->get();
        return view('livewire.productos.formulario-simple', compact('categorias'));
    }
}
