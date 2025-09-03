@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Productos</h1>
            <p class="text-sm text-gray-600 mt-1">
                Gestiona tu catálogo de productos - {{ $empresa->nombre }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('categorias.index') }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <span class="text-lg mr-2">🏷️</span>
                Categorías
            </a>
            <a href="{{ route('productos.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <span class="text-lg mr-2">➕</span>
                Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-md">
                    <span class="text-2xl">📦</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_productos']) }}</div>
                    <div class="text-sm text-gray-500">Total Productos</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-md">
                    <span class="text-2xl">⚠️</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['productos_stock_bajo'] }}</div>
                    <div class="text-sm text-gray-500">Stock Bajo</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-md">
                    <span class="text-2xl">🏷️</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['categorias_count'] }}</div>
                    <div class="text-sm text-gray-500">Categorías</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-md">
                    <span class="text-2xl">💰</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">S/ {{ number_format($stats['valor_inventario'], 2) }}</div>
                    <div class="text-sm text-gray-500">Valor Inventario</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('productos.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nombre, código..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select name="categoria_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->icono }} {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="activo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Productos -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Producto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Categoría
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Precios
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stock
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($productos as $producto)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($producto->imagen_url)
                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <span class="text-lg">📦</span>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                        @if($producto->codigo_interno)
                                            <div class="text-sm text-gray-500">SKU: {{ $producto->codigo_interno }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color: {{ $producto->categoria->color }}20; color: {{ $producto->categoria->color }}">
                                    {{ $producto->categoria->icono }} {{ $producto->categoria->nombre }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>Costo: S/ {{ number_format($producto->precio_costo, 2) }}</div>
                                <div>Venta: S/ {{ number_format($producto->precio_venta, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($producto->maneja_stock)
                                    @php
                                        $stock = $producto->stocks->first();
                                        $cantidad = $stock ? $stock->cantidad_actual : 0;
                                        $esStockBajo = $cantidad <= $producto->stock_minimo;
                                    @endphp
                                    <div class="text-sm">
                                        <span class="{{ $esStockBajo ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            {{ number_format($cantidad, 2) }} {{ $producto->unidad_medida }}
                                        </span>
                                        @if($esStockBajo)
                                            <div class="text-xs text-red-500">⚠️ Stock bajo</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Sin control</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($producto->activo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('productos.show', $producto) }}" 
                                   class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('productos.edit', $producto) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <span class="text-4xl block mb-4">📦</span>
                                    <p class="text-lg font-medium">No hay productos registrados</p>
                                    <p class="text-sm">Comienza agregando tu primer producto</p>
                                    <a href="{{ route('productos.create') }}" 
                                       class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Crear Producto
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($productos->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $productos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
