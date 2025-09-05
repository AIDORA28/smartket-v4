@section('title', 'Gestión de Inventario')

<div class="space-y-6">
    <!-- Header con estadísticas -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Gestión de Inventario</h1>
            
            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Productos -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Productos</p>
                            <p class="text-3xl font-bold">{{ number_format($totalProductos) }}</p>
                        </div>
                        <div class="text-blue-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Stock Bajo -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">Stock Bajo</p>
                            <p class="text-3xl font-bold">{{ $productosStockBajo }}</p>
                        </div>
                        <div class="text-yellow-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Sin Stock -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm font-medium">Sin Stock</p>
                            <p class="text-3xl font-bold">{{ $productosSinStock }}</p>
                        </div>
                        <div class="text-red-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C17.5 2 22 6.5 22 12S17.5 22 12 22 2 17.5 2 12 6.5 2 12 2M12 4C7.59 4 4 7.59 4 12S7.59 20 12 20 20 16.41 20 12 16.41 4 12 4M15.5 17L9 10.5L10.5 9L15.5 14L15.5 17Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Valor Inventario -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Valor Inventario</p>
                            <p class="text-3xl font-bold">S/ {{ number_format($valorInventario, 2) }}</p>
                        </div>
                        <div class="text-green-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 15H9C9 16.08 10.37 17 12 17S15 16.08 15 15C15 13.9 13.96 13.5 11.76 12.97C9.64 12.44 7 11.78 7 9C7 7.21 8.47 5.69 10.5 5.18V3H13.5V5.18C15.53 5.69 17 7.21 17 9H15C15 7.92 13.63 7 12 7S9 7.92 9 9C9 10.1 10.04 10.5 12.24 11.03C14.36 11.56 17 12.22 17 15C17 16.79 15.53 18.31 13.5 18.82V21H10.5V18.82C8.47 18.31 7 16.79 7 15Z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y controles -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
                <!-- Búsqueda -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Producto</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               placeholder="Nombre, código interno o código de barras..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filtro por categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select wire:model.live="categoriaFiltro" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->icono }} {{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado Stock</label>
                    <select wire:model.live="stockFiltro" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="todos">Todos los productos</option>
                        <option value="bajo">Stock bajo</option>
                        <option value="sin_stock">Sin stock</option>
                        <option value="exceso">Stock en exceso</option>
                    </select>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('productos.crear') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuevo Producto
                </a>
                
                <a href="{{ route('categorias.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4H5m4 8H5"></path>
                    </svg>
                    Categorías
                </a>
            </div>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="cambiarOrden('nombre')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center space-x-1">
                                <span>Producto</span>
                                @if($ordenarPor === 'nombre')
                                    <svg class="w-4 h-4 {{ $direccion === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th wire:click="cambiarOrden('categoria')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center space-x-1">
                                <span>Categoría</span>
                                @if($ordenarPor === 'categoria')
                                    <svg class="w-4 h-4 {{ $direccion === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Código
                        </th>
                        <th wire:click="cambiarOrden('stock')" 
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center justify-center space-x-1">
                                <span>Stock</span>
                                @if($ordenarPor === 'stock')
                                    <svg class="w-4 h-4 {{ $direccion === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Precio
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($productos as $producto)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($producto->imagen_url)
                                        <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" 
                                             class="h-10 w-10 rounded-lg object-cover mr-4">
                                    @else
                                        <div class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                            <span class="text-gray-500 text-xl">📦</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                        @if($producto->descripcion)
                                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ $producto->descripcion }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($producto->categoria)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $producto->categoria->icono }} {{ $producto->categoria->nombre }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Sin categoría</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $producto->codigo_interno }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    @php
                                        $stockClass = 'text-gray-900';
                                        $stockIcon = '📦';
                                        if ($producto->stock_total <= 0) {
                                            $stockClass = 'text-red-600';
                                            $stockIcon = '❌';
                                        } elseif ($producto->stock_total <= $producto->stock_minimo) {
                                            $stockClass = 'text-yellow-600';
                                            $stockIcon = '⚠️';
                                        } elseif ($producto->stock_total > $producto->stock_maximo) {
                                            $stockClass = 'text-blue-600';
                                            $stockIcon = '📈';
                                        }
                                    @endphp
                                    <span class="text-lg font-semibold {{ $stockClass }}">
                                        {{ number_format($producto->stock_total, 2) }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $stockIcon }} {{ $producto->unidad_medida }}</span>
                                    @if($producto->stock_reservado > 0)
                                        <span class="text-xs text-orange-600">{{ $producto->stock_reservado }} reservado</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">S/ {{ number_format($producto->precio_venta, 2) }}</div>
                                    <div class="text-gray-500">Costo: S/ {{ number_format($producto->precio_costo, 2) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                <button wire:click="abrirModalAjuste({{ $producto->id }})"
                                        class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-lg transition-colors">
                                    Ajustar Stock
                                </button>
                                <a href="{{ route('productos.editar', $producto->id) }}" 
                                   class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1 rounded-lg transition-colors">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <p class="text-lg font-medium mb-2">No se encontraron productos</p>
                                    <p class="text-sm mb-4">Ajusta los filtros o agrega productos nuevos</p>
                                    <a href="{{ route('productos.crear') }}" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        Agregar Producto
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($productos->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $productos->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de ajuste de stock -->
    @if($mostrarModalAjuste)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" 
                 @click.away="cerrarModalAjuste">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Ajustar Stock - {{ $productoSeleccionado->nombre }}
                </h3>
                
                <div class="space-y-4">
                    <!-- Stock actual -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Stock actual:</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($productoSeleccionado->stocks->sum('cantidad_actual'), 2) }} 
                            {{ $productoSeleccionado->unidad_medida }}
                        </p>
                    </div>

                    <!-- Tipo de ajuste -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Ajuste</label>
                        <select wire:model="tipoAjuste" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="entrada">Entrada (+)</option>
                            <option value="salida">Salida (-)</option>
                            <option value="ajuste">Ajuste a cantidad específica</option>
                        </select>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            @if($tipoAjuste === 'ajuste')
                                Nueva Cantidad
                            @else
                                Cantidad a {{ $tipoAjuste === 'entrada' ? 'Agregar' : 'Retirar' }}
                            @endif
                        </label>
                        <input type="number" 
                               wire:model="cantidadAjuste" 
                               step="0.01" 
                               min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        @error('cantidadAjuste')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Motivo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Motivo</label>
                        <input type="text" 
                               wire:model="motivoAjuste" 
                               placeholder="Ej: Inventario físico, producto dañado, etc."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        @error('motivoAjuste')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="cerrarModalAjuste" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button wire:click="ejecutarAjusteStock" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Confirmar Ajuste
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@if (session('message'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
         x-data="{ show: true }" 
         x-show="show" 
         x-transition 
         x-init="setTimeout(() => show = false, 3000)">
        {{ session('message') }}
    </div>
@endif
