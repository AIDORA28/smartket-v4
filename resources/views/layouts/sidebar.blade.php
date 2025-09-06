<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     x-show="sidebarOpen || window.innerWidth >= 1024"
     x-transition>
    
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-blue-600">
        <div class="flex items-center">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span class="ml-2 text-xl font-bold text-white">SmartKet</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-5 px-2 pb-20">
        <div class="space-y-1">
            <!-- Dashboard -->
            <x-navigation.nav-item 
                route="dashboard"
                label="Dashboard">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7zm0 0V5a2 2 0 012-2h6l2 2h6a2 2 0 012 2v2" />
                    </svg>
                </x-slot>
            </x-navigation.nav-item>

            <!-- Productos -->
            <x-navigation.nav-item 
                :active="request()->routeIs('productos.*') || request()->routeIs('categorias.*') || request()->routeIs('inventario.*')"
                label="Productos">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </x-slot>
                
                <x-slot name="children">
                    <x-navigation.nav-subitem route="productos.index" label="Catálogo" />
                    <x-navigation.nav-subitem route="categorias.index" label="Categorías" />
                    <x-navigation.nav-subitem route="inventario.index" label="Inventario" />
                </x-slot>
            </x-navigation.nav-item>

            <!-- Ventas -->
            <x-navigation.nav-item 
                :active="request()->routeIs('ventas.*') || request()->routeIs('pos.*') || request()->routeIs('clientes.*')"
                label="Ventas">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </x-slot>
                
                <x-slot name="children">
                    <x-navigation.nav-subitem route="pos.index" label="Punto de Venta" />
                    <x-navigation.nav-subitem route="ventas.index" label="Historial Ventas" />
                    <x-navigation.nav-subitem route="clientes.index" label="Clientes" />
                </x-slot>
            </x-navigation.nav-item>

            <!-- Caja -->
            <x-navigation.nav-item 
                route="caja.index"
                label="Caja">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </x-slot>
            </x-navigation.nav-item>

            <!-- Compras -->
            <x-navigation.nav-item 
                :active="request()->routeIs('compras.*') || request()->routeIs('proveedores.*')"
                label="Compras">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </x-slot>
                
                <x-slot name="children">
                    <x-navigation.nav-subitem route="compras.index" label="Órdenes de Compra" />
                    <x-navigation.nav-subitem route="proveedores.index" label="Proveedores" />
                </x-slot>
            </x-navigation.nav-item>

            <!-- Lotes -->
            <x-navigation.nav-item 
                route="lotes.index"
                label="Lotes y Vencimientos">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </x-slot>
            </x-navigation.nav-item>

            <!-- Reportes -->
            <x-navigation.nav-item 
                :active="request()->routeIs('reportes.*')"
                label="Reportes">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </x-slot>
                
                <x-slot name="children">
                    <x-navigation.nav-subitem route="reportes.index" label="Dashboard Reportes" />
                    <x-navigation.nav-subitem route="reportes.ventas" label="Reportes de Ventas" />
                    <x-navigation.nav-subitem route="reportes.inventario" label="Reportes de Inventario" />
                    <x-navigation.nav-subitem route="reportes.clientes" label="Análisis de Clientes" />
                </x-slot>
            </x-navigation.nav-item>

            <!-- Configuraciones Avanzadas -->
            <x-navigation.nav-item 
                route="configuraciones.index"
                label="Configuraciones">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                    </svg>
                </x-slot>
            </x-navigation.nav-item>

            <!-- Configuración Administrativa (Solo admins) -->
            @can('admin')
            <x-navigation.nav-item 
                :active="request()->routeIs('admin.*')"
                label="Administración">
                <x-slot name="icon">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </x-slot>
                
                <x-slot name="children">
                    <x-navigation.nav-subitem route="admin.empresas.index" label="Empresas" />
                    <x-navigation.nav-subitem route="admin.usuarios.index" label="Usuarios" />
                    <x-navigation.nav-subitem route="admin.feature-flags.index" label="Feature Flags" />
                </x-slot>
            </x-navigation.nav-item>
            @endcan
        </div>
    </nav>

    <!-- Tenant Info -->
    @if(app('App\Services\TenantService')->getEmpresa())
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gray-50 border-t">
            <div class="text-xs text-gray-500">
                <div class="font-medium">{{ app('App\Services\TenantService')->getEmpresa()->nombre }}</div>
                <div>{{ app('App\Services\TenantService')->getEmpresa()->plan->nombre ?? 'Sin Plan' }}</div>
            </div>
        </div>
    @endif
</div>

<!-- Mobile menu overlay -->
<div x-show="sidebarOpen && window.innerWidth < 1024" 
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>
