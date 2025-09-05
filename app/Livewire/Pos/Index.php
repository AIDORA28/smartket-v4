<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Venta;
use App\Services\TenantService;
use App\Services\VentaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $empresa;
    public $sucursal;
    
    // Estado del carrito
    public $carrito = [];
    public $clienteSeleccionado = null;
    public $total = 0;
    
    // Filtros y búsqueda
    public $categoriaSeleccionada = '';
    public $busqueda = '';
    
    // Modal de pago
    public $mostrarModalPago = false;
    public $metodoPago = 'efectivo';
    public $montoRecibido = 0;
    public $cambio = 0;
    
    // Datos
    public $productos = [];
    public $categorias = [];
    public $clientes = [];
    
    protected $tenantService;
    protected $ventaService;

    public function mount()
    {
        $this->tenantService = app(TenantService::class);
        $this->ventaService = app(VentaService::class);
        
        // Inicializar contexto de tenant
        if (!$this->tenantService->getEmpresa() && Auth::user() && Auth::user()->empresa_id) {
            $this->tenantService->setEmpresa(Auth::user()->empresa_id);
        }
        
        $this->empresa = $this->tenantService->getEmpresa();
        $this->sucursal = $this->tenantService->getSucursal();
        
        if ($this->empresa) {
            $this->cargarDatos();
        }
    }

    public function cargarDatos()
    {
        // Cargar categorías
        $this->categorias = Categoria::where('empresa_id', $this->empresa->id)
            ->where('activa', true)
            ->orderBy('nombre')
            ->get();
            
        // Cargar clientes básicos
        $this->clientes = Cliente::where('empresa_id', $this->empresa->id)
            ->orderBy('nombre')
            ->limit(10)
            ->get();
            
        $this->filtrarProductos();
    }

    public function filtrarProductos()
    {
        if (!$this->empresa) return;
        
        $query = Producto::where('empresa_id', $this->empresa->id)
            ->where('activo', true)
            ->with(['categoria']);
            
        if ($this->categoriaSeleccionada) {
            $query->where('categoria_id', $this->categoriaSeleccionada);
        }
        
        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('codigo_interno', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('codigo_barra', 'like', '%' . $this->busqueda . '%');
            });
        }
        
        $this->productos = $query->orderBy('nombre')->get();
    }

    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::find($productoId);
        if (!$producto) return;
        
        if (isset($this->carrito[$productoId])) {
            $this->carrito[$productoId]['cantidad']++;
        } else {
            $this->carrito[$productoId] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio_venta,
                'cantidad' => 1,
                'subtotal' => $producto->precio_venta
            ];
        }
        
        $this->actualizarCarrito();
    }

    public function actualizarCantidad($productoId, $cantidad)
    {
        if ($cantidad <= 0) {
            unset($this->carrito[$productoId]);
        } else {
            $this->carrito[$productoId]['cantidad'] = $cantidad;
            $this->carrito[$productoId]['subtotal'] = $this->carrito[$productoId]['precio'] * $cantidad;
        }
        
        $this->actualizarCarrito();
    }

    public function removerDelCarrito($productoId)
    {
        unset($this->carrito[$productoId]);
        $this->actualizarCarrito();
    }

    public function actualizarCarrito()
    {
        $this->total = 0;
        foreach ($this->carrito as $item) {
            $this->total += $item['subtotal'];
        }
    }

    public function abrirModalPago()
    {
        if (empty($this->carrito)) {
            session()->flash('error', 'El carrito está vacío');
            return;
        }
        
        $this->montoRecibido = $this->total;
        $this->cambio = 0;
        $this->mostrarModalPago = true;
    }

    public function calcularCambio()
    {
        $this->cambio = max(0, $this->montoRecibido - $this->total);
    }

    public function procesarVenta()
    {
        if (empty($this->carrito)) {
            session()->flash('error', 'El carrito está vacío');
            return;
        }
        
        if ($this->metodoPago === 'efectivo' && $this->montoRecibido < $this->total) {
            session()->flash('error', 'El monto recibido es insuficiente');
            return;
        }

        try {
            DB::beginTransaction();
            
            // Crear la venta
            $venta = Venta::create([
                'empresa_id' => $this->empresa->id,
                'sucursal_id' => $this->sucursal->id ?? $this->empresa->sucursales->first()->id,
                'usuario_id' => Auth::id(),
                'cliente_id' => $this->clienteSeleccionado,
                'numero_venta' => 'V-' . date('Y') . '-' . str_pad(Venta::where('empresa_id', $this->empresa->id)->count() + 1, 6, '0', STR_PAD_LEFT),
                'fecha_venta' => now(),
                'subtotal' => $this->total,
                'total_descuento' => 0,
                'total_igv' => $this->total * 0.18,
                'total' => $this->total,
                'estado_venta' => 'confirmada'
            ]);
            
            // Crear los detalles de la venta
            foreach ($this->carrito as $item) {
                $venta->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['subtotal'],
                    'descuento' => 0,
                    'total' => $item['subtotal']
                ]);
            }
            
            // Crear el pago
            $venta->pagos()->create([
                'metodo_pago' => $this->metodoPago,
                'monto' => $this->total,
                'fecha_pago' => now(),
                'estado' => 'completado'
            ]);
            
            DB::commit();
            
            // Limpiar el carrito
            $this->carrito = [];
            $this->total = 0;
            $this->mostrarModalPago = false;
            $this->clienteSeleccionado = null;
            
            session()->flash('success', 'Venta procesada exitosamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    public function limpiarCarrito()
    {
        $this->carrito = [];
        $this->total = 0;
    }

    // Watchers
    public function updatedCategoriaSeleccionada()
    {
        $this->filtrarProductos();
    }

    public function updatedBusqueda()
    {
        $this->filtrarProductos();
    }

    public function updatedMontoRecibido()
    {
        $this->calcularCambio();
    }

    public function render()
    {
        return view('livewire.pos.index');
    }
}
