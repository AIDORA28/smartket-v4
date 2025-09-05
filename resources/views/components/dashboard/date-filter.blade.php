@props([
    'startDate' => null,
    'endDate' => null,
    'wireModel' => 'dateRange',
    'presets' => true,
    'label' => 'Filtrar por fecha'
])

<div {{ $attributes->merge(['class' => 'flex items-center space-x-3']) }} 
     x-data="{ 
        showPresets: false,
        applyPreset(days) {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - days);
            
            this.$refs.startDate.value = start.toISOString().split('T')[0];
            this.$refs.endDate.value = end.toISOString().split('T')[0];
            
            // Trigger Livewire update
            this.$refs.startDate.dispatchEvent(new Event('input'));
            this.$refs.endDate.dispatchEvent(new Event('input'));
            
            this.showPresets = false;
        }
     }">
    
    @if($presets)
        <!-- Presets Dropdown -->
        <div class="relative">
            <x-ui.button 
                variant="secondary" 
                size="sm"
                @click="showPresets = !showPresets"
                class="hidden sm:inline-flex">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Períodos
            </x-ui.button>
            
            <!-- Dropdown Menu -->
            <div x-show="showPresets" 
                 @click.away="showPresets = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                
                <div class="py-1">
                    <button @click="applyPreset(0)" 
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Hoy
                    </button>
                    <button @click="applyPreset(6)" 
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Últimos 7 días
                    </button>
                    <button @click="applyPreset(29)" 
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Últimos 30 días
                    </button>
                    <button @click="applyPreset(89)" 
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Últimos 90 días
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Date Inputs -->
    <div class="flex items-center space-x-2">
        <x-ui.input 
            type="date" 
            x-ref="startDate"
            wire:model="{{ $wireModel }}.start"
            class="text-sm"
            placeholder="Fecha inicio" />
        
        <span class="text-gray-400 text-sm">-</span>
        
        <x-ui.input 
            type="date" 
            x-ref="endDate"
            wire:model="{{ $wireModel }}.end"
            class="text-sm"
            placeholder="Fecha fin" />
    </div>
</div>
