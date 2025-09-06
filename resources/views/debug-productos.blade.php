<!DOCTYPE html>
<html>
<head>
    <title>Debug Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">🔍 Debug - Productos Module</h1>
        
        <div class="space-y-4">
            <div class="p-4 bg-green-50 rounded">
                <h3 class="font-semibold text-green-800">✅ Ruta funcionando</h3>
                <p class="text-green-600">Esta ruta de prueba está funcionando correctamente</p>
            </div>
            
            <div class="p-4 bg-blue-50 rounded">
                <h3 class="font-semibold text-blue-800">🔍 Información de Debug</h3>
                <ul class="text-blue-600 space-y-1">
                    <li><strong>Usuario autenticado:</strong> {{ auth()->check() ? 'Sí' : 'No' }}</li>
                    <li><strong>Usuario ID:</strong> {{ auth()->id() ?? 'N/A' }}</li>
                    <li><strong>Empresa ID:</strong> {{ auth()->user()?->empresa_id ?? 'N/A' }}</li>
                    <li><strong>Tenant Service Empresa:</strong> {{ app(\App\Services\TenantService::class)->getEmpresaId() ?? 'N/A' }}</li>
                    <li><strong>Livewire Component Exists:</strong> {{ class_exists(\App\Livewire\Productos\Formulario::class) ? 'Sí' : 'No' }}</li>
                    <li><strong>Categorías count:</strong> {{ \App\Models\Categoria::count() }}</li>
                </ul>
            </div>
            
            <div class="flex space-x-4">
                <a href="{{ route('productos.index') }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Ver Catálogo
                </a>
                <a href="{{ route('productos.crear') }}" 
                   class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Probar Livewire Formulario
                </a>
            </div>
        </div>
    </div>
</body>
</html>
