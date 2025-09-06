<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0 mb-8">
        <div>
            <h1 class="font-bold text-3xl text-gray-900 leading-tight">
                ➕ Nuevo Producto (Versión Simple)
            </h1>
            <p class="text-lg text-gray-600 mt-2">
                Registra un nuevo producto en el catálogo - Versión simplificada para debug
            </p>
        </div>
        <a href="{{ route('productos.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al Catálogo
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        @if (session()->has('message'))
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                <div class="text-sm text-green-800">
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-6">
            <h3 class="text-lg font-medium text-gray-900">Información Básica</h3>
            
            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">
                    Nombre del Producto *
                </label>
                <input wire:model="nombre" 
                       type="text" 
                       id="nombre" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Categoría -->
            <div>
                <label for="categoria_id" class="block text-sm font-medium text-gray-700">
                    Categoría *
                </label>
                <select wire:model="categoria_id" 
                        id="categoria_id" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Seleccionar categoría</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precios -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="precio_costo" class="block text-sm font-medium text-gray-700">
                        Precio de Costo *
                    </label>
                    <input wire:model="precio_costo" 
                           type="number" 
                           step="0.01" 
                           min="0"
                           id="precio_costo" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('precio_costo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="precio_venta" class="block text-sm font-medium text-gray-700">
                        Precio de Venta *
                    </label>
                    <input wire:model="precio_venta" 
                           type="number" 
                           step="0.01" 
                           min="0"
                           id="precio_venta" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('precio_venta')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700">
                    Descripción
                </label>
                <textarea wire:model="descripcion" 
                          id="descripcion" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Descripción del producto..."></textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        wire:click="cancel"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>Crear Producto</span>
                    <span wire:loading>Creando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
