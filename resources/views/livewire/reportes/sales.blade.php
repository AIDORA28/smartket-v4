<div class="space-y-6">
    {{-- Header con navegación --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('reportes.index') }}" 
                   class="text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        📈 Reportes de Ventas
                    </h1>
                    <p class="text-gray-600 mt-1">Análisis detallado de ventas y tendencias</p>
                </div>
            </div>
            
            {{-- Botones de acción --}}
            <div class="flex gap-3">
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
                    Actualizar
                </button>
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center gap-2">
                        📊 Exportar
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                        <div class="py-1">
                            <button wire:click="exportarReporte('csv')"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                📋 Exportar CSV
                            </button>
                            <button wire:click="exportarReporte('pdf')"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                📄 Exportar PDF
                            </button>
                            <button wire:click="exportarReporte('excel')"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                📊 Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros avanzados --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" 
                       wire:model.live="filtros.fecha_inicio"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" 
                       wire:model.live="filtros.fecha_fin"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select wire:model.live="filtros.estado" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="todos">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="pagada">Pagada</option>
                    <option value="anulada">Anulada</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agrupación</label>
                <select wire:model.live="filtros.agrupacion" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="dia">Por Día</option>
                    <option value="semana">Por Semana</option>
                    <option value="mes">Por Mes</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gráfico</label>
                <select wire:model.live="filtros.tipo_grafico" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="linea">Líneas</option>
                    <option value="barras">Barras</option>
                    <option value="dona">Dona</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button wire:click="actualizarFiltros"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                    🔍 Filtrar
                </button>
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
            <span class="text-blue-800 font-medium">Procesando datos de ventas...</span>
        </div>
    </div>

    {{-- KPIs de Ventas --}}
    @if(!empty($metricas) && !isset($metricas['error']))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @if(isset($metricas['ventas_hoy']))
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ventas de Hoy</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $metricas['ventas_hoy']['cantidad'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">S/ {{ number_format($metricas['ventas_hoy']['monto'] ?? 0, 2) }}</p>
                </div>
                <div class="text-3xl">📊</div>
            </div>
        </div>
        @endif

        @if(isset($metricas['ventas_mes']))
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ventas del Mes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $metricas['ventas_mes']['cantidad'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">S/ {{ number_format($metricas['ventas_mes']['monto'] ?? 0, 2) }}</p>
                </div>
                <div class="text-3xl">📈</div>
            </div>
        </div>
        @endif

        @if(isset($metricas['comparacion_anterior']))
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Crecimiento</p>
                    <p class="text-2xl font-bold {{ ($metricas['comparacion_anterior']['crecimiento'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($metricas['comparacion_anterior']['crecimiento'] ?? 0, 1) }}%
                    </p>
                    <p class="text-sm text-gray-500">vs período anterior</p>
                </div>
                <div class="text-3xl">{{ ($metricas['comparacion_anterior']['crecimiento'] ?? 0) >= 0 ? '⬆️' : '⬇️' }}</div>
            </div>
        </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Promedio Venta</p>
                    <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($metricas['promedio_venta'] ?? 0, 2) }}</p>
                    <p class="text-sm text-gray-500">por transacción</p>
                </div>
                <div class="text-3xl">🎯</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Gráfico de Ventas por Período --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Ventas por {{ ucfirst($filtros['agrupacion']) }}</h3>
            @if(!empty($chartData['ventas_periodo']['labels']))
            <div class="h-80" wire:ignore>
                <canvas id="ventasPeriodoChart"></canvas>
            </div>
            @else
            <div class="h-80 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <div class="text-4xl mb-2">📊</div>
                    <p class="text-gray-600">No hay datos para mostrar</p>
                    <p class="text-sm text-gray-500">Ajusta los filtros de fecha</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Gráfico de Top Productos --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🏆 Top Productos Vendidos</h3>
            @if(!empty($chartData['top_productos']['labels']))
            <div class="h-80" wire:ignore>
                <canvas id="topProductosChart"></canvas>
            </div>
            @else
            <div class="h-80 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <div class="text-4xl mb-2">🏆</div>
                    <p class="text-gray-600">No hay productos para mostrar</p>
                    <p class="text-sm text-gray-500">Revisa el período seleccionado</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Tablas de datos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Productos (Tabla) --}}
        @if($topProductos && $topProductos->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">🏆 Productos Más Vendidos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Producto</th>
                            <th class="px-6 py-3">Cantidad</th>
                            <th class="px-6 py-3">Ventas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topProductos->take(10) as $producto)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $producto['nombre'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ number_format($producto['cantidad_vendida'] ?? 0) }}</td>
                            <td class="px-6 py-4">{{ number_format($producto['total_ventas'] ?? 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Top Clientes (Tabla) --}}
        @if($ventasPorCliente && $ventasPorCliente->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">👥 Clientes Más Frecuentes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-900">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Cliente</th>
                            <th class="px-6 py-3">Compras</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($ventasPorCliente->take(10) as $cliente)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $cliente['nombre'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ number_format($cliente['total_compras'] ?? 0) }}</td>
                            <td class="px-6 py-4">S/ {{ number_format($cliente['monto_total'] ?? 0, 2) }}</td>
                            <td class="px-6 py-4">S/ {{ number_format($cliente['promedio_compra'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Scripts para gráficos --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let ventasPeriodoChart, topProductosChart;
        
        function initCharts() {
            // Gráfico de ventas por período
            const ventasData = @json($chartData['ventas_periodo'] ?? []);
            if (ventasData && ventasData.labels && ventasData.labels.length > 0) {
                const ctx1 = document.getElementById('ventasPeriodoChart');
                if (ctx1) {
                    if (ventasPeriodoChart) {
                        ventasPeriodoChart.destroy();
                    }
                    ventasPeriodoChart = new Chart(ctx1, {
                        type: '{{ $filtros["tipo_grafico"] === "barras" ? "bar" : "line" }}',
                        data: ventasData,
                        options: @json($chartOptions)
                    });
                }
            }
            
            // Gráfico de top productos
            const productosData = @json($chartData['top_productos'] ?? []);
            if (productosData && productosData.labels && productosData.labels.length > 0) {
                const ctx2 = document.getElementById('topProductosChart');
                if (ctx2) {
                    if (topProductosChart) {
                        topProductosChart.destroy();
                    }
                    topProductosChart = new Chart(ctx2, {
                        type: '{{ $filtros["tipo_grafico"] === "dona" ? "doughnut" : "bar" }}',
                        data: productosData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 10
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }
        
        // Inicializar gráficos cuando se cargue la página
        setTimeout(initCharts, 100);
        
        // Actualizar gráficos cuando cambien los filtros
        Livewire.on('filtros-actualizados', initCharts);
    });
</script>
@endpush
