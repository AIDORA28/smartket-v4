@props(['breadcrumbs' => []])

@php
$breadcrumbs = $breadcrumbs ?: [];
$currentRoute = request()->route()->getName();

// Auto-generate breadcrumbs based on current route
$routeBreadcrumbs = [
    'dashboard' => [
        ['name' => 'Dashboard', 'current' => true]
    ],
    'productos.index' => [
        ['name' => 'Productos', 'url' => route('productos.index'), 'current' => true]
    ],
    'productos.create' => [
        ['name' => 'Productos', 'url' => route('productos.index')],
        ['name' => 'Nuevo Producto', 'current' => true]
    ],
    'productos.editar' => [
        ['name' => 'Productos', 'url' => route('productos.index')],
        ['name' => 'Editar Producto', 'current' => true]
    ],
    'categorias.index' => [
        ['name' => 'Productos', 'url' => route('productos.index')],
        ['name' => 'Categorías', 'current' => true]
    ],
    'ventas.index' => [
        ['name' => 'Ventas', 'url' => route('ventas.index'), 'current' => true]
    ],
    'pos.index' => [
        ['name' => 'Ventas', 'url' => route('ventas.index')],
        ['name' => 'Punto de Venta', 'current' => true]
    ],
    'clientes.index' => [
        ['name' => 'Ventas', 'url' => route('ventas.index')],
        ['name' => 'Clientes', 'current' => true]
    ],
    'caja.index' => [
        ['name' => 'Caja', 'current' => true]
    ],
    'compras.index' => [
        ['name' => 'Compras', 'current' => true]
    ],
    'proveedores.index' => [
        ['name' => 'Compras', 'url' => route('compras.index')],
        ['name' => 'Proveedores', 'current' => true]
    ],
    'inventario.index' => [
        ['name' => 'Productos', 'url' => route('productos.index')],
        ['name' => 'Inventario', 'current' => true]
    ],
    'lotes.index' => [
        ['name' => 'Lotes y Vencimientos', 'current' => true]
    ],
    'reportes.index' => [
        ['name' => 'Reportes', 'current' => true]
    ]
];

$breadcrumbs = $breadcrumbs ?: ($routeBreadcrumbs[$currentRoute] ?? []);
@endphp

@if(count($breadcrumbs) > 0)
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <!-- Home -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span class="sr-only">Inicio</span>
                </a>
            </li>

            @foreach($breadcrumbs as $breadcrumb)
                <li>
                    <div class="flex items-center">
                        <!-- Separator -->
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        
                        @if(isset($breadcrumb['current']) && $breadcrumb['current'])
                            <span class="ml-2 text-sm font-medium text-gray-900">
                                {{ $breadcrumb['name'] }}
                            </span>
                        @else
                            <a href="{{ $breadcrumb['url'] ?? '#' }}" 
                               class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                                {{ $breadcrumb['name'] }}
                            </a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </nav>
@endif
