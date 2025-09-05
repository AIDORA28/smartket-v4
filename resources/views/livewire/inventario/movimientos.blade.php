@section('title', 'Movimientos de Inventario')

<div class="space-y-6">
    <!-- Header con estadísticas -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Movimientos de Inventario</h1>
            
            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Movimientos -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Movimientos</p>
                            <p class="text-3xl font-bold">{{ number_format($estadisticas['total_movimientos']) }}</p>
                        </div>
                        <div class="text-blue-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 6L18.29 8.29L13.41 13.17L9.41 9.17L2 16.59L3.41 18L9.41 12L13.41 16L19.59 9.83L22 12.24V6H16Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Entradas del Mes -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Entradas (Mes)</p>
                            <p class="text-3xl font-bold">{{ $estadisticas['entradas_mes'] }}</p>
                        </div>
                        <div class="text-green-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15,20A1,1 0 0,0 16,19V4H8A1,1 0 0,0 7,5V16H5V5A3,3 0 0,1 8,2H19A1,1 0 0,1 20,3V19A3,3 0 0,1 17,22H8A3,3 0 0,1 5,19V18H17A1,1 0 0,0 18,17V16H17A2,2 0 0,1 15,14V20M9,6H14V8H9V6M9,10H14V12H9V10M9,14H12V16H9V14Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Salidas del Mes -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm font-medium">Salidas (Mes)</p>
                            <p class="text-3xl font-bold">{{ $estadisticas['salidas_mes'] }}</p>
                        </div>
                        <div class="text-red-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ajustes del Mes -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">Ajustes (Mes)</p>
                            <p class="text-3xl font-bold">{{ $estadisticas['ajustes_mes'] }}</p>
                        </div>
                        <div class="text-yellow-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.97 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.04 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
                <!-- Búsqueda -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Producto</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               placeholder="Nombre o código del producto..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tipo de Movimiento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select wire:model.live="tipoFiltro" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="entrada">🟢 Entrada</option>
                        <option value="salida">🔴 Salida</option>
                        <option value="ajuste">🟡 Ajuste</option>
                    </select>
                </div>

                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select wire:model.live="categoriaFiltro" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->icono }} {{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha Desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                    <input type="date" 
                           wire:model.live="fechaDesde"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Fecha Hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                    <input type="date" 
                           wire:model.live="fechaHasta"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-wrap gap-3 mt-4">
                <a href="{{ route('inventario.index') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                    Volver a Inventario
                </a>
                
                <button onclick="window.print()" 
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- Timeline de movimientos -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            @if($movimientos->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($movimientos as $movimiento)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start space-x-4">
                                <!-- Icono del tipo de movimiento -->
                                <div class="flex-shrink-0">
                                    @switch($movimiento->tipo_movimiento)
                                        @case('entrada')
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('salida')
                                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('ajuste')
                                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.97 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.04 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                                                </svg>
                                            </div>
                                            @break
                                    @endswitch
                                </div>

                                <!-- Información del movimiento -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                {{ $movimiento->producto->nombre ?? 'Producto eliminado' }}
                                            </h3>
                                            @switch($movimiento->tipo_movimiento)
                                                @case('entrada')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        🟢 Entrada
                                                    </span>
                                                    @break
                                                @case('salida')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        🔴 Salida
                                                    </span>
                                                    @break
                                                @case('ajuste')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        🟡 Ajuste
                                                    </span>
                                                    @break
                                            @endswitch
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                        </div>
                                    </div>

                                    <div class="mt-2 grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <!-- Cantidad -->
                                        <div>
                                            <p class="text-sm text-gray-500">Cantidad</p>
                                            <p class="text-lg font-semibold {{ $movimiento->tipo_movimiento === 'entrada' ? 'text-green-600' : ($movimiento->tipo_movimiento === 'salida' ? 'text-red-600' : 'text-yellow-600') }}">
                                                {{ $movimiento->tipo_movimiento === 'salida' ? '-' : '+' }}{{ number_format($movimiento->cantidad, 2) }}
                                                {{ $movimiento->producto->unidad_medida ?? 'unid' }}
                                            </p>
                                        </div>

                                        <!-- Stock Anterior/Posterior -->
                                        <div>
                                            <p class="text-sm text-gray-500">Stock: Anterior → Posterior</p>
                                            <p class="text-lg font-medium text-gray-700">
                                                {{ number_format($movimiento->stock_anterior, 2) }} → 
                                                <span class="font-semibold">{{ number_format($movimiento->stock_posterior, 2) }}</span>
                                            </p>
                                        </div>

                                        <!-- Costo -->
                                        <div>
                                            <p class="text-sm text-gray-500">Costo Unitario</p>
                                            <p class="text-lg font-medium text-gray-900">
                                                S/ {{ number_format($movimiento->costo_unitario, 2) }}
                                            </p>
                                        </div>

                                        <!-- Usuario -->
                                        <div>
                                            <p class="text-sm text-gray-500">Usuario</p>
                                            <p class="text-lg font-medium text-gray-900">
                                                {{ $movimiento->usuario->name ?? 'Sistema' }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($movimiento->observaciones)
                                        <div class="mt-3">
                                            <p class="text-sm text-gray-500">Observaciones:</p>
                                            <p class="text-sm text-gray-700 italic">{{ $movimiento->observaciones }}</p>
                                        </div>
                                    @endif

                                    @if($movimiento->referencia_tipo && $movimiento->referencia_id)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                Ref: {{ ucfirst($movimiento->referencia_tipo) }} #{{ $movimiento->referencia_id }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">No hay movimientos de inventario</h3>
                        <p class="text-gray-500 mb-4">Los movimientos aparecerán aquí cuando realices ajustes de stock</p>
                        <a href="{{ route('inventario.index') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Gestionar Inventario
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Paginación -->
        @if($movimientos->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $movimientos->links() }}
            </div>
        @endif
    </div>
</div>
