<div class="space-y-6">
    <!-- Header optimizado -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0 mb-8">
        <div>
            <h1 class="font-bold text-3xl text-gray-900 leading-tight">
                📦 Catálogo de Productos
            </h1>
            <p class="text-lg text-gray-600 mt-2">Gestiona la información comercial de tus productos</p>
        </div>
        <a href="{{ route('productos.crear') }}" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-xl font-semibold text-base text-white uppercase tracking-wider hover:from-blue-700 hover:to-blue-800 transition-colors duration-200">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nuevo Producto
        </a>
    </div>

    <!-- Filtros simplificados -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Búsqueda -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input wire:model.live.debounce.300ms="search"
                       type="text" 
                       placeholder="Nombre, código interno o código de barras..."
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                <select wire:model.live="categoria_id" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select wire:model.live="estado" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
            </div>
        </div>

        @if($search || $categoria_id || $estado)
            <div class="mt-4">
                <button wire:click="limpiarFiltros" 
                        class="text-sm text-gray-500 hover:text-gray-700 underline">
                    Limpiar filtros
                </button>
            </div>
        @endif
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
            <h3 class="text-sm font-medium opacity-90">Total de Productos</h3>
            <span class="text-2xl font-bold">{{ $productos->total() }}</span>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-sm p-6 text-white">
            <h3 class="text-sm font-medium opacity-90">Página Actual</h3>
            <span class="text-2xl font-bold">{{ $productos->currentPage() }}</span>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
            <h3 class="text-sm font-medium opacity-90">Total de Páginas</h3>
            <span class="text-2xl font-bold">{{ $productos->lastPage() }}</span>
        </div>
    </div>

    <!-- Lista de productos optimizada -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($productos->count() > 0)
            <!-- Grid responsive más eficiente -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                @foreach($productos as $producto)
                    <div class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                        <!-- Imagen optimizada -->
                        <div class="w-full h-32 bg-gray-200 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                            @if($producto->imagen_url)
                                <img loading="lazy" 
                                     src="{{ asset('storage/' . $producto->imagen_url) }}" 
                                     alt="{{ $producto->nombre }}"
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl">📦</span>
                            @endif
                        </div>

                        <!-- Info del producto -->
                        <div class="space-y-2">
                            <h4 class="font-semibold text-gray-900 truncate">{{ $producto->nombre }}</h4>
                            
                            @if($producto->categoria)
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    {{ $producto->categoria->nombre }}
                                </span>
                            @endif

                            <!-- Precios -->
                            <div class="space-y-1">
                                <div class="font-bold text-green-600">
                                    💰 S/{{ number_format($producto->precio_venta, 2) }}
                                </div>
                                @if($producto->precio_costo > 0)
                                    <div class="text-sm text-gray-500">
                                        Costo: S/{{ number_format($producto->precio_costo, 2) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Códigos -->
                            @if($producto->codigo_interno)
                                <div class="text-xs text-gray-600">
                                    🏷️ {{ $producto->codigo_interno }}
                                </div>
                            @endif

                            <!-- Estado y acciones -->
                            <div class="flex items-center justify-between pt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $producto->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                                </span>

                                <button wire:click="toggleEstado({{ $producto->id }})"
                                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $producto->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $productos->links() }}
            </div>
        @else
            <!-- Estado vacío -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m2.2-9h11.6M7 21h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productos</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search || $categoria_id || $estado)
                        No se encontraron productos con los filtros seleccionados.
                    @else
                        Comienza creando tu primer producto.
                    @endif
                </p>
                @if(!$search && !$categoria_id && !$estado)
                    <div class="mt-6">
                        <a href="{{ route('productos.crear') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nuevo Producto
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
