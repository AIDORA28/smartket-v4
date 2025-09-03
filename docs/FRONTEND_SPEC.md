# 🎨 SmartKet ERP - Especificación Frontend

**Versión:** 1.0  
**Fecha:** 30 Agosto 2025  
**Estado:** 📋 ESPECIFICACIÓN UI/UX COMPLETA  

---

## 🎯 **ARQUITECTURA FRONTEND**

### **🏗️ Stack Frontend**
```yaml
Framework: Laravel Blade Templates
Interactividad: Livewire 3.6+
JavaScript: Alpine.js (mínimo necesario)
CSS Framework: TailwindCSS 3.4+
Icons: Heroicons/Phosphor Icons
Build Tool: Vite (Laravel integrado)
Components: Blade Components reutilizables
```

### **📁 Estructura Frontend**
```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php           # Layout principal
│   │   ├── guest.blade.php         # Layout invitados
│   │   └── auth.blade.php          # Layout autenticación
│   ├── components/
│   │   ├── ui/                     # Componentes UI básicos
│   │   ├── forms/                  # Componentes de formularios
│   │   ├── tables/                 # Componentes de tablas
│   │   └── navigation/             # Componentes navegación
│   ├── livewire/
│   │   ├── dashboard/              # Dashboard components
│   │   ├── productos/              # Productos components
│   │   ├── ventas/                 # Ventas components
│   │   ├── inventario/             # Inventario components
│   │   ├── reportes/               # Reportes components
│   │   └── common/                 # Componentes comunes
│   ├── pages/
│   │   ├── welcome.blade.php       # Landing page
│   │   ├── about.blade.php         # Acerca de
│   │   └── pricing.blade.php       # Planes y precios
│   └── auth/
│       ├── login.blade.php         # Login
│       ├── register.blade.php      # Registro
│       └── forgot-password.blade.php
├── css/
│   └── app.css                     # TailwindCSS principal
└── js/
    ├── app.js                      # Alpine.js setup
    └── components/                 # JS components específicos
```

---

## 🏛️ **LAYOUTS PRINCIPALES**

### **🎨 app.blade.php - Layout Principal**
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'SmartKet ERP' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">
    <div class="min-h-full">
        <!-- Sidebar -->
        <x-navigation.sidebar />
        
        <!-- Main content wrapper -->
        <div class="lg:pl-72">
            <!-- Top navigation -->
            <x-navigation.header />
            
            <!-- Page content -->
            <main class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Context selector -->
                    @if(auth()->check())
                        <x-context-selector />
                    @endif
                    
                    <!-- Flash messages -->
                    <x-flash-messages />
                    
                    <!-- Page content -->
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
```

### **👤 guest.blade.php - Layout Invitados**
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'SmartKet ERP' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <x-application-logo class="mx-auto h-12 w-auto" />
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                {{ $heading ?? 'SmartKet ERP' }}
            </h2>
        </div>
        
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                {{ $slot }}
            </div>
        </div>
    </div>
    
    @livewireScripts
</body>
</html>
```

---

## 🧩 **COMPONENTES UI REUTILIZABLES**

### **🔘 Button Component**
```blade
{{-- resources/views/components/ui/button.blade.php --}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'loading' => false,
    'disabled' => false
])

@php
$variants = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white',
    'secondary' => 'bg-white hover:bg-gray-50 focus:ring-blue-500 text-gray-900 border border-gray-300',
    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
    'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white',
];

$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs',
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-4 py-2 text-base',
    'xl' => 'px-6 py-3 text-base',
];

$classes = $variants[$variant] . ' ' . $sizes[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-md border border-transparent font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors $classes"]) }}>
        @if($loading)
            <x-ui.loading-spinner class="mr-2 h-4 w-4" />
        @endif
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}"
        @if($disabled || $loading) disabled @endif
        {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-md border border-transparent font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed $classes"]) }}
    >
        @if($loading)
            <x-ui.loading-spinner class="mr-2 h-4 w-4" />
        @endif
        {{ $slot }}
    </button>
@endif
```

### **📝 Input Component**
```blade
{{-- resources/views/components/ui/input.blade.php --}}
@props([
    'label' => null,
    'error' => null,
    'help' => null,
    'required' => false,
    'type' => 'text'
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1">
        <input 
            type="{{ $type }}"
            {{ $attributes->except(['class', 'label', 'error', 'help', 'required'])->merge([
                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm' . ($error ? ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')
            ]) }}
        />
    </div>
    
    @if($help)
        <p class="mt-2 text-sm text-gray-500">{{ $help }}</p>
    @endif
    
    @if($error)
        <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
```

### **📊 Status Badge Component**
```blade
{{-- resources/views/components/ui/status-badge.blade.php --}}
@props(['status', 'type' => 'default'])

@php
$configs = [
    'venta' => [
        'borrador' => ['bg-gray-100 text-gray-800', '📝', 'Borrador'],
        'pendiente' => ['bg-yellow-100 text-yellow-800', '⏳', 'Pendiente'],
        'emitida' => ['bg-green-100 text-green-800', '✅', 'Emitida'],
        'anulada' => ['bg-red-100 text-red-800', '❌', 'Anulada'],
    ],
    'stock' => [
        'bajo' => ['bg-red-100 text-red-800', '🔻', 'Stock Bajo'],
        'normal' => ['bg-green-100 text-green-800', '✅', 'Normal'],
        'sin_stock' => ['bg-gray-100 text-gray-800', '❌', 'Sin Stock'],
    ],
    'default' => [
        'activo' => ['bg-green-100 text-green-800', '✅', 'Activo'],
        'inactivo' => ['bg-gray-100 text-gray-800', '❌', 'Inactivo'],
    ]
];

$config = $configs[$type][$status] ?? ['bg-gray-100 text-gray-800', '❓', ucfirst($status)];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }}">
    <span class="mr-1">{{ $config[1] }}</span>
    {{ $config[2] }}
</span>
```

### **📋 Data Table Component**
```blade
{{-- resources/views/components/ui/data-table.blade.php --}}
@props(['headers', 'rows', 'actions' => null])

<div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($headers as $header)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $header }}
                                </th>
                            @endforeach
                            @if($actions)
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rows as $row)
                            <tr class="hover:bg-gray-50">
                                @foreach($row as $cell)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {!! $cell !!}
                                    </td>
                                @endforeach
                                @if($actions)
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {!! $actions($row) !!}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No hay datos disponibles
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
```

---

## 🔍 **COMPONENTES LIVEWIRE**

### **📊 Dashboard Component**
```php
<?php
// app/Http/Livewire/Dashboard.php
namespace App\Http\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $recentSales = [];
    public $lowStockProducts = [];
    
    public function mount()
    {
        $this->loadStats();
        $this->loadRecentSales();
        $this->loadLowStockProducts();
    }
    
    public function render()
    {
        return view('livewire.dashboard.dashboard')
            ->layout('layouts.app', ['title' => 'Dashboard']);
    }
    
    private function loadStats()
    {
        $empresaId = app(TenantService::class)->getEmpresaId();
        
        $this->stats = [
            'ventas_hoy' => Venta::whereDate('fecha', today())->sum('total_neto'),
            'ventas_mes' => Venta::whereMonth('fecha', now()->month)->sum('total_neto'),
            'productos_total' => Producto::count(),
            'productos_bajo_stock' => Producto::whereHas('stocks', function($q) {
                $q->whereColumn('stock_actual', '<=', 'min_stock');
            })->count(),
        ];
    }
    
    private function loadRecentSales()
    {
        $this->recentSales = Venta::with(['cliente', 'usuario'])
            ->latest('fecha')
            ->take(10)
            ->get();
    }
    
    private function loadLowStockProducts()
    {
        $this->lowStockProducts = Producto::with(['stocks' => function($q) {
            $q->whereColumn('stock_actual', '<=', 'min_stock');
        }])->take(5)->get();
    }
}
```

### **📦 ProductoTable Component**
```php
<?php
// app/Http/Livewire/Productos/ProductoTable.php
namespace App\Http\Livewire\Productos;

use Livewire\Component;
use Livewire\WithPagination;

class ProductoTable extends Component
{
    use WithPagination;
    
    public $search = '';
    public $categoria = '';
    public $estado = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $perPage = 15;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'categoria' => ['except' => ''],
        'estado' => ['except' => '']
    ];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }
    
    public function render()
    {
        $productos = Producto::query()
            ->with(['categorias', 'stocks'])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%')
                          ->orWhere('codigo', 'like', '%' . $this->search . '%')
                          ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoria, function($q) {
                $q->whereHas('categorias', function($query) {
                    $query->where('categoria_id', $this->categoria);
                });
            })
            ->when($this->estado !== '', function($q) {
                $q->where('activo', $this->estado);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        $categorias = Categoria::orderBy('nombre')->get();
        
        return view('livewire.productos.producto-table', [
            'productos' => $productos,
            'categorias' => $categorias
        ])->layout('layouts.app', ['title' => 'Productos']);
    }
}
```

### **🛒 POS Component**
```php
<?php
// app/Http/Livewire/Ventas/POS.php
namespace App\Http\Livewire\Ventas;

use Livewire\Component;

class POS extends Component
{
    public $cart = [];
    public $cliente_id = null;
    public $tipo_doc = 'interno';
    public $search_producto = '';
    public $productos_suggestions = [];
    public $total = 0;
    public $descuento_global = 0;
    public $metodos_pago = [
        ['metodo' => 'efectivo', 'monto' => 0]
    ];
    
    protected $rules = [
        'cart.*.cantidad' => 'required|numeric|min:0.001',
        'cart.*.precio_unit' => 'required|numeric|min:0',
    ];
    
    public function mount()
    {
        $this->resetCart();
    }
    
    public function updatedSearchProducto($value)
    {
        if (strlen($value) >= 2) {
            $this->productos_suggestions = Producto::where('activo', true)
                ->where(function($q) use ($value) {
                    $q->where('nombre', 'like', '%' . $value . '%')
                      ->orWhere('codigo', 'like', '%' . $value . '%')
                      ->orWhere('sku', 'like', '%' . $value . '%');
                })
                ->limit(10)
                ->get();
        } else {
            $this->productos_suggestions = [];
        }
    }
    
    public function addToCart($producto_id)
    {
        $producto = Producto::findOrFail($producto_id);
        
        $existingIndex = collect($this->cart)->search(function($item) use ($producto_id) {
            return $item['producto_id'] == $producto_id;
        });
        
        if ($existingIndex !== false) {
            $this->cart[$existingIndex]['cantidad']++;
        } else {
            $this->cart[] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'cantidad' => 1,
                'precio_unit' => $producto->precio_base,
                'subtotal' => $producto->precio_base,
            ];
        }
        
        $this->updateCartTotals();
        $this->search_producto = '';
        $this->productos_suggestions = [];
    }
    
    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->updateCartTotals();
    }
    
    public function updateCartTotals()
    {
        foreach ($this->cart as $index => $item) {
            $this->cart[$index]['subtotal'] = $item['cantidad'] * $item['precio_unit'];
        }
        
        $subtotal = collect($this->cart)->sum('subtotal');
        $this->total = $subtotal - $this->descuento_global;
    }
    
    public function procesarVenta()
    {
        $this->validate();
        
        if (empty($this->cart)) {
            $this->addError('cart', 'El carrito está vacío');
            return;
        }
        
        try {
            $ventaData = [
                'sucursal_id' => auth()->user()->sucursal_id ?: 1,
                'cliente_id' => $this->cliente_id,
                'tipo_doc' => $this->tipo_doc,
                'items' => $this->cart,
                'descuento_global_tipo' => 'monto',
                'descuento_global_valor' => $this->descuento_global,
            ];
            
            $venta = app(VentaService::class)->crear($ventaData);
            app(VentaService::class)->procesar($venta);
            
            session()->flash('success', 'Venta procesada exitosamente');
            
            $this->resetCart();
            
            return redirect()->route('ventas.show', $venta);
            
        } catch (\Exception $e) {
            $this->addError('general', $e->getMessage());
        }
    }
    
    private function resetCart()
    {
        $this->cart = [];
        $this->total = 0;
        $this->descuento_global = 0;
        $this->cliente_id = null;
        $this->tipo_doc = 'interno';
    }
    
    public function render()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        
        return view('livewire.ventas.pos', [
            'clientes' => $clientes
        ])->layout('layouts.app', ['title' => 'Punto de Venta']);
    }
}
```

---

## 🎨 **VISTAS LIVEWIRE**

### **📊 Dashboard View**
```blade
{{-- resources/views/livewire/dashboard/dashboard.blade.php --}}
<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Ventas de hoy -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm">💰</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Ventas Hoy</dt>
                            <dd class="text-lg font-medium text-gray-900">S/ {{ number_format($stats['ventas_hoy'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ventas del mes -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm">📈</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Ventas del Mes</dt>
                            <dd class="text-lg font-medium text-gray-900">S/ {{ number_format($stats['ventas_mes'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total productos -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm">📦</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Productos</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['productos_total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stock bajo -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm">⚠️</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Stock Bajo</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['productos_bajo_stock'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Sales & Low Stock -->
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Recent Sales -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ventas Recientes</h3>
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @forelse($recentSales as $venta)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            Venta #{{ $venta->id }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $venta->cliente?->nombre ?? 'Cliente General' }} • 
                                            {{ $venta->fecha->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <x-ui.status-badge :status="$venta->estado" type="venta" />
                                        <span class="text-sm font-medium text-gray-900">
                                            S/ {{ number_format($venta->total_neto, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="py-4 text-center text-gray-500">
                                No hay ventas recientes
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Low Stock Products -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Productos con Stock Bajo</h3>
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @forelse($lowStockProducts as $producto)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $producto->nombre }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $producto->codigo }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @php
                                            $totalStock = $producto->stocks->sum('stock_actual');
                                            $status = $totalStock <= 0 ? 'sin_stock' : 'bajo';
                                        @endphp
                                        <x-ui.status-badge :status="$status" type="stock" />
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $totalStock }} {{ $producto->unidad }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="py-4 text-center text-gray-500">
                                No hay productos con stock bajo
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
```

### **🛒 POS View**
```blade
{{-- resources/views/livewire/ventas/pos.blade.php --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Selector de productos -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Buscar Productos</h3>
                
                <!-- Buscador -->
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.debounce.300ms="search_producto"
                        placeholder="Buscar por nombre, código o SKU..."
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                    
                    <!-- Sugerencias -->
                    @if(count($productos_suggestions) > 0)
                        <div class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                            @foreach($productos_suggestions as $producto)
                                <div 
                                    wire:click="addToCart({{ $producto->id }})"
                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-600 hover:text-white"
                                >
                                    <div class="flex items-center">
                                        <span class="font-medium truncate">{{ $producto->nombre }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ $producto->codigo }}</span>
                                    </div>
                                    <div class="text-sm">
                                        S/ {{ number_format($producto->precio_base, 2) }} • 
                                        Stock: {{ $producto->getTotalStock() }} {{ $producto->unidad }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Carrito de compras -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Carrito de Venta</h3>
                
                <!-- Cliente y tipo de documento -->
                <div class="space-y-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select wire:model="cliente_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Cliente General</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                        <select wire:model="tipo_doc" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="interno">Nota Interna</option>
                            <option value="boleta">Boleta</option>
                            <option value="factura">Factura</option>
                        </select>
                    </div>
                </div>
                
                <!-- Items del carrito -->
                <div class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                    @forelse($cart as $index => $item)
                        <div class="flex items-center space-x-2 p-2 border rounded">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item['nombre'] }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <input 
                                        type="number" 
                                        wire:model.defer="cart.{{ $index }}.cantidad"
                                        wire:change="updateCartTotals"
                                        step="0.001"
                                        min="0.001"
                                        class="w-20 text-sm border-gray-300 rounded"
                                    >
                                    <span class="text-xs text-gray-500">×</span>
                                    <input 
                                        type="number" 
                                        wire:model.defer="cart.{{ $index }}.precio_unit"
                                        wire:change="updateCartTotals"
                                        step="0.01"
                                        min="0"
                                        class="w-20 text-sm border-gray-300 rounded"
                                    >
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium">S/ {{ number_format($item['subtotal'], 2) }}</p>
                                <button 
                                    wire:click="removeFromCart({{ $index }})"
                                    class="text-red-500 hover:text-red-700 text-xs"
                                >
                                    Quitar
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">Carrito vacío</p>
                    @endforelse
                </div>
                
                <!-- Descuento global -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Descuento Global</label>
                    <input 
                        type="number" 
                        wire:model.defer="descuento_global"
                        wire:change="updateCartTotals"
                        step="0.01"
                        min="0"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                
                <!-- Total -->
                <div class="border-t pt-4">
                    <div class="flex justify-between text-lg font-bold">
                        <span>TOTAL:</span>
                        <span>S/ {{ number_format($total, 2) }}</span>
                    </div>
                </div>
                
                <!-- Botón procesar -->
                <div class="mt-4">
                    <x-ui.button 
                        wire:click="procesarVenta" 
                        variant="primary" 
                        size="lg" 
                        class="w-full"
                        :disabled="empty($cart)"
                    >
                        Procesar Venta
                    </x-ui.button>
                </div>
                
                @error('general')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>
```

---

## 🔍 **COMPONENTES DE NAVEGACIÓN**

### **📱 Sidebar Component**
```blade
{{-- resources/views/components/navigation/sidebar.blade.php --}}
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
        <!-- Logo -->
        <div class="flex h-16 shrink-0 items-center">
            <x-application-logo class="h-8 w-auto text-white" />
            <span class="ml-2 text-white font-bold text-xl">SmartKet</span>
        </div>
        
        <!-- Navigation -->
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('dashboard') }}" 
                               class="{{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <span class="text-xl">📊</span>
                                Dashboard
                            </a>
                        </li>
                        
                        <!-- POS -->
                        <li>
                            <a href="{{ route('pos') }}" 
                               class="{{ request()->routeIs('pos') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <span class="text-xl">🛒</span>
                                Punto de Venta
                            </a>
                        </li>
                        
                        <!-- Productos -->
                        <li>
                            <a href="{{ route('productos.index') }}" 
                               class="{{ request()->routeIs('productos.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <span class="text-xl">📦</span>
                                Productos
                            </a>
                        </li>
                        
                        <!-- Ventas -->
                        <li>
                            <a href="{{ route('ventas.index') }}" 
                               class="{{ request()->routeIs('ventas.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <span class="text-xl">💰</span>
                                Ventas
                            </a>
                        </li>
                        
                        <!-- Inventario -->
                        <li>
                            <a href="{{ route('inventario.stock') }}" 
                               class="{{ request()->routeIs('inventario.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <span class="text-xl">📊</span>
                                Inventario
                            </a>
                        </li>
                        
                        <!-- Caja (condicional) -->
                        @if(app(\App\Services\FeatureFlagService::class)->isEnabled('caja'))
                            <li>
                                <a href="{{ route('caja.sesiones') }}" 
                                   class="{{ request()->routeIs('caja.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                    <span class="text-xl">💵</span>
                                    Caja
                                </a>
                            </li>
                        @endif
                        
                        <!-- Reportes -->
                        <li>
                            <a href="{{ route('reportes.ventas') }}" 
                               class="{{ request()->routeIs('reportes.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <span class="text-xl">📈</span>
                                Reportes
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Settings section -->
                <li class="mt-auto">
                    <ul role="list" class="-mx-2 space-y-1">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white hover:bg-gray-800 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <span class="text-xl">⚙️</span>
                                Configuración
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
```

### **🔝 Header Component**
```blade
{{-- resources/views/components/navigation/header.blade.php --}}
<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
    <!-- Mobile menu button -->
    <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
        <span class="sr-only">Abrir sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
    
    <!-- Separator -->
    <div class="h-6 w-px bg-gray-900/10 lg:hidden" aria-hidden="true"></div>
    
    <!-- Breadcrumb / Page title -->
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <div class="flex items-center">
            <h1 class="text-lg font-semibold text-gray-900">
                {{ $title ?? 'SmartKet ERP' }}
            </h1>
        </div>
    </div>
    
    <!-- Right side -->
    <div class="flex items-center gap-x-4 lg:gap-x-6">
        <!-- Notifications -->
        <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
            <span class="sr-only">Ver notificaciones</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
        </button>
        
        <!-- Separator -->
        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-900/10" aria-hidden="true"></div>
        
        <!-- Profile dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5">
                <span class="sr-only">Abrir menú de usuario</span>
                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-700">
                        {{ substr(auth()->user()->nombre, 0, 1) }}
                    </span>
                </div>
                <span class="hidden lg:flex lg:items-center">
                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ auth()->user()->nombre }}</span>
                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </span>
            </button>
            
            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5">
                <a href="#" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Mi Perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
```

---

## 📊 **ESTADO DE IMPLEMENTACIÓN**

### **✅ Completado**
```
Layouts:
- ✅ app.blade.php (layout principal)
- ✅ guest.blade.php (layout invitados)

Componentes UI:
- ✅ Button component
- ✅ Input component  
- ✅ Status badge component
- ✅ Data table component

Navegación:
- ✅ Sidebar component
- ✅ Header component
```

### **🔄 En Progreso**
```
Livewire Components:
- 🔄 Dashboard (estructura básica)
- 🔄 ProductoTable (funcional básico)
- 🔄 POS (estructura principal)

Vistas:
- 🔄 Dashboard view (stats básicos)
- 🔄 POS view (interfaz básica)
```

### **❌ Pendiente**
```
Livewire Components:
- ❌ ProductoForm
- ❌ VentaTable
- ❌ VentaForm
- ❌ InventarioStock
- ❌ InventarioMovimientos
- ❌ ReporteVentas
- ❌ ContextSelector

Vistas:
- ❌ Todas las vistas de productos
- ❌ Todas las vistas de ventas
- ❌ Todas las vistas de inventario
- ❌ Todas las vistas de reportes

Componentes UI:
- ❌ Modal component
- ❌ Loading spinner component
- ❌ Pagination component
- ❌ Search component
- ❌ Date picker component
```

---

**🎨 ESTA ESPECIFICACIÓN DEFINE LA ARQUITECTURA FRONTEND COMPLETA**

*Actualizado: 30 Agosto 2025*  
*Estado: 📋 ESPECIFICACIÓN UI/UX COMPLETA*  
*Próximo paso: Implementar componentes Livewire y vistas pendientes*
