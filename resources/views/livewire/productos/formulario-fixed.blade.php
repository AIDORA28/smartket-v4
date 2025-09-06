<div class="space-y-6">
    <!-- Header con título y botón Volver -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0 mb-8">
        <div>
            <h1 class="font-bold text-3xl text-gray-900 leading-tight">
                {{ $editing ? '✏️ Editar Producto' : '➕ Nuevo Producto' }}
            </h1>
            <p class="text-lg text-gray-600 mt-2">
                {{ $editing ? 'Modifica la información del producto' : 'Registra un nuevo producto en el catálogo' }}
            </p>
        </div>
        <a href="{{ route('productos.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:from-gray-700 hover:to-gray-800 focus:from-gray-700 focus:to-gray-800 active:from-gray-900 active:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al Catálogo
        </a>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Mensajes -->
        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulario -->
        <form wire:submit="save" class="space-y-6">
            <!-- Información Básica -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Información Básica</h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Nombre -->
                        <div class="sm:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre del Producto *
                            </label>
                            <div class="mt-1">
                                <input wire:model="nombre" 
                                       type="text" 
                                       id="nombre" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('nombre')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Código -->
                        <div>
                            <label for="codigo_interno" class="block text-sm font-medium text-gray-700">
                                Código Interno
                            </label>
                            <div class="mt-1">
                                <input wire:model="codigo_interno" 
                                       type="text" 
                                       id="codigo_interno" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('codigo_interno')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Código de Barras -->
                        <div>
                            <label for="codigo_barra" class="block text-sm font-medium text-gray-700">
                                Código de Barras
                            </label>
                            <div class="mt-1">
                                <input wire:model="codigo_barra" 
                                       type="text" 
                                       id="codigo_barra" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('codigo_barra')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Categoría -->
                        <div>
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700">
                                Categoría *
                            </label>
                            <div class="mt-1">
                                <select wire:model="categoria_id" 
                                        id="categoria_id" 
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('categoria_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="sm:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700">
                                Descripción
                            </label>
                            <div class="mt-1">
                                <textarea wire:model="descripcion" 
                                          id="descripcion" 
                                          rows="3" 
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                          placeholder="Descripción del producto..."></textarea>
                            </div>
                            @error('descripcion')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Precios y Stock -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Precios y Stock</h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Precio de Compra -->
                        <div>
                            <label for="precio_costo" class="block text-sm font-medium text-gray-700">
                                Precio de Costo *
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">S/</span>
                                </div>
                                <input wire:model="precio_costo" 
                                       type="number" 
                                       step="0.01" 
                                       min="0"
                                       id="precio_costo" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('precio_costo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Precio de Venta -->
                        <div>
                            <label for="precio_venta" class="block text-sm font-medium text-gray-700">
                                Precio de Venta *
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">S/</span>
                                </div>
                                <input wire:model="precio_venta" 
                                       type="number" 
                                       step="0.01" 
                                       min="0"
                                       id="precio_venta" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('precio_venta')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Mínimo -->
                        <div>
                            <label for="stock_minimo" class="block text-sm font-medium text-gray-700">
                                Stock Mínimo
                            </label>
                            <div class="mt-1">
                                <input wire:model="stock_minimo" 
                                       type="number" 
                                       min="0"
                                       id="stock_minimo" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('stock_minimo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">El stock actual se maneja en el módulo de Inventario</p>
                        </div>
                    </div>

                    <!-- Margen de Ganancia -->
                    @if($precio_costo > 0)
                        <div class="mt-4 p-3 bg-gray-50 rounded-md">
                            <div class="text-sm text-gray-600">
                                <strong>Margen de ganancia:</strong> 
                                @if($precio_venta > $precio_costo)
                                    S/{{ number_format($precio_venta - $precio_costo, 2) }} 
                                    ({{ number_format((($precio_venta - $precio_costo) / $precio_costo) * 100, 1) }}%)
                                @else
                                    <span class="text-red-600">Precio de venta menor al costo</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        wire:click="cancel"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </button>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                        wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ $editing ? 'Actualizar' : 'Crear' }} Producto
                </button>
            </div>
        </form>
    </div>
</div>
