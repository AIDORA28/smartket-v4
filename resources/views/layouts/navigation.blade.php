<!-- Top Navigation -->
<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side -->
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <!-- Breadcrumbs -->
                <div class="hidden lg:flex lg:items-center lg:ml-4">
                    <x-navigation.breadcrumbs />
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <!-- Tenant Selector -->
                @if(auth()->user() && app('App\Services\TenantService')->getEmpresa())
                    <div class="hidden lg:block">
                        <livewire:tenant-selector />
                    </div>
                @endif
                
                <!-- Quick Actions -->
                <div class="flex items-center space-x-2">
                    <!-- POS Quick Access -->
                    <x-ui.button variant="primary" size="sm" href="{{ route('pos.index') }}" class="hidden sm:inline-flex">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        POS
                    </x-ui.button>
                    
                    <!-- Mobile POS button -->
                    <x-ui.button variant="primary" size="sm" href="{{ route('pos.index') }}" class="sm:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </x-ui.button>
                </div>
                
                <!-- Notifications -->
                <button class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors relative">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5-3.5a5.98 5.98 0 000-8.49l3.5-3.5H15m-6 0H4l3.5 3.5a5.98 5.98 0 000 8.49L4 17h5m6-10v4m-6-4v4" />
                    </svg>
                    <!-- Notification badge (hidden for now) -->
                    <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-400 ring-2 ring-white hidden"></span>
                    <span class="sr-only">Ver notificaciones</span>
                </button>

                <!-- User Menu -->
                <x-navigation.user-menu />
            </div>
        </div>
        
        <!-- Mobile Tenant Selector -->
        @if(auth()->user() && app('App\Services\TenantService')->getEmpresa())
            <div class="lg:hidden px-4 py-3 border-t border-gray-200 bg-gray-50">
                <livewire:tenant-selector />
            </div>
        @endif
    </div>
</nav>
