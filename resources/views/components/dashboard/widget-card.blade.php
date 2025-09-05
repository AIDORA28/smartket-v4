@props([
    'title' => '',
    'subtitle' => null,
    'action' => null,
    'actionUrl' => null,
    'actionText' => 'Ver todos',
    'loading' => false,
    'emptyIcon' => '📋',
    'emptyMessage' => 'No hay datos disponibles'
])

<x-ui.card {{ $attributes->merge(['class' => 'widget-card h-full']) }}>
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        
        @if($actionUrl || $action)
            <div class="flex-shrink-0">
                @if($actionUrl)
                    <a href="{{ $actionUrl }}" 
                       class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        {{ $actionText }} →
                    </a>
                @elseif($action)
                    {{ $action }}
                @endif
            </div>
        @endif
    </div>
    
    <!-- Content -->
    <div class="flex-1">
        @if($loading)
            <div class="flex items-center justify-center py-8">
                <x-ui.loading size="lg" />
            </div>
        @else
            {{ $slot }}
        @endif
    </div>
    
    <!-- Empty State (usar cuando $slot esté vacío) -->
    {{ $empty ?? '' }}
</x-ui.card>

<!-- Slot para estado vacío -->
@push('dashboard-empty-states')
<template x-if="isEmpty">
    <div class="flex flex-col items-center justify-center py-8 text-center">
        <div class="text-4xl mb-3">{{ $emptyIcon }}</div>
        <p class="text-sm text-gray-500 mb-4">{{ $emptyMessage }}</p>
        @if($actionUrl)
            <x-ui.button variant="primary" size="sm" href="{{ $actionUrl }}">
                Comenzar
            </x-ui.button>
        @endif
    </div>
</template>
@endpush
