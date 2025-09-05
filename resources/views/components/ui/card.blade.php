@props([
    'title' => null,
    'actions' => null,
    'padding' => 'p-6'
])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm rounded-lg']) }}>
    @if($title || $actions)
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            @if($title)
                <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
            @endif
            
            @if($actions)
                <div class="flex items-center space-x-3">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
