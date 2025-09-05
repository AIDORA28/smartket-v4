@props([
    'href' => null,
    'route' => null,
    'icon' => null,
    'label' => '',
    'active' => false,
    'children' => null,
    'badge' => null,
    'badgeColor' => 'blue'
])

@php
    $href = $href ?: ($route ? route($route) : '#');
    $isActive = $active || ($route && request()->routeIs($route));
    
    $baseClasses = 'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-150';
    $activeClasses = 'bg-blue-100 text-blue-700 border-r-2 border-blue-500';
    $inactiveClasses = 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
    
    $classes = $baseClasses . ' ' . ($isActive ? $activeClasses : $inactiveClasses);
@endphp

@if($children)
    {{-- Menu item with submenu --}}
    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="{{ $baseClasses }} {{ $inactiveClasses }} w-full justify-between">
            <div class="flex items-center">
                @if($icon)
                    <div class="mr-3 flex h-6 w-6 items-center justify-center">
                        {!! $icon !!}
                    </div>
                @endif
                <span>{{ $label }}</span>
                @if($badge)
                    <x-ui.badge variant="{{ $badgeColor }}" size="sm" class="ml-2">
                        {{ $badge }}
                    </x-ui.badge>
                @endif
            </div>
            
            <svg class="h-5 w-5 transform transition-transform duration-200" 
                 :class="open ? 'rotate-90' : ''" 
                 fill="currentColor" 
                 viewBox="0 0 20 20">
                <path fill-rule="evenodd" 
                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" 
                      clip-rule="evenodd" />
            </svg>
        </button>
        
        <div x-show="open" 
             x-collapse
             class="ml-8 space-y-1 overflow-hidden">
            {{ $children }}
        </div>
    </div>
@else
    {{-- Simple menu item --}}
    <a href="{{ $href }}" class="{{ $classes }}">
        @if($icon)
            <div class="mr-3 flex h-6 w-6 items-center justify-center">
                {!! $icon !!}
            </div>
        @endif
        
        <span class="flex-1">{{ $label }}</span>
        
        @if($badge)
            <x-ui.badge variant="{{ $badgeColor }}" size="sm" class="ml-2">
                {{ $badge }}
            </x-ui.badge>
        @endif
        
        @if($isActive)
            <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-500 rounded-l"></div>
        @endif
    </a>
@endif
