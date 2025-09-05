@props([
    'icon' => '📋',
    'title' => 'No hay datos',
    'message' => 'No se encontraron elementos para mostrar',
    'actionText' => null,
    'actionUrl' => null,
    'actionVariant' => 'primary'
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-8 px-4 text-center']) }}>
    <div class="text-4xl mb-3">{{ $icon }}</div>
    
    <h3 class="text-sm font-medium text-gray-900 mb-1">{{ $title }}</h3>
    <p class="text-sm text-gray-500 mb-4 max-w-sm">{{ $message }}</p>
    
    @if($actionUrl && $actionText)
        <x-ui.button variant="{{ $actionVariant }}" size="sm" href="{{ $actionUrl }}">
            {{ $actionText }}
        </x-ui.button>
    @endif
    
    {{ $slot }}
</div>
