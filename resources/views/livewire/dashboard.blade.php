<div class="min-h-screen bg-gray-50">
    {{-- Header del Dashboard --}}
    <x-dashboard.header 
        title="Dashboard"
        :subtitle="$empresa ? $empresa->nombre . ' • ' . now()->format('d/m/Y H:i') : null">
        
        <x-slot name="actions">
            {{-- Filtro de fechas --}}
            <x-dashboard.date-filter 
                wire:model="dateRange"
                class="w-full sm:w-auto" />
            
            {{-- Botón refrescar --}}
            <x-ui.button 
                variant="secondary" 
                size="sm"
                wire:click="refrescarDatos"
                :loading="$loading"
                class="w-full sm:w-auto justify-center">
                <x-slot name="icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </x-slot>
                Actualizar
            </x-ui.button>
            
            {{-- Botón POS --}}
            <x-ui.button 
                variant="primary" 
                href="{{ route('pos.index') }}"
                size="sm"
                class="w-full sm:w-auto justify-center">
                <x-slot name="icon">🛒</x-slot>
                <span class="hidden sm:inline">Nueva Venta</span>
                <span class="sm:hidden">Ir al POS</span>
            </x-ui.button>
        </x-slot>
    </x-dashboard.header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Alertas --}}
        @if(count($alertas) > 0)
            <div class="mb-6 space-y-3">
                @foreach($alertas as $alerta)
                    <x-ui.alert 
                        type="{{ $alerta['tipo'] }}" 
                        :dismissible="true"
                        class="alert-enter">
                        @if(isset($alerta['titulo']))
                            <x-slot name="title">{{ $alerta['titulo'] }}</x-slot>
                        @endif
                        
                        {{ $alerta['mensaje'] }}
                        
                        @if(isset($alerta['accion']) && isset($alerta['url']))
                            <div class="mt-2">
                                <x-ui.button 
                                    variant="secondary" 
                                    size="sm" 
                                    href="{{ $alerta['url'] }}">
                                    {{ $alerta['accion'] }}
                                </x-ui.button>
                            </div>
                        @endif
                    </x-ui.alert>
                @endforeach
            </div>
        @endif

        {{-- KPIs Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @foreach($kpis as $key => $kpi)
                <x-dashboard.kpi-card 
                    :title="ucwords(str_replace('_', ' ', $key))"
                    :value="$kpi['monto'] ?? $kpi['cantidad']"
                    :format="isset($kpi['monto']) ? 'currency' : 'number'"
                    :icon="$kpi['icono']"
                    :color="$this->getKpiColor($key)"
                    :href="$this->getKpiUrl($key)"
                    class="transform transition-transform hover:scale-105" />
            @endforeach
        </div>

        {{-- Grid principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6">
            {{-- Gráfico de ventas de la semana --}}
            <div class="lg:col-span-8 order-1">
                <x-dashboard.widget-card 
                    title="Ventas de los últimos 7 días"
                    subtitle="Seguimiento de ventas diarias">
                    <x-slot name="action">
                        <x-ui.badge variant="green" size="sm">
                            📈 Tendencia
                        </x-ui.badge>
                    </x-slot>
                    
                    <x-dashboard.chart 
                        id="ventasSemanaChart"
                        type="line"
                        :data="[
                            'labels' => collect($ventasSemana)->pluck('fecha')->toArray(),
                            'datasets' => [
                                [
                                    'label' => 'Cantidad de Ventas',
                                    'data' => collect($ventasSemana)->pluck('cantidad')->toArray(),
                                    'borderColor' => 'rgb(59, 130, 246)',
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                                    'tension' => 0.4,
                                    'fill' => true,
                                    'yAxisID' => 'y'
                                ],
                                [
                                    'label' => 'Monto (S/)',
                                    'data' => collect($ventasSemana)->pluck('monto')->toArray(),
                                    'borderColor' => 'rgb(16, 185, 129)',
                                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                                    'tension' => 0.4,
                                    'yAxisID' => 'y1'
                                ]
                            ]
                        ]"
                        :options="[
                            'scales' => [
                                'y' => [
                                    'type' => 'linear',
                                    'display' => true,
                                    'position' => 'left',
                                    'title' => ['display' => true, 'text' => 'Cantidad']
                                ],
                                'y1' => [
                                    'type' => 'linear',
                                    'display' => true,
                                    'position' => 'right',
                                    'title' => ['display' => true, 'text' => 'Monto (S/)'],
                                    'grid' => ['drawOnChartArea' => false]
                                ]
                            ]
                        ]"
                        :loading="$loading"
                        height="280px" />
                </x-dashboard.widget-card>
            </div>

            {{-- Productos top --}}
            <div class="lg:col-span-4 order-2 lg:order-2">
                <x-dashboard.widget-card 
                    title="Top Productos" 
                    subtitle="Más vendidos este mes"
                    action-url="{{ route('productos.index') }}"
                    action-text="Ver productos"
                    :loading="$loading">
                    
                    @if(count($productosTop) > 0)
                        <div class="space-y-2 list-stagger">
                            @foreach($productosTop as $index => $producto)
                                <x-dashboard.list-item 
                                    :title="$producto->nombre"
                                    :subtitle="$producto->cantidad_vendida . ' unidades'"
                                    :value="'S/ ' . number_format($producto->total_vendido, 2)"
                                    :ranking="$index + 1"
                                    href="{{ route('productos.editar', $producto->id) }}" />
                            @endforeach
                        </div>
                    @else
                        <x-dashboard.empty-state 
                            icon="📦"
                            title="Sin ventas"
                            message="No hay productos vendidos este mes"
                            action-text="Ver productos"
                            action-url="{{ route('productos.index') }}" />
                    @endif
                </x-dashboard.widget-card>
            </div>

            {{-- Ventas por hora - Móvil primero --}}
            <div class="lg:col-span-6 order-3">
                <x-dashboard.widget-card 
                    title="Ventas por Hora" 
                    subtitle="Actividad de hoy">
                    
                    <x-dashboard.chart 
                        id="ventasHoraChart"
                        type="bar"
                        :data="[
                            'labels' => collect($ventasPorHora)->pluck('hora')->toArray(),
                            'datasets' => [
                                [
                                    'label' => 'Ventas por Hora',
                                    'data' => collect($ventasPorHora)->pluck('ventas')->toArray(),
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                                    'borderColor' => 'rgb(59, 130, 246)',
                                    'borderWidth' => 1,
                                    'borderRadius' => 4
                                ]
                            ]
                        ]"
                        :options="[
                            'plugins' => ['legend' => ['display' => false]],
                            'scales' => [
                                'y' => [
                                    'beginAtZero' => true,
                                    'ticks' => ['stepSize' => 1],
                                    'title' => ['display' => true, 'text' => 'Número de Ventas']
                                ],
                                'x' => [
                                    'title' => ['display' => true, 'text' => 'Hora del día']
                                ]
                            ]
                        ]"
                        :loading="$loading"
                        height="220px" />
                </x-dashboard.widget-card>
            </div>

            {{-- Ventas recientes --}}
            <div class="lg:col-span-6 order-4">
                <x-dashboard.widget-card 
                    title="Ventas Recientes"
                    action-url="{{ route('ventas.index') }}"
                    :loading="$loading">
                    
                    @if(count($ventasRecientes) > 0)
                        <div class="space-y-2 list-stagger">
                            @foreach($ventasRecientes as $venta)
                                <x-dashboard.list-item 
                                    :title="$venta['cliente']"
                                    :subtitle="$venta['numero'] . ' • ' . $venta['fecha']"
                                    :value="'S/ ' . number_format($venta['total'], 2)"
                                    href="{{ route('ventas.show', $venta['id']) }}" />
                            @endforeach
                        </div>
                    @else
                        <x-dashboard.empty-state 
                            icon="🛒"
                            title="Sin ventas"
                            message="No hay ventas registradas"
                            action-text="Realizar primera venta"
                            action-url="{{ route('pos.index') }}" />
                    @endif
                </x-dashboard.widget-card>
            </div>

            {{-- Stock bajo --}}
            <div class="lg:col-span-4">
                <x-dashboard.widget-card 
                    title="Stock Bajo"
                    subtitle="Productos que requieren reposición"
                    action-url="{{ route('productos.index') }}"
                    action-text="Ver productos"
                    :loading="$loading">
                    
                    @if(count($productosStockBajo) > 0)
                        <div class="space-y-3">
                            @foreach($productosStockBajo as $producto)
                                <x-dashboard.list-item 
                                    :title="$producto['nombre']"
                                    :subtitle="'Stock: ' . $producto['stock_actual'] . ' (min: ' . $producto['stock_minimo'] . ')'"
                                    :value="'-' . $producto['diferencia']"
                                    badge="Bajo"
                                    badge-color="red"
                                    class="bg-red-50 border border-red-200"
                                    href="{{ route('productos.editar', $producto['id']) }}" />
                            @endforeach
                        </div>
                    @else
                        <x-dashboard.empty-state 
                            icon="✅"
                            title="Stock óptimo"
                            message="Todos los productos tienen stock en niveles óptimos" />
                    @endif
                </x-dashboard.widget-card>
            </div>

            {{-- Lotes próximos a vencer --}}
            <div class="lg:col-span-4">
                <x-dashboard.widget-card 
                    title="Lotes Próximos a Vencer"
                    subtitle="Control de vencimientos"
                    action-url="{{ route('lotes.index') }}"
                    action-text="Ver lotes"
                    :loading="$loading">
                    
                    @if(count($lotesVencimiento) > 0)
                        <div class="space-y-3">
                            @foreach($lotesVencimiento as $lote)
                                <x-dashboard.list-item 
                                    :title="$lote['producto']"
                                    :subtitle="$lote['codigo'] . ' • ' . $lote['cantidad'] . ' unidades'"
                                    :value="$lote['dias_restantes'] . ' días'"
                                    :badge="$lote['urgente'] ? 'Urgente' : 'Próximo'"
                                    :badge-color="$lote['urgente'] ? 'red' : 'yellow'"
                                    :class="$lote['urgente'] ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200'"
                                    href="{{ route('lotes.index') }}" />
                            @endforeach
                        </div>
                    @else
                        <x-dashboard.empty-state 
                            icon="✅"
                            title="Sin vencimientos próximos"
                            message="No hay lotes próximos a vencer" />
                    @endif
                </x-dashboard.widget-card>
            </div>

            {{-- Clientes top --}}
            <div class="lg:col-span-4">
                <x-dashboard.widget-card 
                    title="Mejores Clientes"
                    subtitle="Top compradores del mes"
                    action-url="{{ route('clientes.index') }}"
                    action-text="Ver clientes"
                    :loading="$loading">
                    
                    @if(count($clientesTop) > 0)
                        <div class="space-y-3">
                            @foreach($clientesTop as $cliente)
                                <x-dashboard.list-item 
                                    :title="$cliente['nombre']"
                                    :subtitle="$cliente['ventas_count'] . ' compras este mes'"
                                    :value="'S/ ' . number_format($cliente['ventas_total'], 2)"
                                    badge="VIP"
                                    badge-color="green"
                                    class="bg-green-50 border border-green-200"
                                    href="{{ route('clientes.index') }}" />
                            @endforeach
                        </div>
                    @else
                        <x-dashboard.empty-state 
                            icon="👥"
                            title="Sin compras"
                            message="No hay compras registradas este mes" />
                    @endif
                </x-dashboard.widget-card>
            </div>
        </div>
    </div>

    {{-- Scripts para gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</div>
