<!-- Tab Features -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Control de Funcionalidades</h3>
            <p class="mt-1 text-sm text-gray-500">Gestiona qué módulos y características están disponibles</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <x-ui.badge color="purple" size="large">
                {{ $featuresActivos }} activos
            </x-ui.badge>
        </div>
    </div>
    
    <!-- Módulos Principales -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Módulos Core (Siempre Activos)
            </h4>
            <p class="mt-1 text-sm text-gray-500">Funcionalidades básicas del sistema ERP</p>
        </div>
        
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Dashboard -->
                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Dashboard</p>
                            <p class="text-xs text-green-600">Panel principal</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>
                
                <!-- Productos -->
                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Productos</p>
                            <p class="text-xs text-green-600">Catálogo e inventario</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>
                
                <!-- POS -->
                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Punto de Venta</p>
                            <p class="text-xs text-green-600">Sistema POS</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>
                
                <!-- Clientes -->
                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Clientes</p>
                            <p class="text-xs text-green-600">CRM básico</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>
                
                <!-- Reportes -->
                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Reportes</p>
                            <p class="text-xs text-green-600">Analytics y dashboards</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>
                
                <!-- Inventario -->
                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Inventario</p>
                            <p class="text-xs text-green-600">Control de stock</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </span>
                </div>
            </div>
        </div>
    </x-ui.card>
    
    <!-- Funcionalidades Avanzadas -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                Funcionalidades Opcionales
            </h4>
            <p class="mt-1 text-sm text-gray-500">Características que pueden activarse/desactivarse según el plan</p>
        </div>
        
        <div class="px-6 py-6">
            <div class="space-y-4">
                <!-- Multi-sucursal -->
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-gray-900">Multi-sucursal</p>
                                <x-ui.badge color="blue" size="small" class="ml-2">Pro</x-ui.badge>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Gestión de múltiples ubicaciones</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-not-allowed">
                        <input type="checkbox" class="sr-only" disabled>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                
                <!-- Lotes y Vencimientos -->
                <div class="flex items-center justify-between p-4 border border-green-200 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-green-900">Lotes y Vencimientos</p>
                                <x-ui.badge color="green" size="small" class="ml-2">Activo</x-ui.badge>
                            </div>
                            <p class="text-xs text-green-600 mt-1">Control FIFO y alertas de caducidad</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-not-allowed">
                        <input type="checkbox" class="sr-only" checked disabled>
                        <div class="w-11 h-6 bg-green-600 peer-focus:outline-none rounded-full peer after:content-[''] after:absolute after:top-[2px] after:left-[7px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    </label>
                </div>
                
                <!-- Caja -->
                <div class="flex items-center justify-between p-4 border border-green-200 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-green-900">Sistema de Caja</p>
                                <x-ui.badge color="green" size="small" class="ml-2">Activo</x-ui.badge>
                            </div>
                            <p class="text-xs text-green-600 mt-1">Apertura, cierre y arqueo de caja</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-not-allowed">
                        <input type="checkbox" class="sr-only" checked disabled>
                        <div class="w-11 h-6 bg-green-600 peer-focus:outline-none rounded-full peer after:content-[''] after:absolute after:top-[2px] after:left-[7px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    </label>
                </div>
                
                <!-- Facturación Electrónica -->
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-gray-900">Facturación SUNAT</p>
                                <x-ui.badge color="yellow" size="small" class="ml-2">Premium</x-ui.badge>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Comprobantes electrónicos</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-not-allowed">
                        <input type="checkbox" class="sr-only" disabled>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
            
            <div class="mt-6">
                <x-ui.alert type="info">
                    <div class="flex">
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">
                                Gestión de Feature Flags
                            </h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>
                                    El sistema completo de gestión de feature flags estará disponible en la próxima actualización. Por ahora, las funcionalidades están habilitadas según la configuración del plan.
                                </p>
                            </div>
                        </div>
                    </div>
                </x-ui.alert>
            </div>
        </div>
    </x-ui.card>
</div>
