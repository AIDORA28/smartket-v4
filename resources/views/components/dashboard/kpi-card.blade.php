@props([
    'title' => '',
    'value' => 0,
    'icon' => null,
    'trend' => null,
    'trendType' => 'neutral', // 'up', 'down', 'neutral'
    'color' => 'blue',
    'href' => null,
    'format' => 'number' // 'number', 'currency', 'percentage'
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-50 text-blue-600',
        'green' => 'bg-green-50 text-green-600', 
        'red' => 'bg-red-50 text-red-600',
        'yellow' => 'bg-yellow-50 text-yellow-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'indigo' => 'bg-indigo-50 text-indigo-600',
    ];
    
    $trendClasses = [
        'up' => 'text-green-600',
        'down' => 'text-red-600', 
        'neutral' => 'text-gray-500'
    ];
    
    $trendIcons = [
        'up' => '<path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />',
        'down' => '<path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />',
        'neutral' => '<path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />'
    ];
    
    $formattedValue = match($format) {
        'currency' => 'S/ ' . number_format($value, 2),
        'percentage' => number_format($value, 1) . '%',
        default => number_format($value)
    };
    
    $component = $href ? 'a' : 'div';
    $componentAttributes = $href ? ['href' => $href] : [];
@endphp

<{{ $component }} 
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'kpi-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200 ' . ($href ? 'hover:border-gray-300' : '')]) }}>
    
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-3">
                @if($icon)
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-lg {{ $colorClasses[$color] }} flex items-center justify-center">
                            <div class="text-xl">
                                {!! $icon !!}
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline space-x-2">
                        <p class="text-2xl font-bold text-gray-900 truncate">
                            {{ $formattedValue }}
                        </p>
                        @if($trend !== null)
                            <div class="flex items-center {{ $trendClasses[$trendType] }}">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    {!! $trendIcons[$trendType] !!}
                                </svg>
                                <span class="text-sm font-medium">{{ $trend }}%</span>
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 truncate mt-1">
                        {{ $title }}
                    </p>
                </div>
            </div>
        </div>
        
        @if($href)
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        @endif
    </div>
</{{ $component }}>
