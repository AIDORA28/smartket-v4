<div class="min-h-screen bg-gray-50">
    <!-- Header del POS -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">🛒 POS - Punto de Venta</h1>
                    @if($empresa)
                        <p class="text-sm text-gray-500">{{ $empresa->nombre }}</p>
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    @if($clienteSeleccionado)
                        <div class="bg-blue-100 px-3 py-1 rounded-lg">
                            <span class="text-blue-800 text-sm font-medium">
                                Cliente: {{ $clientes->find($clienteSeleccionado)->nombre ?? 'Anónimo' }}
                            </span>
                        </div>
                    @endif
                    <button wire:click="limpiarCarrito" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                        🗑️ Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="flex h-[calc(100vh-4rem)]">
        <!-- Panel de Productos (Izquierda) -->
        <div class="flex-1 p-6">
            <!-- Filtros -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Búsqueda -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar productos</label>
                        <input type="text" 
                               wire:model.live="busqueda" 
                               placeholder="Nombre, código interno o código de barras..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <!-- Categorías -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                        <select wire:model.live="categoriaSeleccionada" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grid de Productos -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                @if($productos->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($productos as $producto)
                            <div wire:click="agregarAlCarrito({{ $producto->id }})" 
                                 class="relative bg-white border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:border-blue-500 hover:shadow-lg transition-all duration-200 group">
                                
                                <!-- Imagen del producto (placeholder) -->
                                <div class="bg-gray-100 rounded-lg h-24 flex items-center justify-center mb-3 group-hover:bg-gray-200 transition-colors">
                                    <span class="text-3xl">📦</span>
                                </div>
                                
                                <!-- Información del producto -->
                                <div class="text-center">
                                    <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2">{{ $producto->nombre }}</h3>
                                    <p class="text-xs text-gray-500 mb-2">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>
                                    <p class="text-lg font-bold text-blue-600">S/ {{ number_format($producto->precio_venta, 2) }}</p>
                                    
                                    @if($producto->maneja_stock)
                                        <p class="text-xs text-gray-400 mt-1">Stock: {{ $producto->getStockTotal() }}</p>
                                    @endif
                                </div>
                                
                                <!-- Indicador de hover simple -->
                                <div class="absolute top-2 right-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    +
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">📦</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay productos</h3>
                        <p class="text-gray-500">No se encontraron productos con los filtros seleccionados</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Panel del Carrito (Derecha) -->
        <div class="w-96 bg-white border-l border-gray-200 flex flex-col">
            <!-- Header del carrito -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">🛒 Carrito de Compras</h2>
                
                <!-- Cliente -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                    <select wire:model="clienteSeleccionado" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Cliente anónimo</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Items del carrito -->
            <div class="flex-1 overflow-y-auto p-6">
                @if(!empty($carrito))
                    <div class="space-y-4">
                        @foreach($carrito as $productoId => $item)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $item['nombre'] }}</h4>
                                    <button wire:click="removerDelCarrito({{ $productoId }})" 
                                            class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="actualizarCantidad({{ $productoId }}, {{ $item['cantidad'] - 1 }})" 
                                                class="bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full flex items-center justify-center text-sm">
                                            -
                                        </button>
                                        <span class="w-8 text-center font-medium">{{ $item['cantidad'] }}</span>
                                        <button wire:click="actualizarCantidad({{ $productoId }}, {{ $item['cantidad'] + 1 }})" 
                                                class="bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full flex items-center justify-center text-sm">
                                            +
                                        </button>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">S/ {{ number_format($item['precio'], 2) }} c/u</p>
                                        <p class="font-semibold">S/ {{ number_format($item['subtotal'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🛒</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Carrito vacío</h3>
                        <p class="text-gray-500">Selecciona productos para agregarlos al carrito</p>
                    </div>
                @endif
            </div>

            <!-- Total y botón de pago -->
            @if(!empty($carrito))
                <div class="p-6 border-t border-gray-200 bg-gray-50">
                    <!-- Total -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center text-lg font-semibold">
                            <span>Total:</span>
                            <span class="text-2xl text-blue-600">S/ {{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                    
                    <!-- Botón de pago -->
                    <button wire:click="abrirModalPago" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200">
                        💳 Procesar Pago
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Pago -->
    @if($mostrarModalPago)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💳 Procesar Pago</h3>
                    
                    <!-- Total -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Total a pagar:</p>
                            <p class="text-3xl font-bold text-blue-600">S/ {{ number_format($total, 2) }}</p>
                        </div>
                    </div>
                    
                    <!-- Método de pago -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Método de pago</label>
                        <select wire:model="metodoPago" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="efectivo">💵 Efectivo</option>
                            <option value="tarjeta">💳 Tarjeta</option>
                            <option value="transferencia">🏦 Transferencia</option>
                        </select>
                    </div>
                    
                    @if($metodoPago === 'efectivo')
                        <!-- Monto recibido -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monto recibido</label>
                            <input type="number" 
                                   wire:model.live="montoRecibido" 
                                   step="0.01" 
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <!-- Cambio -->
                        <div class="mb-6">
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Cambio:</p>
                                    <p class="text-2xl font-bold text-green-600">S/ {{ number_format($cambio, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Botones -->
                    <div class="flex space-x-4">
                        <button wire:click="$set('mostrarModalPago', false)" 
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg">
                            Cancelar
                        </button>
                        <button wire:click="procesarVenta" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                            ✅ Confirmar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Mensajes Flash -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Estilos internos -->
    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>
</div>
