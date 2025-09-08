<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductManagementController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientControllerSimple;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rutas públicas
Route::get('/', [PublicController::class, 'landing'])->name('landing');
Route::get('/precios', [PublicController::class, 'precios'])->name('precios');
Route::get('/caracteristicas', [PublicController::class, 'caracteristicas'])->name('caracteristicas');

Route::middleware(['auth', 'verified', 'empresa.scope'])->group(function () {
    
    // Dashboard principal con React + Inertia
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas de gestión de tenant
    Route::prefix('tenant')->name('tenant.')->group(function () {
        Route::post('/switch-empresa', [TenantController::class, 'switchEmpresa'])->name('switch-empresa');
        Route::post('/switch-sucursal', [TenantController::class, 'switchSucursal'])->name('switch-sucursal');
    });
    
    // Módulo de Productos e Inventario
    // Productos con React + Inertia - GESTIÓN COMPLETA CON DATOS REALES
    Route::get('productos', [ProductManagementController::class, 'index'])->name('productos.index');
    Route::post('productos', [ProductController::class, 'store'])->name('productos.store');
    Route::put('productos/{id}', [ProductController::class, 'update'])->name('productos.update');
    Route::delete('productos/{id}', [ProductController::class, 'destroy'])->name('productos.destroy');
    Route::post('productos/{producto}/ajustar-stock', [ProductoController::class, 'ajustarStock'])->name('productos.ajustar-stock');
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    
    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/movements', [InventoryController::class, 'movements'])->name('movements');
        Route::post('/adjust-stock', [InventoryController::class, 'adjustStock'])->name('adjust-stock');
        Route::get('/transferencias', [PlaceholderController::class, 'show'])
            ->defaults('module', 'transferencias')
            ->name('transferencias');
    });
    
    // Módulo de Ventas y POS con React + Inertia
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/sale', [PosController::class, 'processSale'])->name('sale');
    });
    
    Route::prefix('ventas')->name('ventas.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/{id}', [SaleController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SaleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SaleController::class, 'update'])->name('update');
        Route::delete('/{id}', [SaleController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/cancel', [SaleController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/complete', [SaleController::class, 'complete'])->name('complete');
    });
    
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', [ClientControllerSimple::class, 'index'])->name('index');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{id}', [ClientController::class, 'show'])->name('show');
        Route::put('/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy');
    });
    
    // Módulo de Caja
    Route::prefix('caja')->name('caja.')->group(function () {
        Route::get('/', [PlaceholderController::class, 'show'])
            ->defaults('module', 'caja')
            ->name('index');
        Route::post('/abrir', function () {
            return redirect()->back()->with('success', 'Caja abierta exitosamente');
        })->name('abrir');
        Route::post('/cerrar', function () {
            return redirect()->back()->with('success', 'Caja cerrada exitosamente');
        })->name('cerrar');
    });
    
    // Módulo de Compras
    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('/', [PlaceholderController::class, 'show'])
            ->defaults('module', 'compras')
            ->name('index');
        Route::get('/create', [PlaceholderController::class, 'show'])
            ->defaults('module', 'compras')
            ->name('create');
    });
    
    Route::prefix('proveedores')->name('proveedores.')->group(function () {
        Route::get('/', [PlaceholderController::class, 'show'])
            ->defaults('module', 'proveedores')
            ->name('index');
        Route::get('/create', [PlaceholderController::class, 'show'])
            ->defaults('module', 'proveedores')
            ->name('create');
    });
    
    // Módulo de Lotes y Vencimientos
    Route::prefix('lotes')->name('lotes.')->group(function () {
        Route::get('/', [PlaceholderController::class, 'show'])
            ->defaults('module', 'lotes')
            ->name('index');
        Route::get('/vencimientos', [PlaceholderController::class, 'show'])
            ->defaults('module', 'lotes')
            ->name('vencimientos');
        Route::get('/trazabilidad', [PlaceholderController::class, 'show'])
            ->defaults('module', 'lotes')
            ->name('trazabilidad');
    });
    
    // Módulo de Reportes y Analytics con React + Inertia
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });
    
    // Módulo de Configuraciones con React + Inertia
    Route::prefix('configuraciones')->name('configuraciones.')->group(function () {
        Route::get('/', [ConfigurationController::class, 'index'])->name('index');
        Route::put('/empresa', [ConfigurationController::class, 'updateEmpresa'])->name('empresa.update');
        Route::post('/usuarios', [ConfigurationController::class, 'createUser'])->name('usuarios.store');
        Route::put('/usuarios/{id}', [ConfigurationController::class, 'updateUser'])->name('usuarios.update');
        Route::delete('/usuarios/{id}', [ConfigurationController::class, 'deleteUser'])->name('usuarios.destroy');
        Route::post('/sucursales', [ConfigurationController::class, 'createSucursal'])->name('sucursales.store');
        Route::put('/sucursales/{id}', [ConfigurationController::class, 'updateSucursal'])->name('sucursales.update');
        Route::delete('/sucursales/{id}', [ConfigurationController::class, 'deleteSucursal'])->name('sucursales.destroy');
        Route::put('/features/{id}', [ConfigurationController::class, 'updateFeature'])->name('features.update');
    });
    
    // Módulo de Administración (Solo admins)
    Route::prefix('admin')->name('admin.')->middleware('can:admin')->group(function () {
        Route::prefix('empresas')->name('empresas.')->group(function () {
            Route::get('/', [PlaceholderController::class, 'show'])
                ->defaults('module', 'admin-empresas')
                ->name('index');
        });
        
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [PlaceholderController::class, 'show'])
                ->defaults('module', 'admin-usuarios')
                ->name('index');
        });
        
        Route::prefix('feature-flags')->name('feature-flags.')->group(function () {
            Route::get('/', [PlaceholderController::class, 'show'])
                ->defaults('module', 'feature-flags')
                ->name('index');
        });
    });
    
});

// Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
