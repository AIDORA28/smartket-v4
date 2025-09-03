@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $producto->nombre }}</h1>
            <p class="text-sm text-gray-600 mt-1">
                @if($producto->codigo_interno)
                    SKU: {{ $producto->codigo_interno }} •
                @endif
                {{ $producto->categoria->icono }} {{ $producto->categoria->nombre }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('productos.edit', $producto) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <span class="mr-2">✏️</span>
                Editar
            </a>
            <a href="{{ route('productos.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <span class="mr-2">←</span>
                Volver
            </a>
        </div>
    </div>

    <!-- Información Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Imagen y Estado -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                @if($producto->imagen_url)
                    <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" 
                         class="w-full h-64 object-cover rounded-lg mb-4">
                @else
                    <div class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                        <span class="text-6xl">📦</span>
                    </div>
                @endif
                
                <div class="space-y-3">
                    <!-- Estado -->
                    @if($producto->activo)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✅ Activo
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            ❌ Inactivo
                        </span>
                    @endif

                    <!-- Categoría -->
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                              style="background-color: {{ $producto->categoria->color }}20; color: {{ $producto->categoria->color }}">
                            {{ $producto->categoria->icono }} {{ $producto->categoria->nombre }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Básica -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nombre</label>
                        <p class="text-gray-900">{{ $producto->nombre }}</p>
                    </div>

                    @if($producto->codigo_interno)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Código Interno (SKU)</label>
                            <p class="text-gray-900 font-mono">{{ $producto->codigo_interno }}</p>
                        </div>
                    @endif

                    @if($producto->codigo_barra)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Código de Barras</label>
                            <p class="text-gray-900 font-mono">{{ $producto->codigo_barra }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="text-sm font-medium text-gray-500">Unidad de Medida</label>
                        <p class="text-gray-900 capitalize">{{ $producto->unidad_medida }}</p>
                    </div>

                    @if($producto->descripcion)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Descripción</label>
                            <p class="text-gray-900">{{ $producto->descripcion }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Precios -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Precios</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">S/ {{ number_format($producto->precio_costo, 2) }}</div>
                        <div class="text-sm text-orange-500">Precio de Costo</div>
                    </div>

                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">S/ {{ number_format($producto->precio_venta, 2) }}</div>
                        <div class="text-sm text-green-500">Precio de Venta</div>
                    </div>

                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        @php
                            $ganancia = $producto->precio_venta - $producto->precio_costo;
                            $margen = $producto->precio_costo > 0 ? (($ganancia / $producto->precio_costo) * 100) : 0;
                        @endphp
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($margen, 1) }}%</div>
                        <div class="text-sm text-blue-500">Margen de Ganancia</div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600">
                        <strong>Ganancia por unidad:</strong> S/ {{ number_format($ganancia, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock e Inventario -->
    @if($producto->maneja_stock)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Control de Stock</h3>
                <button onclick="openStockModal()" 
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <span class="mr-2">📦</span>
                    Ajustar Stock
                </button>
            </div>

            @php $stock = $producto->stocks->first(); @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $stock ? number_format($stock->cantidad_actual, 2) : '0.00' }}
                    </div>
                    <div class="text-sm text-blue-500">Stock Actual</div>
                    <div class="text-xs text-gray-500">{{ $producto->unidad_medida }}</div>
                </div>

                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600">{{ number_format($producto->stock_minimo, 2) }}</div>
                    <div class="text-sm text-orange-500">Stock Mínimo</div>
                    <div class="text-xs text-gray-500">{{ $producto->unidad_medida }}</div>
                </div>

                @if($producto->stock_maximo)
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($producto->stock_maximo, 2) }}</div>
                        <div class="text-sm text-purple-500">Stock Máximo</div>
                        <div class="text-xs text-gray-500">{{ $producto->unidad_medida }}</div>
                    </div>
                @endif

                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">
                        S/ {{ $stock ? number_format($stock->cantidad_actual * $producto->precio_costo, 2) : '0.00' }}
                    </div>
                    <div class="text-sm text-green-500">Valor en Stock</div>
                    <div class="text-xs text-gray-500">Precio de costo</div>
                </div>
            </div>

            <!-- Estado del Stock -->
            @if($stock)
                @php
                    $cantidad = $stock->cantidad_actual;
                    $esStockBajo = $cantidad <= $producto->stock_minimo;
                    $esStockAlto = $producto->stock_maximo && $cantidad >= $producto->stock_maximo;
                @endphp
                
                @if($esStockBajo)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">⚠️</span>
                            <div>
                                <h4 class="font-medium text-red-800">Stock Bajo</h4>
                                <p class="text-sm text-red-600">
                                    El stock actual ({{ number_format($cantidad, 2) }} {{ $producto->unidad_medida }}) 
                                    está por debajo del mínimo recomendado.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($esStockAlto)
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">📈</span>
                            <div>
                                <h4 class="font-medium text-orange-800">Stock Alto</h4>
                                <p class="text-sm text-orange-600">
                                    El stock actual está en el límite máximo recomendado.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">✅</span>
                            <div>
                                <h4 class="font-medium text-green-800">Stock Óptimo</h4>
                                <p class="text-sm text-green-600">
                                    El stock se encuentra en niveles adecuados.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Últimos Movimientos -->
            @if($producto->movimientos->count() > 0)
                <div class="mt-6">
                    <h4 class="font-medium text-gray-900 mb-4">Últimos Movimientos de Stock</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($producto->movimientos->take(5) as $movimiento)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ $movimiento->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if($movimiento->tipo === 'entrada')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ↗️ Entrada
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    ↙️ Salida
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ number_format($movimiento->cantidad, 2) }} {{ $producto->unidad_medida }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500">
                                            {{ $movimiento->motivo ?: 'Sin especificar' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center py-8">
                <span class="text-4xl block mb-4">📋</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Sin Control de Stock</h3>
                <p class="text-gray-500">Este producto no maneja control de inventario</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal para Ajustar Stock -->
@if($producto->maneja_stock)
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Ajustar Stock</h3>
                    <button onclick="closeStockModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="sr-only">Cerrar</span>
                        <span class="text-xl">×</span>
                    </button>
                </div>

                <form action="{{ route('productos.ajustar-stock', $producto) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Movimiento</label>
                            <select name="tipo" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="entrada">➕ Entrada (Agregar stock)</option>
                                <option value="salida">➖ Salida (Reducir stock)</option>
                                <option value="ajuste">🔧 Ajuste (Establecer cantidad exacta)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                            <input type="number" name="cantidad" step="0.01" min="0" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="0.00">
                            <p class="text-xs text-gray-500 mt-1">En {{ $producto->unidad_medida }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                            <input type="text" name="motivo" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Ej: Compra, Venta, Inventario físico...">
                        </div>

                        <div class="bg-blue-50 p-3 rounded-lg">
                            <div class="text-sm text-blue-800">
                                <strong>Stock actual:</strong> {{ $stock ? number_format($stock->cantidad_actual, 2) : '0.00' }} {{ $producto->unidad_medida }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeStockModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            Ajustar Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openStockModal() {
    document.getElementById('stockModal').classList.remove('hidden');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('stockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStockModal();
    }
});
</script>
@endpush
@endif
@endsection
