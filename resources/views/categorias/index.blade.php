@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categorías</h1>
            <p class="text-sm text-gray-600 mt-1">
                Organiza tus productos por categorías - {{ $empresa->nombre }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('productos.index') }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <span class="text-lg mr-2">📦</span>
                Productos
            </a>
            <button onclick="openCreateModal()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <span class="text-lg mr-2">➕</span>
                Nueva Categoría
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-md">
                    <span class="text-2xl">🏷️</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $categorias->count() }}</div>
                    <div class="text-sm text-gray-500">Total Categorías</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-md">
                    <span class="text-2xl">📦</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $categorias->sum('productos_count') }}</div>
                    <div class="text-sm text-gray-500">Total Productos</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-md">
                    <span class="text-2xl">✅</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $categorias->where('activa', true)->count() }}</div>
                    <div class="text-sm text-gray-500">Categorías Activas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Categorías -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($categorias->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                @foreach($categorias as $categoria)
                    <div class="relative bg-white border-2 rounded-lg p-6 hover:shadow-md transition-shadow"
                         style="border-color: {{ $categoria->color }}40;">
                        <!-- Menú de opciones -->
                        <div class="absolute top-4 right-4">
                            <div class="relative">
                                <button onclick="toggleMenu('menu-{{ $categoria->id }}')" 
                                        class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <span class="text-lg">⋮</span>
                                </button>
                                <div id="menu-{{ $categoria->id }}" 
                                     class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border">
                                    <div class="py-1">
                                        <button onclick="editCategoria({{ $categoria->id }}, '{{ $categoria->nombre }}', '{{ $categoria->icono }}', '{{ $categoria->color }}', {{ $categoria->activa ? 'true' : 'false' }})"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            ✏️ Editar
                                        </button>
                                        @if($categoria->productos_count == 0)
                                            <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" 
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')" class="w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                    🗑️ Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido de la categoría -->
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 style="background-color: {{ $categoria->color }}20;">
                                <span class="text-3xl">{{ $categoria->icono }}</span>
                            </div>
                            
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $categoria->nombre }}</h3>
                            
                            <div class="space-y-2">
                                <div class="text-sm text-gray-500">
                                    {{ $categoria->productos_count }} {{ $categoria->productos_count == 1 ? 'producto' : 'productos' }}
                                </div>
                                
                                @if($categoria->activa)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactiva
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Ver productos de la categoría -->
                        @if($categoria->productos_count > 0)
                            <div class="mt-4">
                                <a href="{{ route('productos.index', ['categoria_id' => $categoria->id]) }}" 
                                   class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Ver Productos
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <span class="text-6xl block mb-4">🏷️</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay categorías registradas</h3>
                <p class="text-gray-500 mb-6">Comienza creando tu primera categoría para organizar tus productos</p>
                <button onclick="openCreateModal()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <span class="mr-2">➕</span>
                    Crear Primera Categoría
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Modal para Crear/Editar Categoría -->
<div id="categoriaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form id="categoriaForm" method="POST">
                @csrf
                <div id="method-field"></div>
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 id="modal-title" class="text-lg font-medium text-gray-900">Nueva Categoría</h3>
                        <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">Cerrar</span>
                            <span class="text-xl">×</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre" id="nombre" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Ej: Panadería, Bebidas, Lácteos...">
                        </div>

                        <div>
                            <label for="icono" class="block text-sm font-medium text-gray-700 mb-1">
                                Icono <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="text" name="icono" id="icono" required maxlength="2"
                                       class="w-20 text-center text-2xl rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="🍞">
                                <div class="text-sm text-gray-500">
                                    Usa un emoji que represente la categoría
                                </div>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <button type="button" onclick="selectIcon('🍞')" class="p-2 border rounded hover:bg-gray-50">🍞</button>
                                <button type="button" onclick="selectIcon('🥤')" class="p-2 border rounded hover:bg-gray-50">🥤</button>
                                <button type="button" onclick="selectIcon('🥛')" class="p-2 border rounded hover:bg-gray-50">🥛</button>
                                <button type="button" onclick="selectIcon('🍰')" class="p-2 border rounded hover:bg-gray-50">🍰</button>
                                <button type="button" onclick="selectIcon('🧀')" class="p-2 border rounded hover:bg-gray-50">🧀</button>
                                <button type="button" onclick="selectIcon('🥓')" class="p-2 border rounded hover:bg-gray-50">🥓</button>
                                <button type="button" onclick="selectIcon('🍫')" class="p-2 border rounded hover:bg-gray-50">🍫</button>
                                <button type="button" onclick="selectIcon('🧽')" class="p-2 border rounded hover:bg-gray-50">🧽</button>
                            </div>
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                                Color <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="color" id="color" required
                                       class="w-16 h-10 rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <input type="text" id="color-hex" readonly
                                       class="flex-1 rounded-md border-gray-300 shadow-sm bg-gray-50 text-gray-600">
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <button type="button" onclick="selectColor('#3B82F6')" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #3B82F6;"></button>
                                <button type="button" onclick="selectColor('#10B981')" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #10B981;"></button>
                                <button type="button" onclick="selectColor('#F59E0B')" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #F59E0B;"></button>
                                <button type="button" onclick="selectColor('#EF4444')" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #EF4444;"></button>
                                <button type="button" onclick="selectColor('#8B5CF6')" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #8B5CF6;"></button>
                                <button type="button" onclick="selectColor('#EC4899')" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #EC4899;"></button>
                                <button type="button" onclick="selectColor('#6B7280')" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #6B7280;"></button>
                                <button type="button" onclick="selectColor('#14B8A6')" class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: #14B8A6;"></button>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="activa" id="activa" value="1" checked
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="activa" class="ml-2 block text-sm text-gray-900">
                                Categoría activa
                            </label>
                        </div>

                        <!-- Vista previa -->
                        <div class="border-t pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vista Previa</label>
                            <div class="flex items-center justify-center p-4 border rounded-lg">
                                <div class="text-center">
                                    <div id="preview-icon-container" class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center"
                                         style="background-color: #3B82F620;">
                                        <span id="preview-icon" class="text-2xl">🍞</span>
                                    </div>
                                    <div id="preview-nombre" class="text-sm font-medium text-gray-900">Categoría</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            <span id="submit-text">Crear Categoría</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let isEditing = false;
let editingId = null;

// Abrir menús
function toggleMenu(menuId) {
    // Cerrar otros menús
    document.querySelectorAll('[id^="menu-"]').forEach(menu => {
        if (menu.id !== menuId) {
            menu.classList.add('hidden');
        }
    });
    
    // Toggle el menú actual
    const menu = document.getElementById(menuId);
    menu.classList.toggle('hidden');
}

// Cerrar menús al hacer clic fuera
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick^="toggleMenu"]') && !e.target.closest('[id^="menu-"]')) {
        document.querySelectorAll('[id^="menu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

function openCreateModal() {
    isEditing = false;
    editingId = null;
    document.getElementById('modal-title').textContent = 'Nueva Categoría';
    document.getElementById('submit-text').textContent = 'Crear Categoría';
    document.getElementById('categoriaForm').action = '{{ route("categorias.store") }}';
    document.getElementById('method-field').innerHTML = '';
    
    // Limpiar formulario
    document.getElementById('nombre').value = '';
    document.getElementById('icono').value = '🍞';
    document.getElementById('color').value = '#3B82F6';
    document.getElementById('color-hex').value = '#3B82F6';
    document.getElementById('activa').checked = true;
    
    updatePreview();
    document.getElementById('categoriaModal').classList.remove('hidden');
}

function editCategoria(id, nombre, icono, color, activa) {
    isEditing = true;
    editingId = id;
    document.getElementById('modal-title').textContent = 'Editar Categoría';
    document.getElementById('submit-text').textContent = 'Actualizar Categoría';
    document.getElementById('categoriaForm').action = `/categorias/${id}`;
    document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    // Llenar formulario
    document.getElementById('nombre').value = nombre;
    document.getElementById('icono').value = icono;
    document.getElementById('color').value = color;
    document.getElementById('color-hex').value = color;
    document.getElementById('activa').checked = activa;
    
    updatePreview();
    document.getElementById('categoriaModal').classList.remove('hidden');
    
    // Cerrar menú
    document.getElementById(`menu-${id}`).classList.add('hidden');
}

function closeModal() {
    document.getElementById('categoriaModal').classList.add('hidden');
}

function selectIcon(icon) {
    document.getElementById('icono').value = icon;
    updatePreview();
}

function selectColor(color) {
    document.getElementById('color').value = color;
    document.getElementById('color-hex').value = color;
    updatePreview();
}

function updatePreview() {
    const nombre = document.getElementById('nombre').value || 'Categoría';
    const icono = document.getElementById('icono').value || '🍞';
    const color = document.getElementById('color').value || '#3B82F6';
    
    document.getElementById('preview-nombre').textContent = nombre;
    document.getElementById('preview-icon').textContent = icono;
    document.getElementById('preview-icon-container').style.backgroundColor = color + '20';
    document.getElementById('color-hex').value = color;
}

// Event listeners
document.getElementById('nombre').addEventListener('input', updatePreview);
document.getElementById('icono').addEventListener('input', updatePreview);
document.getElementById('color').addEventListener('input', updatePreview);

// Cerrar modal al hacer clic fuera
document.getElementById('categoriaModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Inicializar vista previa
updatePreview();
</script>
@endpush
@endsection
