<!-- Sidebar para desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
        <!-- Logo -->
        <div class="flex h-16 shrink-0 items-center">
            <div class="h-8 w-8 bg-blue-600 rounded-md flex items-center justify-center">
                <span class="text-white font-bold text-lg">S</span>
            </div>
            <span class="ml-2 text-white font-bold text-xl">SmartKet</span>
        </div>
        
        <!-- Navigation -->
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('dashboard') }}" 
                               data-fast-nav 
                               data-module="dashboard"
                               class="{{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-150">
                                <span class="text-xl">📊</span>
                                Dashboard
                            </a>
                        </li>
                        
                        <!-- Productos -->
                        <li>
                            <a href="{{ route('productos.index') }}" 
                               data-fast-nav 
                               data-module="productos"
                               class="{{ request()->routeIs('productos.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-150">
                                <span class="text-xl">📦</span>
                                Productos
                            </a>
                        </li>
                        
                        <!-- POS -->
                        <li>
                            <a href="{{ route('pos.index') }}" 
                               data-fast-nav 
                               data-module="pos"
                               class="{{ request()->routeIs('pos.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-150">
                                <span class="text-xl">🛒</span>
                                Punto de Venta
                            </a>
                        </li>
                        
                        <!-- Inventario -->
                        <li>
                            <a href="{{ route('inventario.index') }}" 
                               data-fast-nav 
                               data-module="inventario"
                               class="{{ request()->routeIs('inventario.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-150">
                                <span class="text-xl">📊</span>
                                Inventario
                            </a>
                        </li>

                        <!-- Clientes -->
                        <li>
                            <a href="{{ route('clientes.index') }}" 
                               data-fast-nav 
                               data-module="clientes"
                               class="{{ request()->routeIs('clientes.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-150">
                                <span class="text-xl">�</span>
                                Clientes
                            </a>
                        </li>

                        <!-- Reportes -->
                        <li>
                            <a href="{{ route('reportes.index') }}" 
                               data-fast-nav 
                               data-module="reportes"
                               class="{{ request()->routeIs('reportes.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-150">
                                <span class="text-xl">📈</span>
                                Reportes
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Settings section -->
                <li class="mt-auto">
                    <ul role="list" class="-mx-2 space-y-1">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white hover:bg-gray-800 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <span class="text-xl">⚙️</span>
                                Configuración
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Sidebar móvil -->
<div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/80"></div>

    <div class="fixed inset-0 flex">
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative mr-16 flex w-full max-w-xs flex-1">
            
            <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5">
                    <span class="sr-only">Cerrar sidebar</span>
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mismo contenido que el sidebar de desktop -->
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
                <!-- Logo -->
                <div class="flex h-16 shrink-0 items-center">
                    <div class="h-8 w-8 bg-blue-600 rounded-md flex items-center justify-center">
                        <span class="text-white font-bold text-lg">S</span>
                    </div>
                    <span class="ml-2 text-white font-bold text-xl">SmartKet</span>
                </div>
                
                <!-- Navigation - mismo contenido -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <a href="{{ route('productos.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="{{ request()->routeIs('productos.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                        <span class="text-xl">�</span>
                                        Productos
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
