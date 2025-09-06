<div class="space-y-6">
    <!-- Header con título y botón Nuevo Producto -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0 mb-8">
        <div>
            <h1 class="font-bold text-3xl text-gray-900 leading-tight">
                📦 Catálogo de Productos
            </h1>
            <p class="text-lg text-gray-600 mt-2">Gestiona la información comercial de tus productos</p>
        </div>
        <a href="{{ route('productos.crear') }}" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-xl font-semibold text-base text-white uppercase tracking-wider hover:from-blue-700 hover:to-blue-800 focus:from-blue-700 focus:to-blue-800 active:from-blue-900 active:to-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nuevo Producto
        </a>
    </div>

    <!-- Filtros mejorados -->
    <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
        <div class="p-8 bg-gradient-to-br from-blue-50 via-white to-indigo-50">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Búsqueda -->
                <div>
                    <label for="search" class="block text-sm font-semibold text-gray-800 mb-2">🔍 Buscar Producto</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" 
                               type="text" 
                               id="search"
                               class="focus:ring-2 focus:ring-blue-500 focus:border-transparent block w-full pl-12 pr-4 py-3 text-sm border border-gray-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-all duration-200 placeholder-gray-500" 
                               placeholder="Nombre, código interno o código de barras...">
                    </div>
                </div>

                <!-- Categoría -->
                <div>
                    <label for="categoria" class="block text-sm font-semibold text-gray-800 mb-2">🏷️ Categoría</label>
                    <select wire:model.live="categoria_id" 
                            id="categoria"
                            class="block w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm hover:shadow-md transition-all duration-200">
                        <option value="">Todas las categorías</option>
                        @foreach($this->categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-semibold text-gray-800 mb-2">📊 Estado</label>
                    <select wire:model.live="estado" 
                            id="estado"
                            class="block w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm hover:shadow-md transition-all duration-200">
                        <option value="">Todos los estados</option>
                        <option value="1">✅ Activos</option>
                        <option value="0">❌ Inactivos</option>
                    </select>
                </div>

                <!-- Registros por página -->
                <div>
                    <label for="per_page" class="block text-sm font-semibold text-gray-800 mb-2">📄 Mostrar</label>
                    <select wire:model.live="per_page" 
                            id="per_page"
                            class="block w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm hover:shadow-md transition-all duration-200">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
            </div>
            
            <!-- Estadísticas y acciones -->
            <div class="flex flex-wrap items-center justify-between mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <div class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-100 border border-blue-200">
                        <span class="text-2xl font-bold text-blue-600">{{ $this->productos->total() }}</span>
                        <span class="ml-2 text-sm text-blue-700 font-medium">productos encontrados</span>
                    </div>
                    
                    @if($search || $categoria_id || $estado)
                        <button wire:click="limpiarFiltros" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Limpiar filtros
                        </button>
                    @endif
                </div>
                
                <a href="{{ route('inventario.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Gestionar Inventario
                </a>
            </div>
        </div>
    </div>

    <!-- Mensajes mejorados -->
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        ✅ {{ session('message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        ❌ {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla mejorada -->
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">📦 Lista de Productos</h3>
        </div>
        <div class="overflow-x-auto">
            @if($this->productos->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Códigos
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Categoría
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Precios
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Stock Total
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($this->productos as $producto)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($producto->imagen_url)
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <img class="h-12 w-12 rounded-xl object-cover shadow-sm border border-gray-200" 
                                                     src="{{ asset('storage/' . $producto->imagen_url) }}" 
                                                     alt="{{ $producto->nombre }}">
                                            </div>
                                        @else
                                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center shadow-sm border border-gray-200">
                                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $producto->nombre }}</div>
                                            @if($producto->descripcion)
                                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($producto->descripcion, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        @if($producto->codigo_interno)
                                            <div class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                🏷️ {{ $producto->codigo_interno }}
                                            </div>
                                        @endif
                                        @if($producto->codigo_barra)
                                            <div class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                                📊 {{ $producto->codigo_barra }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($producto->categoria)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                            {{ $producto->categoria->nombre }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">Sin categoría</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="text-sm font-semibold text-green-600">
                                            💰 S/{{ number_format($producto->precio_venta, 2) }}
                                        </div>
                                        @if($producto->precio_costo)
                                            <div class="text-xs text-gray-500">
                                                Costo: S/{{ number_format($producto->precio_costo, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $stockTotal = $producto->stock_total ?? 0;
                                        $stockClass = $stockTotal > 10 ? 'text-green-600 bg-green-50 border-green-200' : 
                                                     ($stockTotal > 0 ? 'text-yellow-600 bg-yellow-50 border-yellow-200' : 
                                                     'text-red-600 bg-red-50 border-red-200');
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border {{ $stockClass }}">
                                        📦 {{ $stockTotal }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($producto->activo)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            ✅ Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                            ❌ Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('productos.editar', $producto->id) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105" 
                                           title="Editar información del producto">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Editar
                                        </a>
                                        
                                        <a href="{{ route('inventario.index', ['producto_id' => $producto->id]) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-green-300 rounded-lg text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105"
                                           title="Ver stock e inventario">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            Stock
                                        </a>
                                        
                                        <button wire:click="delete({{ $producto->id }})" 
                                                wire:confirm="¿Estás seguro de que deseas eliminar este producto?"
                                                class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105"
                                                title="Eliminar producto">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-16 px-6">
                    <div class="mx-auto h-24 w-24 text-gray-300 mb-6">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">📦 Tu catálogo está vacío</h3>
                    <p class="text-gray-500 mb-8 max-w-sm mx-auto">Comienza agregando tu primer producto al catálogo para empezar a gestionar tu inventario.</p>
                    <a href="{{ route('productos.crear') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-xl font-semibold text-base text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Crear Primer Producto
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Paginación mejorada -->
    @if($this->productos->hasPages())
        <div class="flex items-center justify-center">
            <div class="bg-white rounded-2xl shadow-lg p-4">
                {{ $this->productos->links() }}
            </div>
        </div>
    @endif
</div>
