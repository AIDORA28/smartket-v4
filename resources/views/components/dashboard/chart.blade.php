@props([
    'id' => 'chart-' . uniqid(),
    'type' => 'line',
    'data' => [],
    'options' => [],
    'height' => '300px',
    'loading' => false
])

@php
    $defaultOptions = [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'top'
            ],
            'tooltip' => [
                'mode' => 'index',
                'intersect' => false,
                'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                'titleColor' => '#fff',
                'bodyColor' => '#fff',
                'borderColor' => 'rgba(255, 255, 255, 0.1)',
                'borderWidth' => 1,
                'cornerRadius' => 8,
                'displayColors' => false
            ]
        ],
        'interaction' => [
            'mode' => 'nearest',
            'axis' => 'x',
            'intersect' => false
        ],
        'scales' => [
            'x' => [
                'display' => true,
                'grid' => [
                    'display' => false
                ]
            ],
            'y' => [
                'display' => true,
                'beginAtZero' => true,
                'grid' => [
                    'color' => 'rgba(0, 0, 0, 0.1)',
                    'drawBorder' => false
                ]
            ]
        ]
    ];
    
    $mergedOptions = array_merge_recursive($defaultOptions, $options);
    $chartId = $id;
@endphp

<div {{ $attributes->merge(['class' => 'chart-container relative']) }} style="height: {{ $height }}">
    @if($loading)
        <div class="absolute inset-0 flex items-center justify-center bg-gray-50 rounded-lg">
            <x-ui.loading size="lg" />
            <span class="ml-3 text-sm text-gray-500">Cargando gráfico...</span>
        </div>
    @else
        <canvas id="{{ $chartId }}" class="w-full h-full"></canvas>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initChart_{{ str_replace(['-', '.'], '_', $chartId) }}();
});

// Escuchar eventos de Livewire para refrescar gráficos
document.addEventListener('livewire:initialized', function() {
    Livewire.on('refreshChart:{{ $chartId }}', function() {
        initChart_{{ str_replace(['-', '.'], '_', $chartId) }}();
    });
});

function initChart_{{ str_replace(['-', '.'], '_', $chartId) }}() {
    const ctx = document.getElementById('{{ $chartId }}');
    if (!ctx) return;
    
    // Destruir gráfico existente si existe
    if (window.chart_{{ str_replace(['-', '.'], '_', $chartId) }} instanceof Chart) {
        window.chart_{{ str_replace(['-', '.'], '_', $chartId) }}.destroy();
    }
    
    const data = @json($data);
    const options = @json($mergedOptions);
    
    window.chart_{{ str_replace(['-', '.'], '_', $chartId) }} = new Chart(ctx, {
        type: '{{ $type }}',
        data: data,
        options: options
    });
}
</script>
