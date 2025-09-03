@props(['empresa', 'sucursal'])

<div class="relative" x-data="{ open: false }">
    <!-- Botón para abrir selector -->
    <button type="button" 
            @click="open = !open"
            class="flex w-full items-center justify-between rounded-lg bg-white px-3 py-2 text-sm font-medium text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50">
        <div class="flex items-center">
            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                <span class="text-sm font-medium text-blue-600">
                    {{ substr($empresa->nombre, 0, 1) }}
                </span>
            </div>
            <div class="ml-3 text-left">
                <div class="text-sm font-medium text-gray-900">{{ $empresa->nombre }}</div>
                @if($sucursal)
                    <div class="text-xs text-gray-500">{{ $sucursal->nombre }}</div>
                @endif
            </div>
        </div>
        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Dropdown menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
        
        <!-- Empresa actual -->
        <div class="px-4 py-2 border-b border-gray-200">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Empresa</div>
            <div class="mt-1 text-sm font-medium text-gray-900">{{ $empresa->nombre }}</div>
            <div class="text-xs text-gray-500">{{ $empresa->ruc }}</div>
        </div>

        <!-- Sucursales -->
        @if($empresa->sucursales->count() > 0)
            <div class="px-4 py-2">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Sucursales</div>
                @foreach($empresa->sucursales as $suc)
                    <form method="POST" action="{{ route('tenant.switch-sucursal') }}" class="inline-block w-full">
                        @csrf
                        <input type="hidden" name="sucursal_id" value="{{ $suc->id }}">
                        <button type="submit" 
                                class="w-full text-left px-2 py-1 text-sm rounded hover:bg-gray-100 {{ $sucursal && $sucursal->id === $suc->id ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                            <div class="flex items-center">
                                <div class="h-2 w-2 rounded-full {{ $sucursal && $sucursal->id === $suc->id ? 'bg-blue-600' : 'bg-gray-300' }} mr-2"></div>
                                {{ $suc->nombre }}
                            </div>
                            @if($suc->direccion)
                                <div class="text-xs text-gray-500 ml-4">{{ $suc->direccion }}</div>
                            @endif
                        </button>
                    </form>
                @endforeach
            </div>
        @endif

        <!-- Plan actual -->
        <div class="px-4 py-2 border-t border-gray-200">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Plan</div>
            <div class="mt-1 text-sm font-medium text-gray-900">{{ $empresa->plan->nombre }}</div>
            <div class="text-xs text-gray-500">
                Vence: {{ $empresa->fecha_vencimiento_plan->format('d/m/Y') }}
            </div>
        </div>
    </div>
</div>
