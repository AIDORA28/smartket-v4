<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $editing ? 'Editar Producto' : 'Nuevo Producto' }}
            </h2>
            <a href="{{ route('productos.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

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
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('nombre') border-red-300 @enderror">
                            </div>
                            @error('nombre')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Código -->
                        <div>
                            <label for="codigo" class="block text-sm font-medium text-gray-700">
                                Código *
                            </label>
                            <div class="mt-1">
                                <input wire:model.blur="codigo" 
                                       type="text" 
                                       id="codigo" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('codigo') border-red-300 @enderror">
                            </div>
                            @error('codigo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Código de Barras -->
                        <div>
                            <label for="codigo_barras" class="block text-sm font-medium text-gray-700">
                                Código de Barras
                            </label>
                            <div class="mt-1">
                                <input wire:model="codigo_barras" 
                                       type="text" 
                                       id="codigo_barras" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('codigo_barras') border-red-300 @enderror">
                            </div>
                            @error('codigo_barras')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Categoría -->
                        <div>
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700">
                                Categoría *
                            </label>
                            <div class="mt-1">
                                <select wire:model.live="categoria_id" 
                                        id="categoria_id" 
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('categoria_id') border-red-300 @enderror">
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($this->categorias as $categoria)
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
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('descripcion') border-red-300 @enderror"
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
                            <label for="precio_compra" class="block text-sm font-medium text-gray-700">
                                Precio de Compra *
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input wire:model="precio_compra" 
                                       type="number" 
                                       step="0.01" 
                                       min="0"
                                       id="precio_compra" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md @error('precio_compra') border-red-300 @enderror">
                            </div>
                            @error('precio_compra')
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
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input wire:model="precio_venta" 
                                       type="number" 
                                       step="0.01" 
                                       min="0"
                                       id="precio_venta" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md @error('precio_venta') border-red-300 @enderror">
                            </div>
                            @error('precio_venta')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Actual -->
                        <div>
                            <label for="stock_actual" class="block text-sm font-medium text-gray-700">
                                Stock Actual *
                            </label>
                            <div class="mt-1">
                                <input wire:model="stock_actual" 
                                       type="number" 
                                       min="0"
                                       id="stock_actual" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('stock_actual') border-red-300 @enderror">
                            </div>
                            @error('stock_actual')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Mínimo -->
                        <div>
                            <label for="stock_minimo" class="block text-sm font-medium text-gray-700">
                                Stock Mínimo *
                            </label>
                            <div class="mt-1">
                                <input wire:model="stock_minimo" 
                                       type="number" 
                                       min="0"
                                       id="stock_minimo" 
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('stock_minimo') border-red-300 @enderror">
                            </div>
                            @error('stock_minimo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Margen de Ganancia -->
                    @if($precio_compra > 0)
                        <div class="mt-4 p-3 bg-gray-50 rounded-md">
                            <div class="text-sm text-gray-600">
                                <strong>Margen de ganancia:</strong> 
                                @if($precio_venta > $precio_compra)
                                    ${{ number_format($precio_venta - $precio_compra, 2) }} 
                                    ({{ number_format((($precio_venta - $precio_compra) / $precio_compra) * 100, 1) }}%)
                                @else
                                    <span class="text-red-600">Precio de venta menor al costo</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Imagen y Configuración -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Imagen y Configuración</h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Imagen -->
                        <div>
                            <label for="imagen" class="block text-sm font-medium text-gray-700">
                                Imagen del Producto
                            </label>
                            <div class="mt-1">
                                <input wire:model="imagen" 
                                       type="file" 
                                       id="imagen" 
                                       accept="image/*"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('imagen') border-red-300 @enderror">
                            </div>
                            @error('imagen')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Preview de imagen -->
                            <div class="mt-3">
                                @if($imagen)
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $imagen->temporaryUrl() }}" 
                                                 alt="Preview" 
                                                 class="h-20 w-20 rounded-lg object-cover">
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Nueva imagen seleccionada
                                        </div>
                                    </div>
                                @elseif($editing && $producto && $producto->imagen)
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                                 alt="{{ $producto->nombre }}" 
                                                 class="h-20 w-20 rounded-lg object-cover">
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Imagen actual
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Configuración -->
                        <div class="space-y-4">
                            <!-- Estado -->
                            <div class="flex items-center">
                                <input wire:model="activo" 
                                       id="activo" 
                                       type="checkbox" 
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="activo" class="ml-2 block text-sm text-gray-900">
                                    Producto activo
                                </label>
                            </div>

                            <!-- Control de Stock -->
                            <div class="flex items-center">
                                <input wire:model="controla_stock" 
                                       id="controla_stock" 
                                       type="checkbox" 
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="controla_stock" class="ml-2 block text-sm text-gray-900">
                                    Controlar stock
                                </label>
                            </div>

                            <div class="text-xs text-gray-500">
                                <p>• Producto activo: Se muestra en catálogos y puede venderse</p>
                                <p>• Controlar stock: El sistema descontará automáticamente el stock en las ventas</p>
                            </div>
                        </div>
                    </div>
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
