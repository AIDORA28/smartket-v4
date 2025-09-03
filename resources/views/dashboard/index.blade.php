@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Bienvenido a SmartKet - {{ $empresa->nombre }}
                    @if($sucursal)
                        · {{ $sucursal->nombre }}
                    @endif
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">{{ now()->format('d/m/Y H:i') }}</div>
                <div class="text-xs text-gray-400">Plan: {{ $empresa->plan->nombre }}</div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Ventas Hoy -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-md">
                    <span class="text-2xl">💰</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">S/ {{ number_format($stats['ventas_hoy'], 2) }}</div>
                    <div class="text-sm text-gray-500">Ventas Hoy</div>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-md">
                    <span class="text-2xl">📦</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_productos']) }}</div>
                    <div class="text-sm text-gray-500">Total Productos</div>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-md">
                    <span class="text-2xl">⚠️</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['productos_stock_bajo'] }}</div>
                    <div class="text-sm text-gray-500">Stock Bajo</div>
                </div>
            </div>
        </div>

        <!-- Sucursales -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-md">
                    <span class="text-2xl">🏪</span>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['sucursales_activas'] }}</div>
                    <div class="text-sm text-gray-500">Sucursales</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            
            <!-- POS -->
            @if($features['pos'])
                <a href="{{ route('pos.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <span class="text-3xl mb-2">🛒</span>
                    <span class="text-sm font-medium text-gray-900">Nueva Venta</span>
                </a>
            @else
                <div class="flex flex-col items-center p-4 rounded-lg border border-gray-200 bg-gray-50 opacity-50">
                    <span class="text-3xl mb-2">🛒</span>
                    <span class="text-sm font-medium text-gray-500">Nueva Venta</span>
                    <span class="text-xs text-gray-400">Próximamente</span>
                </div>
            @endif

            <!-- Productos -->
            <a href="{{ route('productos.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                <span class="text-3xl mb-2">📦</span>
                <span class="text-sm font-medium text-gray-900">Productos</span>
            </a>

            <!-- Inventario -->
            @if($features['inventario_avanzado'])
                <a href="{{ route('inventario.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <span class="text-3xl mb-2">📊</span>
                    <span class="text-sm font-medium text-gray-900">Inventario</span>
                </a>
            @else
                <div class="flex flex-col items-center p-4 rounded-lg border border-gray-200 bg-gray-50 opacity-50">
                    <span class="text-3xl mb-2">📊</span>
                    <span class="text-sm font-medium text-gray-500">Inventario</span>
                    <span class="text-xs text-gray-400">Próximamente</span>
                </div>
            @endif

            <!-- Reportes -->
            @if($features['reportes'])
                <a href="#" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <span class="text-3xl mb-2">📈</span>
                    <span class="text-sm font-medium text-gray-900">Reportes</span>
                </a>
            @else
                <div class="flex flex-col items-center p-4 rounded-lg border border-gray-200 bg-gray-50 opacity-50">
                    <span class="text-3xl mb-2">📈</span>
                    <span class="text-sm font-medium text-gray-500">Reportes</span>
                    <span class="text-xs text-gray-400">Próximamente</span>
                </div>
            @endif

            <!-- Configuración -->
            <a href="{{ route('configuracion.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                <span class="text-3xl mb-2">⚙️</span>
                <span class="text-sm font-medium text-gray-900">Configuración</span>
            </a>

            <!-- Facturación Electrónica -->
            @if($features['facturacion_electronica'])
                <a href="#" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <span class="text-3xl mb-2">📄</span>
                    <span class="text-sm font-medium text-gray-900">Facturación</span>
                </a>
            @else
                <div class="flex flex-col items-center p-4 rounded-lg border border-gray-200 bg-gray-50 opacity-50">
                    <span class="text-3xl mb-2">📄</span>
                    <span class="text-sm font-medium text-gray-500">Facturación</span>
                    <span class="text-xs text-gray-400">Próximamente</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Info de empresa y sucursal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Información de la Empresa -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de la Empresa</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Nombre:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $empresa->nombre }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">RUC:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $empresa->ruc }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Plan:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $empresa->plan->nombre }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Vence:</span>
                    <span class="text-sm font-medium text-gray-900">{{ now()->addDays(30)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Información de la Sucursal -->
        @if($sucursal)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sucursal Actual</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Nombre:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $sucursal->nombre }}</span>
                </div>
                @if($sucursal->direccion)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Dirección:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $sucursal->direccion }}</span>
                </div>
                @endif
                @if($sucursal->telefono)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Teléfono:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $sucursal->telefono }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Principal:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $sucursal->es_principal ? 'Sí' : 'No' }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
