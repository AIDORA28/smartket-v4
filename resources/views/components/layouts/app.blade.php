<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SmartKet ERP') }} - {{ $title ?? 'Panel de Control' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: margin-left 0.3s ease-in-out; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Page Content -->
        <div class="sidebar-transition" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'">
            <!-- Top Navigation -->
            @include('layouts.navigation')

            <!-- Page Header -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Main Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Tenant Selector Component -->
        <livewire:tenant-selector />
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Custom Scripts -->
    <script>
        // Auto-close sidebar on mobile after navigation
        document.addEventListener('livewire:navigated', () => {
            if (window.innerWidth < 1024) {
                Alpine.store('sidebar', false);
            }
        });

        // Handle responsive sidebar
        window.addEventListener('resize', () => {
            const sidebarOpen = window.innerWidth >= 1024;
            // Update Alpine data if needed
        });
    </script>
</body>
</html>
