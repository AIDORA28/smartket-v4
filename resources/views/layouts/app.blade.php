<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SmartKet ERP') }} - @yield('title', 'Panel de Control')</title>
    <meta name="description" content="@yield('description', 'Sistema ERP para comercio minorista - SmartKet')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: margin-left 0.3s ease-in-out; }
        .content-blur { filter: blur(2px); transition: filter 0.2s ease-in-out; }
        
        /* Mobile sidebar overlay */
        @media (max-width: 1023px) {
            .sidebar-overlay {
                backdrop-filter: blur(4px);
            }
        }
        
        /* Loading state */
        .loading-overlay {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(2px);
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <!-- Loading overlay (can be controlled by Livewire) -->
    <div x-data="{ loading: false }" 
         x-show="loading" 
         x-cloak
         class="fixed inset-0 z-[9999] loading-overlay flex items-center justify-center">
        <x-ui.loading size="lg" />
    </div>

    <!-- Main App Container -->
    <div class="min-h-screen" x-data="{ 
        sidebarOpen: window.innerWidth >= 1024,
        isMobile: window.innerWidth < 1024
    }" @resize.window="
        isMobile = window.innerWidth < 1024;
        if (!isMobile && !sidebarOpen) {
            sidebarOpen = true;
        }
    ">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Page Content -->
        <main class="sidebar-transition" :class="sidebarOpen && !isMobile ? 'lg:ml-64' : 'ml-0'">
            <!-- Navigation -->
            @include('layouts.navigation')

            <!-- Flash Messages -->
            <div class="relative z-40">
                @if (session('success'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div class="bg-green-50 border border-green-200 rounded-md p-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-red-800">Se encontraron errores:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Header -->
            @isset($header)
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </main>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Custom Scripts -->
    @stack('scripts')
    
    <!-- Debug Livewire Connection -->
    <script>
        // Verificar que todo esté cargado correctamente
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔧 Layout Debug:');
            console.log('  - Alpine disponible:', typeof window.Alpine !== 'undefined');
            console.log('  - Livewire disponible:', typeof window.Livewire !== 'undefined');
            
            // Auto-hide success messages after 5 seconds
            setTimeout(() => {
                const successMessages = document.querySelectorAll('[x-data*="show: true"]');
                successMessages.forEach(message => {
                    if (message.textContent.includes('success') || message.querySelector('.text-green-800')) {
                        message.__x.$data.show = false;
                    }
                });
            }, 5000);
        });
        
        // Evento cuando Livewire se inicializa
        document.addEventListener('livewire:init', () => {
            console.log('✅ Livewire inicializado en layout');
        });
        
        // Evento cuando Livewire se actualiza
        document.addEventListener('livewire:update', (event) => {
            console.log('🔄 Livewire actualizado:', event);
        });
    </script>
</body>
</html>
