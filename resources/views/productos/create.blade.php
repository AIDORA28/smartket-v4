@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Producto</h1>
            <p class="text-sm text-gray-600 mt-1">Agrega un nuevo producto a tu catálogo</p>
        </div>
        <a href="{{ route('productos.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <span class="mr-2">←</span>
            Volver
        </a>
    </div>

    <!-- Formulario -->
    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Información Básica -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Información Básica</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Producto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ej: Pan integral 500g">
                    @error('nombre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="codigo_interno" class="block text-sm font-medium text-gray-700 mb-1">
                        Código Interno (SKU)
                    </label>
                    <input type="text" name="codigo_interno" id="codigo_interno" value="{{ old('codigo_interno') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ej: PAN-INT-500">
                    @error('codigo_interno')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="codigo_barra" class="block text-sm font-medium text-gray-700 mb-1">
                        Código de Barras
                    </label>
                    <input type="text" name="codigo_barra" id="codigo_barra" value="{{ old('codigo_barra') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ej: 7501234567890">
                    @error('codigo_barra')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Categoría <span class="text-red-500">*</span>
                    </label>
                    <select name="categoria_id" id="categoria_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->icono }} {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unidad_medida" class="block text-sm font-medium text-gray-700 mb-1">
                        Unidad de Medida <span class="text-red-500">*</span>
                    </label>
                    <select name="unidad_medida" id="unidad_medida" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar unidad</option>
                        <option value="unidad" {{ old('unidad_medida') == 'unidad' ? 'selected' : '' }}>Unidad</option>
                        <option value="kg" {{ old('unidad_medida') == 'kg' ? 'selected' : '' }}>Kilogramo (kg)</option>
                        <option value="gr" {{ old('unidad_medida') == 'gr' ? 'selected' : '' }}>Gramo (gr)</option>
                        <option value="lt" {{ old('unidad_medida') == 'lt' ? 'selected' : '' }}>Litro (lt)</option>
                        <option value="ml" {{ old('unidad_medida') == 'ml' ? 'selected' : '' }}>Mililitro (ml)</option>
                        <option value="docena" {{ old('unidad_medida') == 'docena' ? 'selected' : '' }}>Docena</option>
                        <option value="paquete" {{ old('unidad_medida') == 'paquete' ? 'selected' : '' }}>Paquete</option>
                    </select>
                    @error('unidad_medida')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Descripción detallada del producto...">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">
                        Imagen del Producto
                    </label>
                    <input type="file" name="imagen" id="imagen" accept="image/*"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Formatos soportados: JPG, PNG. Tamaño máximo: 2MB</p>
                    @error('imagen')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Precios -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Precios</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="precio_costo" class="block text-sm font-medium text-gray-700 mb-1">
                        Precio de Costo <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">S/</span>
                        <input type="number" name="precio_costo" id="precio_costo" value="{{ old('precio_costo') }}" 
                               step="0.01" min="0" required
                               class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                    </div>
                    @error('precio_costo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="precio_venta" class="block text-sm font-medium text-gray-700 mb-1">
                        Precio de Venta <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">S/</span>
                        <input type="number" name="precio_venta" id="precio_venta" value="{{ old('precio_venta') }}" 
                               step="0.01" min="0" required
                               class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                    </div>
                    @error('precio_venta')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <div id="margen-info" class="mt-4 p-4 bg-blue-50 rounded-lg hidden">
                        <div class="flex items-center text-blue-800">
                            <span class="text-lg mr-2">💰</span>
                            <div>
                                <div class="font-medium">Margen de ganancia: <span id="margen-porcentaje">0%</span></div>
                                <div class="text-sm">Ganancia por unidad: S/ <span id="ganancia-unidad">0.00</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Control de Stock -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Control de Stock</h3>
            
            <div class="space-y-6">
                <div class="flex items-center">
                    <input type="checkbox" name="maneja_stock" id="maneja_stock" value="1" 
                           {{ old('maneja_stock') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="maneja_stock" class="ml-2 block text-sm text-gray-900">
                        Este producto maneja control de stock
                    </label>
                </div>

                <div id="stock-fields" class="grid grid-cols-1 md:grid-cols-3 gap-6 {{ old('maneja_stock') ? '' : 'hidden' }}">
                    <div>
                        <label for="stock_actual" class="block text-sm font-medium text-gray-700 mb-1">
                            Stock Inicial
                        </label>
                        <input type="number" name="stock_actual" id="stock_actual" value="{{ old('stock_actual', 0) }}" 
                               step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                        @error('stock_actual')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_minimo" class="block text-sm font-medium text-gray-700 mb-1">
                            Stock Mínimo
                        </label>
                        <input type="number" name="stock_minimo" id="stock_minimo" value="{{ old('stock_minimo', 0) }}" 
                               step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                        @error('stock_minimo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_maximo" class="block text-sm font-medium text-gray-700 mb-1">
                            Stock Máximo
                        </label>
                        <input type="number" name="stock_maximo" id="stock_maximo" value="{{ old('stock_maximo') }}" 
                               step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                        @error('stock_maximo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Estado</h3>
            
            <div class="flex items-center">
                <input type="checkbox" name="activo" id="activo" value="1" 
                       {{ old('activo', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="activo" class="ml-2 block text-sm text-gray-900">
                    Producto activo (disponible para venta)
                </label>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('productos.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Crear Producto
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const manejaStock = document.getElementById('maneja_stock');
    const stockFields = document.getElementById('stock-fields');
    const precioCosto = document.getElementById('precio_costo');
    const precioVenta = document.getElementById('precio_venta');
    const margenInfo = document.getElementById('margen-info');
    const margenPorcentaje = document.getElementById('margen-porcentaje');
    const gananciaUnidad = document.getElementById('ganancia-unidad');

    // Control de stock
    manejaStock.addEventListener('change', function() {
        if (this.checked) {
            stockFields.classList.remove('hidden');
        } else {
            stockFields.classList.add('hidden');
        }
    });

    // Cálculo de margen
    function calcularMargen() {
        const costo = parseFloat(precioCosto.value) || 0;
        const venta = parseFloat(precioVenta.value) || 0;
        
        if (costo > 0 && venta > 0) {
            const ganancia = venta - costo;
            const margen = ((ganancia / costo) * 100).toFixed(1);
            
            margenPorcentaje.textContent = margen + '%';
            gananciaUnidad.textContent = ganancia.toFixed(2);
            margenInfo.classList.remove('hidden');
        } else {
            margenInfo.classList.add('hidden');
        }
    }

    precioCosto.addEventListener('input', calcularMargen);
    precioVenta.addEventListener('input', calcularMargen);
});
</script>
@endpush
@endsection
