<div class="relative" x-data="{ open: false }">
    @if($empresaActual)
        <button @click="open = !open; $wire.set('showDropdown', !open)" 
                class="flex items-center space-x-3 px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm w-full lg:w-auto">
            <!-- Company Avatar -->
            <div class="flex-shrink-0">
                <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span class="text-sm font-bold text-white">
                        {{ substr($empresaActual->nombre, 0, 1) }}
                    </span>
                </div>
            </div>
            
            <!-- Company Info -->
            <div class="flex-1 min-w-0 text-left">
                <div class="text-sm font-semibold text-gray-900 truncate">
                    {{ $empresaActual->nombre }}
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500">
                        {{ $empresaActual->plan->nombre ?? 'Sin Plan' }}
                    </span>
                    @if($sucursalActual)
                        <span class="text-xs text-gray-400">•</span>
                        <span class="text-xs text-gray-500">{{ $sucursalActual->nombre }}</span>
                    @endif
                </div>
            </div>
            
            <!-- Chevron -->
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-gray-400 transition-transform" 
                     :class="open ? 'rotate-180' : ''" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             @click.away="open = false; $wire.set('showDropdown', false)"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50 ring-1 ring-black ring-opacity-5">
            
            <!-- Current Context -->
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-sm font-bold text-white">
                            {{ substr($empresaActual->nombre, 0, 1) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-900">{{ $empresaActual->nombre }}</div>
                        <div class="text-xs text-gray-500">
                            Plan {{ $empresaActual->plan->nombre ?? 'Sin Plan' }}
                            @if($sucursalActual)
                                • {{ $sucursalActual->nombre }}
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <x-ui.badge variant="green" size="sm">Activa</x-ui.badge>
                    </div>
                </div>
            </div>
            
            <!-- Companies Section -->
            @if(count($empresas) > 1)
                <div class="px-4 py-2">
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                        Cambiar Empresa
                    </h4>
                </div>
                
                <div class="max-h-48 overflow-y-auto">
                    @foreach($empresas as $empresa)
                        @if($empresa->id !== $empresaActual->id)
                            <button wire:click="cambiarEmpresa({{ $empresa->id }})" 
                                    class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors flex items-center space-x-3">
                                <div class="h-8 w-8 bg-gradient-to-r from-gray-400 to-gray-600 rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">
                                        {{ substr($empresa->nombre, 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $empresa->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $empresa->plan->nombre ?? 'Sin Plan' }}</div>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </button>
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- Sucursales Section -->
            @if($sucursales && count($sucursales) > 1)
                <div class="border-t border-gray-100 mt-2">
                    <div class="px-4 py-2">
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Cambiar Sucursal
                        </h4>
                    </div>
                    
                    <div class="max-h-32 overflow-y-auto">
                        @foreach($sucursales as $sucursal)
                            <button wire:click="cambiarSucursal({{ $sucursal->id }})" 
                                    class="w-full text-left px-4 py-2 hover:bg-gray-50 transition-colors flex items-center justify-between {{ $sucursalActual && $sucursal->id === $sucursalActual->id ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                                <div class="flex items-center space-x-2">
                                    <svg class="h-4 w-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-sm">{{ $sucursal->nombre }}</span>
                                </div>
                                @if($sucursalActual && $sucursal->id === $sucursalActual->id)
                                    <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
            @if($empresaActual)
                <div class="border-t border-gray-100 mt-2 px-4 py-3 bg-gray-50">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-lg font-semibold text-gray-900">{{ $empresaActual->plan->max_productos ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">Productos Max</div>
                        </div>
                        <div>
                            <div class="text-lg font-semibold text-gray-900">{{ $empresaActual->plan->max_usuarios ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">Usuarios Max</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- No Company Selected -->
        <div class="px-4 py-3 text-center bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center justify-center space-x-2">
                <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-yellow-800">Sin empresa seleccionada</span>
            </div>
            <p class="text-xs text-yellow-600 mt-1">Contacta al administrador</p>
        </div>
    @endif
</div>
