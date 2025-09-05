<div class="space-y-6">
    {{-- Header con filtros --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    📊 Reportes y Analytics
                </h1>
                <p class="text-gray-600 mt-1">
                    Dashboard ejecutivo y suite de reportes visuales
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Filtros de fecha --}}
                <div class="flex gap-2">
                    <input type="date" 
                           wire:model.live="filtros.fecha_inicio"
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="date" 
                           wire:model.live="filtros.fecha_fin"
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                {{-- Botón de actualizar --}}
                <button wire:click="actualizarFiltros"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path>
                    </svg>
                    <span wire:loading.remove>Actualizar</span>
                    <span wire:loading>Actualizando...</span>
                </button>
                
                {{-- Botón de exportar --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center gap-2">
                        📥 Exportar
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                        <div class="py-1">
                            <button wire:click="exportarDashboard('pdf')"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                📄 Exportar PDF
                            </button>
                            <button wire:click="exportarDashboard('excel')"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                📊 Exportar Excel
                            </button>
                            <button wire:click="exportarDashboard('csv')"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                📋 Exportar CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading.delay class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path>
            </svg>
            <span class="text-blue-800 font-medium">Actualizando reportes...</span>
        </div>
    </div>

    {{-- KPIs Resumen --}}
    @if(!empty($kpisResumen))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($kpisResumen as $kpi => $data)
            @if(is_array($data) && isset($data['valor']))
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ $data['titulo'] ?? ucfirst($kpi) }}</p>
                        <p class="text-2xl font-bold text-gray-900">
                            @if(isset($data['formato']) && $data['formato'] === 'moneda')
                                S/ {{ number_format($data['valor'], 2) }}
                            @else
                                {{ number_format($data['valor']) }}
                            @endif
                        </p>
                        @if(isset($data['cambio']))
                            <div class="flex items-center mt-2">
                                @if($data['cambio'] > 0)
                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-green-600 font-medium">+{{ number_format($data['cambio'], 1) }}%</span>
                                @else
                                    <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-red-600 font-medium">{{ number_format($data['cambio'], 1) }}%</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="text-3xl">
                        {{ $data['icono'] ?? '📊' }}
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Reportes Favoritos --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Reportes Disponibles</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($reportesFavoritos as $reporte)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer"
                 wire:click="seleccionarReporte('{{ $reporte['id'] }}')">
                <div class="flex items-start gap-4">
                    <div class="text-2xl bg-{{ $reporte['color'] }}-100 p-3 rounded-lg">
                        {{ $reporte['icono'] }}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $reporte['titulo'] }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ $reporte['descripcion'] }}</p>
                        <div class="flex items-center justify-between">
                            <span class="inline-block bg-{{ $reporte['color'] }}-100 text-{{ $reporte['color'] }}-800 text-xs px-2 py-1 rounded-full font-medium">
                                {{ ucfirst($reporte['tipo']) }}
                            </span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Alertas --}}
    @if(!empty($alertas))
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">🚨 Alertas del Sistema</h2>
        <div class="space-y-3">
            @foreach($alertas as $alerta)
            <div class="flex items-center p-4 rounded-lg border 
                @if($alerta['tipo'] === 'error') bg-red-50 border-red-200 
                @elseif($alerta['tipo'] === 'warning') bg-yellow-50 border-yellow-200 
                @else bg-blue-50 border-blue-200 @endif">
                <div class="flex-1">
                    <p class="font-medium 
                        @if($alerta['tipo'] === 'error') text-red-900 
                        @elseif($alerta['tipo'] === 'warning') text-yellow-900 
                        @else text-blue-900 @endif">
                        {{ $alerta['mensaje'] }}
                    </p>
                </div>
                @if(isset($alerta['accion']))
                <a href="{{ $alerta['url'] ?? '#' }}" 
                   class="ml-4 px-3 py-1 text-sm font-medium rounded-md 
                    @if($alerta['tipo'] === 'error') bg-red-600 text-white hover:bg-red-700 
                    @elseif($alerta['tipo'] === 'warning') bg-yellow-600 text-white hover:bg-yellow-700 
                    @else bg-blue-600 text-white hover:bg-blue-700 @endif">
                    {{ $alerta['accion'] }}
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Actividad Reciente --}}
    @if(!empty($actividad_reciente))
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">⏰ Actividad Reciente</h2>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="divide-y divide-gray-200">
                @foreach(collect($actividad_reciente)->take(10) as $evento)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $evento['descripcion'] ?? 'Evento del sistema' }}</p>
                                <p class="text-xs text-gray-500">{{ $evento['usuario'] ?? 'Sistema' }} • {{ $evento['tiempo'] ?? 'Hace unos minutos' }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $evento['tipo'] ?? 'general' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Scripts para interactividad --}}
@push('scripts')
<script>
    // Event listeners para widgets interactivos
    document.addEventListener('livewire:initialized', () => {
        // Actualización automática cada 5 minutos
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                Livewire.dispatch('actualizarFiltros');
            }
        }, 300000);
        
        // Escuchar evento de descarga
        Livewire.on('descargar-archivo', (event) => {
            if (event.archivo) {
                const link = document.createElement('a');
                link.href = event.archivo;
                link.download = event.archivo.split('/').pop();
                link.click();
            }
        });
    });
</script>
@endpush
