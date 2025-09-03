<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $module }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Placeholder Content -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="text-center">
                    <!-- Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="mt-6 text-lg font-medium text-gray-900">{{ $module }}</h3>
                    
                    <!-- Description -->
                    <p class="mt-2 text-sm text-gray-500">
                        @if(isset($description))
                            {{ $description }}
                        @else
                            Esta funcionalidad estará disponible próximamente.
                        @endif
                    </p>
                    
                    <!-- Status Badge -->
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            En Desarrollo
                        </span>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-6 flex justify-center space-x-3">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver al Dashboard
                        </a>
                        
                        @if(request()->routeIs('pos.*'))
                            <button disabled 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-400 cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd" />
                                </svg>
                                Comenzar Venta (Próximamente)
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Preview -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Funcionalidades Planificadas</h3>
                
                @if(request()->routeIs('pos.*'))
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <h4 class="ml-2 text-sm font-medium text-gray-900">Interfaz Táctil</h4>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">POS optimizado para tablets y pantallas táctiles</p>
                        </div>
                        
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h4 class="ml-2 text-sm font-medium text-gray-900">Múltiples Pagos</h4>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Efectivo, tarjeta, transferencia y crédito</p>
                        </div>
                        
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <h4 class="ml-2 text-sm font-medium text-gray-900">Control de Stock</h4>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Descuento automático del inventario</p>
                        </div>
                    </div>
                
                @elseif(request()->routeIs('reportes.*'))
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h4 class="ml-2 text-sm font-medium text-gray-900">Exportación PDF</h4>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Reportes profesionales con logos y styling</p>
                        </div>
                        
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <h4 class="ml-2 text-sm font-medium text-gray-900">Analytics Dashboard</h4>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">KPIs y métricas de negocio en tiempo real</p>
                        </div>
                    </div>
                
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500">
                            Las funcionalidades específicas de este módulo se implementarán próximamente.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Estado del Desarrollo</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Backend API</span>
                        <span class="text-sm text-green-600 font-medium">100% Completado</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Frontend Web</span>
                        <span class="text-sm text-yellow-600 font-medium">En Desarrollo</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: 30%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Testing</span>
                        <span class="text-sm text-gray-600 font-medium">Pendiente</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gray-400 h-2 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
