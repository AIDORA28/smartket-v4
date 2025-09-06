@props([
    'variant' => 'primary',
    'color' => null,  // Support both color and variant
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'loading' => false
])

@php
// Use color if provided, otherwise use variant
$colorToUse = $color ?? $variant;

$variants = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white border-transparent',
    'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white border-transparent',  // Alias
    'secondary' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 text-white border-transparent',
    'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white border-transparent',
    'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white border-transparent',  // Alias
    'warning' => 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-500 text-white border-transparent',
    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white border-transparent',
    'white' => 'bg-white hover:bg-gray-50 focus:ring-blue-500 text-gray-900 border-gray-300',
    'ghost' => 'bg-transparent hover:bg-gray-100 focus:ring-blue-500 text-gray-700 border-transparent',
];

$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs',
    'sm' => 'px-3 py-2 text-sm leading-4',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-4 py-2 text-base',
    'xl' => 'px-6 py-3 text-base',
];

$baseClasses = 'inline-flex items-center justify-center font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';

// Safely get variant and size with fallbacks
$variantClass = $variants[$colorToUse] ?? $variants['primary'];
$sizeClass = $sizes[$size] ?? $sizes['md'];

$classes = $baseClasses . ' ' . $variantClass . ' ' . $sizeClass;

if ($disabled || $loading) {
    $classes .= ' opacity-50 cursor-not-allowed';
}
@endphp

@if($href && !$disabled && !$loading)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}"
        {{ $disabled || $loading ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if($loading)
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
