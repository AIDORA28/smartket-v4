<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Configuraciones
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Administra la configuración de tu empresa y sistema
                </p>
            </div>
            
            <!-- Sistema Info -->
            <div class="mt-4 sm:mt-0">
                <x-ui.card class="bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9a12.02 12.02 0 00-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-blue-900">
                                {{ $empresa?->nombre ?? 'Sistema SmartKet' }}
                            </p>
                            <p class="text-xs text-blue-600">
                                SmartKet v4.0 - Multi-tenant ERP
                            </p>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-dashboard.kpi-card
            title="Usuarios Activos"
            :value="$totalUsuarios"
            icon="users"
            color="blue"
            subtitle="usuarios en el sistema"
        />
        
        <x-dashboard.kpi-card
            title="Sucursales"
            :value="$totalSucursales"
            icon="location-marker"
            color="green"
            subtitle="ubicaciones configuradas"
        />
        
        <x-dashboard.kpi-card
            title="Funciones Activas"
            :value="$featuresActivos"
            icon="cog"
            color="purple"
            subtitle="features habilitados"
        />
    </div>
    
    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8">
            <button 
                wire:click="setActiveTab('general')"
                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 
                    {{ $activeTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>General</span>
                </div>
            </button>
            
            <button 
                wire:click="setActiveTab('usuarios')"
                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 
                    {{ $activeTab === 'usuarios' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <span>Usuarios</span>
                </div>
            </button>
            
            <button 
                wire:click="setActiveTab('empresa')"
                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 
                    {{ $activeTab === 'empresa' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Empresa</span>
                </div>
            </button>
            
            <button 
                wire:click="setActiveTab('features')"
                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 
                    {{ $activeTab === 'features' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                    <span>Funcionalidades</span>
                </div>
            </button>
            
            <button 
                wire:click="setActiveTab('sistema')"
                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 
                    {{ $activeTab === 'sistema' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                    <span>Sistema</span>
                </div>
            </button>
        </nav>
    </div>
    
    <!-- Tab Content -->
    <div class="space-y-6">
        @if($activeTab === 'general')
            @include('livewire.configuraciones.tabs.general')
        @elseif($activeTab === 'usuarios')
            @include('livewire.configuraciones.tabs.usuarios')
        @elseif($activeTab === 'empresa')
            @include('livewire.configuraciones.tabs.empresa')
        @elseif($activeTab === 'features')
            @include('livewire.configuraciones.tabs.features')
        @elseif($activeTab === 'sistema')
            @include('livewire.configuraciones.tabs.sistema')
        @endif
    </div>
</div>
