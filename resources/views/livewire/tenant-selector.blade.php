<div class="relative" x-data="{ open: false }">
    @if($empresaActual)
        <button @click="open = !open" 
                class="flex items-center space-x-2 px-3 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex items-center">
                <div class="h-6 w-6 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-xs font-medium text-blue-600">
                        {{ substr($empresaActual->nombre, 0, 1) }}
                    </span>
                </div>
                <div class="ml-2">
                    <div class="text-sm font-medium text-gray-900">{{ $empresaActual->nombre }}</div>
                    <div class="text-xs text-gray-500">{{ $empresaActual->plan->nombre ?? 'Sin Plan' }}</div>
                </div>
            </div>
            <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
            
            <div class="px-4 py-2 border-b border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cambiar Empresa</p>
            </div>
            
            @foreach($empresas as $empresa)
                <button wire:click="cambiarEmpresa({{ $empresa->id }})" 
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 transition duration-150 ease-in-out {{ $empresa->id === $empresaActual->id ? 'bg-blue-50' : '' }}">
                    <div class="flex items-center">
                        <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-600">
                                {{ substr($empresa->nombre, 0, 1) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">{{ $empresa->nombre }}</div>
                            <div class="text-xs text-gray-500">{{ $empresa->plan->nombre ?? 'Sin Plan' }}</div>
                        </div>
                        @if($empresa->id === $empresaActual->id)
                            <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>
    @else
        <div class="px-3 py-2 text-sm text-gray-500">
            Sin empresa seleccionada
        </div>
    @endif
</div>
