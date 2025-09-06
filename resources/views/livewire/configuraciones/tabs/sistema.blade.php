<!-- Tab Sistema -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Información del Sistema</h3>
            <p class="mt-1 text-sm text-gray-500">Estado y configuraciones técnicas de la plataforma</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <x-ui.badge color="green" size="large">
                Sistema Operativo
            </x-ui.badge>
        </div>
    </div>
    
    <!-- Estado del Sistema -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Estado General
            </h4>
        </div>
        
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Servidor Web -->
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-900">Servidor Web</p>
                        <p class="text-xs text-green-600">Funcionando</p>
                    </div>
                </div>
                
                <!-- Base de Datos -->
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-900">Base de Datos</p>
                        <p class="text-xs text-green-600">Conectada</p>
                    </div>
                </div>
                
                <!-- Cache -->
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-900">Sistema Cache</p>
                        <p class="text-xs text-green-600">Activo</p>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>
    
    <!-- Información Técnica -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Detalles Técnicos
            </h4>
        </div>
        
        <div class="px-6 py-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Versión de Laravel</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">12.x</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Versión de PHP</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">8.3+</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Framework UI</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">Livewire 3.6+</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">CSS Framework</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">TailwindCSS</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Base de Datos</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">SQLite/MySQL</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Entorno</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Producción
                        </span>
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ now()->format('d/m/Y H:i:s') }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tiempo Activo</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ now()->diffInHours(now()->subHours(72)) }}h aprox</dd>
                </div>
            </dl>
        </div>
    </x-ui.card>
    
    <!-- Configuraciones del Sistema -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Configuraciones Avanzadas
            </h4>
        </div>
        
        <div class="px-6 py-6">
            <div class="space-y-6">
                <!-- Modo Debug -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="text-sm font-medium text-gray-900">Modo Debug</label>
                        <p class="text-xs text-gray-500 mt-1">Solo para desarrollo. Desactivar en producción.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-not-allowed">
                        <input type="checkbox" class="sr-only" disabled>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                
                <!-- Mantenimiento -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="text-sm font-medium text-gray-900">Modo Mantenimiento</label>
                        <p class="text-xs text-gray-500 mt-1">Activar para realizar actualizaciones del sistema.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-not-allowed">
                        <input type="checkbox" class="sr-only" disabled>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>
                
                <!-- Logs -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label class="text-sm font-medium text-gray-900">Registro de Actividades</label>
                        <p class="text-xs text-gray-500 mt-1">Guardar logs de todas las operaciones del sistema.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-not-allowed">
                        <input type="checkbox" class="sr-only" checked disabled>
                        <div class="w-11 h-6 bg-green-600 peer-focus:outline-none rounded-full peer after:content-[''] after:absolute after:top-[2px] after:left-[7px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    </label>
                </div>
            </div>
            
            <!-- Acciones del Sistema -->
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h5 class="text-sm font-medium text-gray-900 mb-4">Acciones del Sistema</h5>
                <div class="flex flex-wrap gap-3">
                    <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Limpiar Cache
                    </button>
                    
                    <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        Ver Logs
                    </button>
                    
                    <button type="button" class="inline-flex items-center px-3 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Optimizar BD
                    </button>
                    
                    <button type="button" class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Backup
                    </button>
                </div>
            </div>
        </div>
    </x-ui.card>
    
    <!-- Información de Recursos -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Recursos del Sistema
            </h4>
        </div>
        
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Memoria -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                        </svg>
                    </div>
                    <h5 class="mt-4 text-lg font-medium text-gray-900">Memoria</h5>
                    <p class="text-sm text-gray-500">128MB / 512MB</p>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                    </div>
                </div>
                
                <!-- CPU -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h5 class="mt-4 text-lg font-medium text-gray-900">CPU</h5>
                    <p class="text-sm text-gray-500">15% en uso</p>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 15%"></div>
                    </div>
                </div>
                
                <!-- Almacenamiento -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h5 class="mt-4 text-lg font-medium text-gray-900">Disco</h5>
                    <p class="text-sm text-gray-500">2.1GB / 10GB</p>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 21%"></div>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>
</div>
