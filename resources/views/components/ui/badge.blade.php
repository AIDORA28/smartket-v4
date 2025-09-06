@props([
    'variant' => 'gray',
    'color' => null,  // Support both color and variant
    'size' => 'md'
])

@php
// Use color if provided, otherwise use variant
$colorToUse = $color ?? $variant;

$variants = [
    'gray' => 'bg-gray-100 text-gray-800',
    'blue' => 'bg-blue-100 text-blue-800',
    'green' => 'bg-green-100 text-green-800',
    'yellow' => 'bg-yellow-100 text-yellow-800',
    'red' => 'bg-red-100 text-red-800',
    'purple' => 'bg-purple-100 text-purple-800',
    'pink' => 'bg-pink-100 text-pink-800',
    'indigo' => 'bg-indigo-100 text-indigo-800',
];

$sizes = [
    'xs' => 'px-1.5 py-0.5 text-xs',
    'sm' => 'px-2 py-1 text-xs',
    'small' => 'px-2 py-1 text-xs',  // Alias
    'md' => 'px-2.5 py-0.5 text-xs',
    'lg' => 'px-3 py-1 text-sm',
    'large' => 'px-4 py-1.5 text-sm font-semibold',  // Add large size
    'xl' => 'px-4 py-2 text-base',
];

// Safely get variant and size with fallbacks
$variantClass = $variants[$colorToUse] ?? $variants['gray'];
$sizeClass = $sizes[$size] ?? $sizes['md'];

$classes = 'inline-flex items-center font-medium rounded-full ' . $variantClass . ' ' . $sizeClass;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
