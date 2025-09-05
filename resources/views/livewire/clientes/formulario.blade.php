<div>
    {{-- Encabezado del formulario --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ $isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                {{ $isEditing ? 'Actualiza la información del cliente' : 'Completa los datos del nuevo cliente' }}
            </p>
        </div>
        <button wire:click="$parent.refresh" 
                class="text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Formulario --}}
    <form wire:submit="guardar" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Nombre --}}
            <div class="md:col-span-2">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre completo / Razón social *
                </label>
                <input type="text" 
                       wire:model="nombre" 
                       id="nombre"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nombre') border-red-300 @enderror">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipo de documento --}}
            <div>
                <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de documento *
                </label>
                <select wire:model="tipo_documento" 
                        id="tipo_documento"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tipo_documento') border-red-300 @enderror">
                    <option value="">Seleccionar...</option>
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                    <option value="CE">Carné de Extranjería</option>
                    <option value="PASAPORTE">Pasaporte</option>
                </select>
                @error('tipo_documento')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Número de documento --}}
            <div>
                <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-2">
                    Número de documento *
                </label>
                <input type="text" 
                       wire:model="numero_documento" 
                       id="numero_documento"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('numero_documento') border-red-300 @enderror">
                @error('numero_documento')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" 
                       wire:model="email" 
                       id="email"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                    Teléfono
                </label>
                <input type="text" 
                       wire:model="telefono" 
                       id="telefono"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telefono') border-red-300 @enderror">
                @error('telefono')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dirección --}}
            <div class="md:col-span-2">
                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                    Dirección
                </label>
                <textarea wire:model="direccion" 
                          id="direccion"
                          rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('direccion') border-red-300 @enderror"></textarea>
                @error('direccion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha de nacimiento --}}
            <div>
                <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de nacimiento
                </label>
                <input type="date" 
                       wire:model="fecha_nacimiento" 
                       id="fecha_nacimiento"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('fecha_nacimiento') border-red-300 @enderror">
                @error('fecha_nacimiento')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Género --}}
            <div>
                <label for="genero" class="block text-sm font-medium text-gray-700 mb-2">
                    Género
                </label>
                <select wire:model="genero" 
                        id="genero"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('genero') border-red-300 @enderror">
                    <option value="">Seleccionar...</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="O">Otro</option>
                </select>
                @error('genero')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Sección de configuración de crédito --}}
        <div class="border-t border-gray-200 pt-6">
            <h4 class="text-md font-medium text-gray-900 mb-4">Configuración de Crédito</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Checkbox Es empresa --}}
                <div class="flex items-center">
                    <input type="checkbox" 
                           wire:model="es_empresa" 
                           id="es_empresa"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="es_empresa" class="ml-2 block text-sm text-gray-700">
                        Es empresa
                    </label>
                </div>

                {{-- Checkbox Permite crédito --}}
                <div class="flex items-center">
                    <input type="checkbox" 
                           wire:model="permite_credito" 
                           id="permite_credito"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="permite_credito" class="ml-2 block text-sm text-gray-700">
                        Permite crédito
                    </label>
                </div>

                {{-- Checkbox Activo --}}
                <div class="flex items-center">
                    <input type="checkbox" 
                           wire:model="activo" 
                           id="activo"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="activo" class="ml-2 block text-sm text-gray-700">
                        Cliente activo
                    </label>
                </div>
            </div>

            @if($permite_credito)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    {{-- Límite de crédito --}}
                    <div>
                        <label for="limite_credito" class="block text-sm font-medium text-gray-700 mb-2">
                            Límite de crédito (S/)
                        </label>
                        <input type="number" 
                               wire:model="limite_credito" 
                               id="limite_credito"
                               min="0"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('limite_credito') border-red-300 @enderror">
                        @error('limite_credito')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descuento porcentaje --}}
                    <div>
                        <label for="descuento_porcentaje" class="block text-sm font-medium text-gray-700 mb-2">
                            Descuento por defecto (%)
                        </label>
                        <input type="number" 
                               wire:model="descuento_porcentaje" 
                               id="descuento_porcentaje"
                               min="0"
                               max="100"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('descuento_porcentaje') border-red-300 @enderror">
                        @error('descuento_porcentaje')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif
        </div>

        {{-- Botones de acción --}}
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <button type="button" 
                    wire:click="$parent.refresh" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancelar
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                {{ $isEditing ? 'Actualizar Cliente' : 'Crear Cliente' }}
            </button>
        </div>
    </form>
</div>
