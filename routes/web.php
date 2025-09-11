<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\Inventory\CategoriaController;
use App\Http\Controllers\Core\UserWebController;
use App\Http\Controllers\Core\CompanySettingsController;
use App\Http\Controllers\Core\OrganizationBrandingController;
use App\Http\Controllers\Core\CompanyAnalyticsController;
use App\Http\Controllers\Core\BranchManagementController;
use App\Http\Controllers\Core\BranchTransferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientControllerSimple;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Sales\SaleController;
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
    Route::get('productos', [ProductController::class, 'index'])->name('productos.index');
    Route::get('productos/{id}', [ProductController::class, 'show'])->name('productos.show');
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
    
    // Módulo de Caja y Sesiones
    Route::prefix('cajas')->name('cajas.')->group(function () {
        Route::get('/', [PlaceholderController::class, 'show'])
            ->defaults('module', 'cajas')
            ->name('index');
        Route::post('/abrir-sesion', function () {
            return redirect()->back()->with('success', 'Sesión de caja abierta exitosamente');
        })->name('abrir-sesion');
        Route::post('/cerrar-sesion', function () {
            return redirect()->back()->with('success', 'Sesión de caja cerrada exitosamente');
        })->name('cerrar-sesion');
        Route::get('/movimientos', [PlaceholderController::class, 'show'])
            ->defaults('module', 'cajas')
            ->name('movimientos');
        Route::get('/arqueo', [PlaceholderController::class, 'show'])
            ->defaults('module', 'cajas')
            ->name('arqueo');
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
        Route::post('/', function () {
            return redirect()->back()->with('success', 'Lote creado exitosamente');
        })->name('store');
        Route::put('/{id}', function () {
            return redirect()->back()->with('success', 'Lote actualizado exitosamente');
        })->name('update');
    });
    
    // Módulo de Reportes y Analytics con React + Inertia
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });
    
    // Módulo Core - User Management
    Route::prefix('core')->name('core.')->group(function () {
        // Phase 2: User Management (Completed)
        Route::resource('users', UserWebController::class);
        Route::post('/switch-empresa', [TenantController::class, 'switchEmpresa'])->name('switch-empresa');
        Route::post('/switch-sucursal', [TenantController::class, 'switchSucursal'])->name('switch-sucursal');
        
        // Phase 3: Company & Branch Management
        Route::prefix('company')->name('company.')->group(function () {
            // Company Settings Management
            Route::get('/settings', [CompanySettingsController::class, 'index'])->name('settings.index');
            Route::get('/settings/create', [CompanySettingsController::class, 'create'])->name('settings.create');
            Route::post('/settings', [CompanySettingsController::class, 'store'])->name('settings.store');
            Route::get('/settings/{section}', [CompanySettingsController::class, 'show'])->name('settings.show');
            Route::get('/settings/{section}/edit', [CompanySettingsController::class, 'edit'])->name('settings.edit');
            Route::put('/settings/{section}', [CompanySettingsController::class, 'update'])->name('settings.update');
            Route::delete('/settings/{section}', [CompanySettingsController::class, 'destroy'])->name('settings.destroy');
            
            // Organization Branding Management
            Route::get('/branding', [OrganizationBrandingController::class, 'index'])->name('branding.index');
            Route::get('/branding/create', [OrganizationBrandingController::class, 'create'])->name('branding.create');
            Route::post('/branding', [OrganizationBrandingController::class, 'store'])->name('branding.store');
            Route::get('/branding/edit', [OrganizationBrandingController::class, 'edit'])->name('branding.edit');
            Route::put('/branding', [OrganizationBrandingController::class, 'update'])->name('branding.update');
            Route::delete('/branding', [OrganizationBrandingController::class, 'destroy'])->name('branding.destroy');
            
            // Company Analytics
            Route::get('/analytics', [CompanyAnalyticsController::class, 'index'])->name('analytics.index');
            Route::get('/analytics/refresh', [CompanyAnalyticsController::class, 'refresh'])->name('analytics.refresh');
            Route::get('/analytics/export', [CompanyAnalyticsController::class, 'export'])->name('analytics.export');
        });
        
        Route::prefix('branches')->name('branches.')->group(function () {
            // Branch Management
            Route::get('/', [BranchManagementController::class, 'index'])->name('index');
            Route::get('/create', [BranchManagementController::class, 'create'])->name('create');
            Route::post('/', [BranchManagementController::class, 'store'])->name('store');
            Route::get('/{sucursal}', [BranchManagementController::class, 'show'])->name('show');
            Route::get('/{sucursal}/edit', [BranchManagementController::class, 'edit'])->name('edit');
            Route::put('/{sucursal}', [BranchManagementController::class, 'update'])->name('update');
            Route::delete('/{sucursal}', [BranchManagementController::class, 'destroy'])->name('destroy');
            
            // Branch Settings
            Route::get('/{sucursal}/settings', [BranchManagementController::class, 'settings'])->name('settings');
            Route::put('/{sucursal}/settings', [BranchManagementController::class, 'updateSettings'])->name('settings.update');
            
            // Branch Performance
            Route::get('/{sucursal}/performance', [BranchManagementController::class, 'performance'])->name('performance');
            Route::get('/{sucursal}/performance/refresh', [BranchManagementController::class, 'refreshPerformance'])->name('performance.refresh');
            
            // Branch Transfers
            Route::get('/transfers', [BranchTransferController::class, 'index'])->name('transfers.index');
            Route::get('/transfers/create', [BranchTransferController::class, 'create'])->name('transfers.create');
            Route::post('/transfers', [BranchTransferController::class, 'store'])->name('transfers.store');
            Route::get('/transfers/{transfer}', [BranchTransferController::class, 'show'])->name('transfers.show');
            Route::put('/transfers/{transfer}/send', [BranchTransferController::class, 'send'])->name('transfers.send');
            Route::put('/transfers/{transfer}/receive', [BranchTransferController::class, 'receive'])->name('transfers.receive');
            Route::put('/transfers/{transfer}/cancel', [BranchTransferController::class, 'cancel'])->name('transfers.cancel');
        });
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

