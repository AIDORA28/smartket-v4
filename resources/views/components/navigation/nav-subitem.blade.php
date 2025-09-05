@props([
    'href' => null,
    'route' => null,
    'label' => '',
    'active' => false,
    'badge' => null,
    'badgeColor' => 'blue'
])

@php
    $href = $href ?: ($route ? route($route) : '#');
    $isActive = $active || ($route && request()->routeIs($route));
    
    $baseClasses = 'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150 relative';
    $activeClasses = 'bg-blue-50 text-blue-700 border-l-2 border-blue-500 ml-2';
    $inactiveClasses = 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 ml-2';
    
    $classes = $baseClasses . ' ' . ($isActive ? $activeClasses : $inactiveClasses);
@endphp

<a href="{{ $href }}" class="{{ $classes }}">
    <span class="flex-1">{{ $label }}</span>
    
    @if($badge)
        <x-ui.badge variant="{{ $badgeColor }}" size="sm" class="ml-2">
            {{ $badge }}
        </x-ui.badge>
    @endif
</a>
