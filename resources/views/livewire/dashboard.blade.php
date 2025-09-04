<div class="min-h-screen bg-gray-50">>
    {{-- Header del Dashboard --}}
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <span class="text-2xl">📊</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
                        @if($empresa)
                            <p class="text-sm text-gray-500">{{ $empresa->nombre }} • {{ now()->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    {{-- Selector de fechas --}}
                    <div class="hidden sm:flex items-center space-x-2">
                        <input type="date" wire:model="fechaInicio" wire:change="filtrarPorFechas" 
                               class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-gray-500">-</span>
                        <input type="date" wire:model="fechaFin" wire:change="filtrarPorFechas"
                               class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    {{-- Botón refrescar --}}
                    <button wire:click="refrescarDatos" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2 {{ $loading ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Actualizar
                    </button>
                    
                    {{-- Botón POS --}}
                    <a href="{{ route('pos.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="mr-2">🛒</span>
                        Nueva Venta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Alertas --}}
        @if(count($alertas) > 0)
            <div class="mb-6 space-y-3">
                @foreach($alertas as $alerta)
                    <div class="rounded-lg p-4 {{ $alerta['tipo'] === 'error' ? 'bg-red-50 border border-red-200' : ($alerta['tipo'] === 'warning' ? 'bg-yellow-50 border border-yellow-200' : 'bg-blue-50 border border-blue-200') }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($alerta['tipo'] === 'error')
                                        <span class="text-red-500 text-xl">⚠️</span>
                                    @elseif($alerta['tipo'] === 'warning')
                                        <span class="text-yellow-500 text-xl">⚠️</span>
                                    @else
                                        <span class="text-blue-500 text-xl">ℹ️</span>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium {{ $alerta['tipo'] === 'error' ? 'text-red-800' : ($alerta['tipo'] === 'warning' ? 'text-yellow-800' : 'text-blue-800') }}">
                                        {{ $alerta['mensaje'] }}
                                    </p>
                                </div>
                            </div>
                            @if(isset($alerta['accion']) && isset($alerta['url']))
                                <a href="{{ $alerta['url'] }}" class="text-sm font-medium {{ $alerta['tipo'] === 'error' ? 'text-red-600 hover:text-red-500' : ($alerta['tipo'] === 'warning' ? 'text-yellow-600 hover:text-yellow-500' : 'text-blue-600 hover:text-blue-500') }}">
                                    {{ $alerta['accion'] }} →
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- KPIs Grid --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-8 mb-6">
            @foreach($kpis as $key => $kpi)
                <div class="bg-white rounded-lg shadow-sm border p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">{{ $kpi['icono'] }}</span>
                        </div>
                        <div class="ml-3 min-w-0 flex-1">
                            <div class="text-lg font-bold text-gray-900 truncate">
                                @if(isset($kpi['monto']))
                                    S/ {{ number_format($kpi['monto'], 2) }}
                                @else
                                    {{ number_format($kpi['cantidad']) }}
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 truncate">
                                {{ ucwords(str_replace('_', ' ', $key)) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Grid principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Gráfico de ventas de la semana --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ventas de los últimos 7 días</h3>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                📈 Tendencia
                            </span>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="ventasSemanaChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- Productos top --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Productos</h3>
                    <div class="space-y-3">
                        @forelse($productosTop as $index => $producto)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-xs font-medium text-blue-800">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-32">
                                            {{ $producto->nombre }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $producto->cantidad_vendida }} unidades
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    S/ {{ number_format($producto->total_vendido, 2) }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <span class="text-4xl">📦</span>
                                <p class="mt-2 text-sm text-gray-500">No hay ventas este mes</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Ventas por hora --}}
            <div class="lg:col-span-6">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas por Hora (Hoy)</h3>
                    <div class="h-48">
                        <canvas id="ventasHoraChart" width="300" height="150"></canvas>
                    </div>
                </div>
            </div>

            {{-- Ventas recientes --}}
            <div class="lg:col-span-6">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ventas Recientes</h3>
                        <a href="{{ route('ventas.index') }}" class="text-sm text-blue-600 hover:text-blue-500">Ver todas →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($ventasRecientes as $venta)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $venta['cliente'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $venta['numero'] }} • {{ $venta['fecha'] }}
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    S/ {{ number_format($venta['total'], 2) }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <span class="text-4xl">🛒</span>
                                <p class="mt-2 text-sm text-gray-500">No hay ventas registradas</p>
                                <a href="{{ route('pos.index') }}" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                                    Realizar primera venta
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Stock bajo --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Stock Bajo</h3>
                        <a href="{{ route('productos.index') }}" class="text-sm text-blue-600 hover:text-blue-500">Ver productos →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($productosStockBajo as $producto)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 truncate max-w-32">
                                        {{ $producto['nombre'] }}
                                    </div>
                                    <div class="text-xs text-red-600">
                                        Stock: {{ $producto['stock_actual'] }} (min: {{ $producto['stock_minimo'] }})
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-red-600">
                                    -{{ $producto['diferencia'] }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <span class="text-4xl">✅</span>
                                <p class="mt-2 text-sm text-gray-500">Stock en niveles óptimos</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Lotes próximos a vencer --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Lotes Próximos a Vencer</h3>
                        <a href="{{ route('lotes.index') }}" class="text-sm text-blue-600 hover:text-blue-500">Ver lotes →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($lotesVencimiento as $lote)
                            <div class="flex items-center justify-between p-3 {{ $lote['urgente'] ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }} rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 truncate max-w-32">
                                        {{ $lote['producto'] }}
                                    </div>
                                    <div class="text-xs {{ $lote['urgente'] ? 'text-red-600' : 'text-yellow-600' }}">
                                        {{ $lote['codigo'] }} • {{ $lote['cantidad'] }} unidades
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium {{ $lote['urgente'] ? 'text-red-600' : 'text-yellow-600' }}">
                                        {{ $lote['vencimiento'] }}
                                    </div>
                                    <div class="text-xs {{ $lote['urgente'] ? 'text-red-500' : 'text-yellow-500' }}">
                                        {{ $lote['dias_restantes'] }} días
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <span class="text-4xl">✅</span>
                                <p class="mt-2 text-sm text-gray-500">No hay lotes próximos a vencer</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Clientes top --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mejores Clientes</h3>
                        <a href="{{ route('clientes.index') }}" class="text-sm text-blue-600 hover:text-blue-500">Ver clientes →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($clientesTop as $cliente)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 truncate max-w-32">
                                        {{ $cliente['nombre'] }}
                                    </div>
                                    <div class="text-xs text-green-600">
                                        {{ $cliente['ventas_count'] }} compras este mes
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-green-600">
                                    S/ {{ number_format($cliente['ventas_total'], 2) }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <span class="text-4xl">👥</span>
                                <p class="mt-2 text-sm text-gray-500">No hay compras este mes</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts para gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
        });

        // Escuchar eventos de Livewire para refrescar gráficos
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('refreshCharts', function() {
                initCharts();
            });
        });

        function initCharts() {
            // Gráfico de ventas de la semana
            const ventasSemanaCtx = document.getElementById('ventasSemanaChart');
            if (ventasSemanaCtx) {
                // Destruir gráfico existente si existe
                if (window.ventasSemanaChart instanceof Chart) {
                    window.ventasSemanaChart.destroy();
                }

                const ventasSemanaData = @json($ventasSemana);
                
                window.ventasSemanaChart = new Chart(ventasSemanaCtx, {
                    type: 'line',
                    data: {
                        labels: ventasSemanaData.map(item => item.fecha),
                        datasets: [{
                            label: 'Cantidad de Ventas',
                            data: ventasSemanaData.map(item => item.cantidad),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Monto (S/)',
                            data: ventasSemanaData.map(item => item.monto),
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false,
                                },
                            }
                        }
                    }
                });
            }

            // Gráfico de ventas por hora
            const ventasHoraCtx = document.getElementById('ventasHoraChart');
            if (ventasHoraCtx) {
                // Destruir gráfico existente si existe
                if (window.ventasHoraChart instanceof Chart) {
                    window.ventasHoraChart.destroy();
                }

                const ventasHoraData = @json($ventasPorHora);
                
                window.ventasHoraChart = new Chart(ventasHoraCtx, {
                    type: 'bar',
                    data: {
                        labels: ventasHoraData.map(item => item.hora),
                        datasets: [{
                            label: 'Ventas por Hora',
                            data: ventasHoraData.map(item => item.ventas),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        }
    </script>
</div>
</div>
