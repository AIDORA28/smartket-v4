<!-- Tab Usuarios -->
<div class="space-y-6">
    <!-- Header con Acciones -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Gestión de Usuarios</h3>
            <p class="mt-1 text-sm text-gray-500">Administra usuarios, roles y permisos de acceso</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <x-ui.button 
                color="blue" 
                class="inline-flex items-center"
                disabled
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Usuario
            </x-ui.button>
        </div>
    </div>
    
    <!-- Lista de Usuarios -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-900">Usuarios Activos ({{ $totalUsuarios }})</h4>
        </div>
        
        <div class="px-6 py-6">
            <!-- Placeholder para usuarios -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Gestión de Usuarios</h3>
                <p class="mt-1 text-sm text-gray-500">
                    La gestión completa de usuarios estará disponible en una actualización futura.
                </p>
                <div class="mt-6">
                    <x-ui.alert type="info" class="max-w-md mx-auto">
                        <div class="flex">
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">
                                    Funcionalidad Próximamente
                                </h4>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>
                                        El sistema de gestión de usuarios con roles y permisos granulares estará disponible en la próxima actualización del sistema.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </x-ui.alert>
                </div>
            </div>
        </div>
    </x-ui.card>
    
    <!-- Configuraciones de Acceso -->
    <x-ui.card>
        <div class="px-6 py-5 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Configuraciones de Seguridad
            </h4>
            <p class="mt-1 text-sm text-gray-500">Configuraciones básicas de acceso y seguridad</p>
        </div>
        
        <div class="px-6 py-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Autenticación Laravel</p>
                            <p class="text-xs text-gray-500">Sistema de login seguro activado</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Activo</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Sesiones Seguras</p>
                            <p class="text-xs text-gray-500">Gestión de sesiones de usuario</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Activo</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Multi-tenant Security</p>
                            <p class="text-xs text-gray-500">Aislamiento de datos por empresa</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Activo</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Two-Factor Auth</p>
                            <p class="text-xs text-gray-500">Autenticación de dos factores (futuro)</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Próximamente</span>
                </div>
            </div>
        </div>
    </x-ui.card>
</div>
