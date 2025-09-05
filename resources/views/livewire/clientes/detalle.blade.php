<div>
    {{-- Encabezado del cliente --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-xl font-bold text-blue-600">
                            {{ strtoupper(substr($cliente->nombre, 0, 2)) }}
                        </span>
                    </div>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $cliente->nombre }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                        <span>{{ $cliente->tipo_documento }}: {{ $cliente->numero_documento }}</span>
                        @if($cliente->es_empresa)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Empresa
                            </span>
                        @endif
                        @if($cliente->activo)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Activo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <button wire:click="editarCliente" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </button>
                <button wire:click="toggleEstado" 
                        class="{{ $cliente->activo ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    {{ $cliente->activo ? 'Desactivar' : 'Activar' }}
                </button>
            </div>
        </div>
    </div>

    {{-- Estadísticas del cliente --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_ventas']) }}</div>
            <div class="text-sm text-gray-600">Total Compras</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-2xl font-bold text-green-600">S/ {{ number_format($stats['monto_total_comprado'], 2) }}</div>
            <div class="text-sm text-gray-600">Monto Total</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-2xl font-bold text-purple-600">S/ {{ number_format($stats['ticket_promedio'], 2) }}</div>
            <div class="text-sm text-gray-600">Ticket Promedio</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-2xl font-bold text-orange-600">
                {{ $stats['ultima_compra'] ? $stats['ultima_compra']->format('d/m/Y') : '-' }}
            </div>
            <div class="text-sm text-gray-600">Última Compra</div>
        </div>
    </div>

    {{-- Navegación por pestañas --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <button wire:click="setActiveTab('info')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Información Personal
                </button>
                <button wire:click="setActiveTab('compras')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'compras' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Historial de Compras
                </button>
                @if($cliente->permite_credito)
                    <button wire:click="setActiveTab('credito')"
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'credito' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Estado de Crédito
                    </button>
                @endif
            </nav>
        </div>

        <div class="p-6">
            {{-- Pestaña: Información Personal --}}
            @if($activeTab === 'info')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Datos Personales</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre completo</dt>
                                <dd class="text-sm text-gray-900">{{ $cliente->nombre }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo de documento</dt>
                                <dd class="text-sm text-gray-900">{{ $cliente->tipo_documento }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Número de documento</dt>
                                <dd class="text-sm text-gray-900">{{ $cliente->numero_documento }}</dd>
                            </div>
                            @if($cliente->fecha_nacimiento)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fecha de nacimiento</dt>
                                    <dd class="text-sm text-gray-900">{{ $cliente->fecha_nacimiento->format('d/m/Y') }}</dd>
                                </div>
                            @endif
                            @if($cliente->genero)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Género</dt>
                                    <dd class="text-sm text-gray-900">
                                        @switch($cliente->genero)
                                            @case('M') Masculino @break
                                            @case('F') Femenino @break
                                            @case('O') Otro @break
                                        @endswitch
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Contacto</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($cliente->email)
                                        <a href="mailto:{{ $cliente->email }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $cliente->email }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">No registrado</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($cliente->telefono)
                                        <a href="tel:{{ $cliente->telefono }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $cliente->telefono }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">No registrado</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                                <dd class="text-sm text-gray-900">{{ $cliente->direccion ?: 'No registrada' }}</dd>
                            </div>
                        </dl>
                        
                        @if($cliente->email || $cliente->telefono)
                            <div class="mt-6 space-y-2">
                                @if($cliente->email)
                                    <a href="mailto:{{ $cliente->email }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Enviar Email
                                    </a>
                                @endif
                                
                                @if($cliente->telefono)
                                    <a href="https://wa.me/51{{ $cliente->telefono }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-2 border border-green-300 rounded-md text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 ml-2">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                        </svg>
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Pestaña: Historial de Compras --}}
            @if($activeTab === 'compras')
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historial de Compras</h3>
                    
                    @if($ventasRecientes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            N° Comprobante
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Monto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vendedor
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($ventasRecientes as $venta)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $venta->fecha_venta->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $venta->numero_venta ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                S/ {{ number_format($venta->total_final, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @switch($venta->estado)
                                                    @case('completada')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Completada
                                                        </span>
                                                        @break
                                                    @case('pendiente')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Pendiente
                                                        </span>
                                                        @break
                                                    @case('anulada')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Anulada
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ ucfirst($venta->estado) }}
                                                        </span>
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $venta->usuario->nombre ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Sin compras registradas</h3>
                            <p class="mt-1 text-sm text-gray-500">Este cliente aún no ha realizado compras.</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Pestaña: Estado de Crédito --}}
            @if($activeTab === 'credito' && $cliente->permite_credito)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Estado de Crédito</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">
                                S/ {{ number_format($cliente->limite_credito, 2) }}
                            </div>
                            <div class="text-sm text-blue-700">Límite de Crédito</div>
                        </div>
                        
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600">
                                S/ {{ number_format($cliente->credito_usado, 2) }}
                            </div>
                            <div class="text-sm text-orange-700">Crédito Usado</div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                S/ {{ number_format($cliente->limite_credito - $cliente->credito_usado, 2) }}
                            </div>
                            <div class="text-sm text-green-700">Crédito Disponible</div>
                        </div>
                    </div>

                    @if($cliente->descuento_porcentaje > 0)
                        <div class="mt-6 bg-purple-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="text-purple-700 font-medium">
                                    Descuento automático: {{ $cliente->descuento_porcentaje }}%
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de edición --}}
    @if($showEditForm)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarEditForm"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                    <livewire:clientes.formulario :cliente="$cliente" :key="'cliente-edit-' . $cliente->id" />
                </div>
            </div>
        </div>
    @endif

    {{-- Mensajes flash --}}
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>
