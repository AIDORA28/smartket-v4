@props([
    'title' => '',
    'subtitle' => null,
    'actions' => null
])

<div {{ $attributes->merge(['class' => 'bg-white shadow-sm border-b']) }}>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Desktop Header -->
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:h-16">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <span class="text-2xl">📊</span>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $title }}</h1>
                    @if($subtitle)
                        <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            
            @if($actions)
                <div class="flex items-center space-x-3">
                    {{ $actions }}
                </div>
            @endif
        </div>
        
        <!-- Mobile Header -->
        <div class="sm:hidden">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-3">
                    <span class="text-xl">📊</span>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">{{ $title }}</h1>
                        @if($subtitle)
                            <p class="text-xs text-gray-500">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Mobile menu trigger si tiene acciones -->
                @if($actions)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" 
                                class="p-2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                        
                        <!-- Mobile actions dropdown -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg z-50 border">
                            <div class="p-4 space-y-3">
                                {{ $actions }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
