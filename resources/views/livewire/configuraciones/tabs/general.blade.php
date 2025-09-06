<!-- Tab General -->
<div class="space-y-6">
    <!-- Configuraciones Rápidas -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Configuraciones Rápidas
            </h3>
            <p class="mt-1 text-sm text-gray-500">Ajustes comunes del sistema</p>
        </div>
        
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Configuración Empresa -->
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors cursor-pointer" wire:click="setActiveTab('empresa')">
                    <div class="flex items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">Datos Empresa</h4>
                            <p class="text-xs text-gray-500">Información general</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">Configura nombre, RUC, dirección y otros datos de la empresa.</p>
                </div>
                
                <!-- Gestión Usuarios -->
                <div class="border border-gray-200 rounded-lg p-4 hover:border-green-300 transition-colors cursor-pointer" wire:click="setActiveTab('usuarios')">
                    <div class="flex items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">Usuarios y Accesos</h4>
                            <p class="text-xs text-gray-500">{{ $totalUsuarios }} activos</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">Administra usuarios, roles y permisos de acceso al sistema.</p>
                </div>
                
                <!-- Feature Flags -->
                <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors cursor-pointer" wire:click="setActiveTab('features')">
                    <div class="flex items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">Funcionalidades</h4>
                            <p class="text-xs text-gray-500">{{ $featuresActivos }} habilitados</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">Controla qué módulos y características están disponibles.</p>
                </div>
            </div>
        </div>
    </x-ui.card>
    
    <!-- Resumen del Sistema -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Estado del Sistema -->
        <x-ui.card>
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Estado del Sistema
                </h3>
            </div>
            
            <div class="px-6 py-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Estado General</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Operativo
                    </span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Base de Datos</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Conectada
                    </span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Multi-tenancy</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Activo
                    </span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">TenantService</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Funcionando 🎉
                    </span>
                </div>
            </div>
        </x-ui.card>
        
        <!-- Información Técnica -->
        <x-ui.card>
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Información Técnica
                </h3>
            </div>
            
            <div class="px-6 py-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Versión SmartKet</span>
                    <span class="text-sm font-medium text-gray-900">v4.0</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Laravel Framework</span>
                    <span class="text-sm font-medium text-gray-900">v11.45+</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">PHP Version</span>
                    <span class="text-sm font-medium text-gray-900">v8.3+</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Livewire</span>
                    <span class="text-sm font-medium text-gray-900">v3.6+</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Base de Datos</span>
                    <span class="text-sm font-medium text-gray-900">SQLite</span>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
