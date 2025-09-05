@props([
    'title' => '',
    'subtitle' => null,
    'value' => null,
    'badge' => null,
    'badgeColor' => 'blue',
    'href' => null,
    'avatar' => null,
    'ranking' => null,
    'trend' => null,
    'trendType' => 'neutral'
])

@php
    $component = $href ? 'a' : 'div';
    $baseClasses = 'list-item flex items-center justify-between p-3 rounded-lg transition-colors duration-150';
    $interactiveClasses = $href ? 'hover:bg-gray-100 cursor-pointer focus-ring' : '';
    
    $trendClasses = [
        'up' => 'text-green-600',
        'down' => 'text-red-600',
        'neutral' => 'text-gray-500'
    ];
@endphp

<{{ $component }} 
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => $baseClasses . ' ' . $interactiveClasses]) }}>
    
    <div class="flex items-center min-w-0 flex-1">
        @if($ranking)
            <div class="flex-shrink-0 mr-3">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-xs font-medium text-blue-800">
                    {{ $ranking }}
                </span>
            </div>
        @endif
        
        @if($avatar)
            <div class="flex-shrink-0 mr-3">
                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                    @if(is_string($avatar))
                        <span class="text-xs font-medium text-gray-700">{{ $avatar }}</span>
                    @else
                        {{ $avatar }}
                    @endif
                </div>
            </div>
        @endif
        
        <div class="min-w-0 flex-1">
            <div class="flex items-center space-x-2">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $title }}</p>
                @if($badge)
                    <x-ui.badge variant="{{ $badgeColor }}" size="sm">{{ $badge }}</x-ui.badge>
                @endif
            </div>
            @if($subtitle)
                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    
    @if($value || $trend)
        <div class="flex-shrink-0 text-right">
            @if($value)
                <div class="text-sm font-medium text-gray-900">{{ $value }}</div>
            @endif
            @if($trend)
                <div class="flex items-center justify-end mt-0.5 {{ $trendClasses[$trendType] }}">
                    @if($trendType === 'up')
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    @elseif($trendType === 'down')
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    <span class="text-xs">{{ $trend }}</span>
                </div>
            @endif
        </div>
    @endif
    
    @if($href)
        <div class="flex-shrink-0 ml-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    @endif
</{{ $component }}>
