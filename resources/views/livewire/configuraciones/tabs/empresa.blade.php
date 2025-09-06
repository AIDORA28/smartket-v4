<!-- Tab Empresa -->
<div class="space-y-6">
    <!-- Información Empresa -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Información de la Empresa
            </h3>
            <p class="mt-1 text-sm text-gray-500">Datos principales de {{ $empresa?->nombre ?? 'tu empresa' }}</p>
        </div>
        
        <div class="px-6 py-6">
            @if($empresa)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Empresa</label>
                            <div class="p-3 bg-gray-50 rounded-md border">
                                <p class="text-sm font-medium text-gray-900">{{ $empresa->nombre }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">RUC</label>
                            <div class="p-3 bg-gray-50 rounded-md border">
                                <p class="text-sm text-gray-900">
                                    {{ $empresa->ruc ?? 'No configurado' }}
                                    @if($empresa->tiene_ruc)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-2">Verificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Rubro</label>
                            <div class="p-3 bg-gray-50 rounded-md border">
                                <p class="text-sm text-gray-900 capitalize">{{ $empresa->tipo_rubro ?? 'General' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plan Actual</label>
                            <div class="p-3 bg-gray-50 rounded-md border">
                                <p class="text-sm text-gray-900">Plan Básico</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Zona Horaria</label>
                            <div class="p-3 bg-gray-50 rounded-md border">
                                <p class="text-sm text-gray-900">{{ $empresa->timezone ?? 'America/Lima' }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <div class="p-3 bg-gray-50 rounded-md border">
                                @if($empresa->activa ?? true)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Inactiva
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botón Editar (Deshabilitado por ahora) -->
                <div class="mt-6 flex justify-end">
                    <x-ui.button color="blue" disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Información
                    </x-ui.button>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Sin Información de Empresa</h3>
                    <p class="mt-1 text-sm text-gray-500">No se pudo cargar la información de la empresa.</p>
                </div>
            @endif
        </div>
    </x-ui.card>
    
    <!-- Sucursales -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Sucursales ({{ $totalSucursales }})
            </h3>
            <p class="mt-1 text-sm text-gray-500">Ubicaciones y puntos de venta de la empresa</p>
        </div>
        
        <div class="px-6 py-6">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Gestión de Sucursales</h3>
                <p class="mt-1 text-sm text-gray-500">
                    La gestión completa de sucursales estará disponible próximamente.
                </p>
                <div class="mt-6">
                    <x-ui.alert type="info" class="max-w-md mx-auto">
                        <div class="flex">
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">
                                    Funcionalidad en Desarrollo
                                </h4>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>
                                        El módulo de gestión completa de sucursales con ubicaciones, configuraciones específicas y transferencias estará disponible en la próxima actualización.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </x-ui.alert>
                </div>
            </div>
        </div>
    </x-ui.card>
</div>
