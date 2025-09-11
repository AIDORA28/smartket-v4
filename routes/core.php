<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Core\UserController;
use App\Http\Controllers\Core\EmpresaController;
use App\Http\Controllers\Core\SucursalController;
use App\Http\Controllers\Core\RubroController;
use App\Http\Controllers\Core\PlanController;
use App\Http\Controllers\Core\PlanAddonController;
use App\Http\Controllers\Core\MultiTenantController;

/*
|--------------------------------------------------------------------------
| Core Module API Routes
|--------------------------------------------------------------------------
|
| Rutas API para el módulo Core del sistema SmartKet.
| Incluye gestión de usuarios, empresas, sucursales, rubros y planes.
|
*/

Route::middleware(['auth:sanctum'])->prefix('core')->name('core.')->group(function () {
    
    // ===== MULTI-TENANT CONTEXT =====
    Route::prefix('context')->name('context.')->group(function () {
        Route::get('/', [MultiTenantController::class, 'getContext'])->name('get');
        Route::post('/switch-empresa', [MultiTenantController::class, 'switchEmpresa'])->name('switch-empresa');
        Route::post('/switch-sucursal', [MultiTenantController::class, 'switchSucursal'])->name('switch-sucursal');
        Route::post('/grant-empresa-access', [MultiTenantController::class, 'grantEmpresaAccess'])->name('grant-empresa-access');
        Route::post('/revoke-empresa-access', [MultiTenantController::class, 'revokeEmpresaAccess'])->name('revoke-empresa-access');
    });

    // ===== USUARIOS =====
    Route::apiResource('users', UserController::class);
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('profile', [UserController::class, 'profile'])->name('profile');
        Route::get('available-roles', [UserController::class, 'availableRoles'])->name('available-roles');
    });

    // ===== EMPRESAS =====
    Route::apiResource('empresas', EmpresaController::class);
    Route::prefix('empresas')->name('empresas.')->group(function () {
        Route::get('{empresa}/usuarios', [EmpresaController::class, 'usuarios'])->name('usuarios');
        Route::get('{empresa}/sucursales', [EmpresaController::class, 'sucursales'])->name('sucursales');
        Route::get('{empresa}/rubros', [EmpresaController::class, 'rubros'])->name('rubros');
        Route::get('{empresa}/addons', [EmpresaController::class, 'addons'])->name('addons');
        Route::post('{empresa}/toggle-status', [EmpresaController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{empresa}/rubros/sync', [EmpresaController::class, 'syncRubros'])->name('rubros.sync');
        Route::post('{empresa}/addons/attach', [EmpresaController::class, 'attachAddon'])->name('addons.attach');
        Route::delete('{empresa}/addons/{addon}/detach', [EmpresaController::class, 'detachAddon'])->name('addons.detach');
    });

    // ===== SUCURSALES =====
    Route::apiResource('sucursales', SucursalController::class);
    Route::prefix('sucursales')->name('sucursales.')->group(function () {
        Route::get('empresa/{empresa}', [SucursalController::class, 'byEmpresa'])->name('by-empresa');
        Route::post('{sucursal}/toggle-status', [SucursalController::class, 'toggleStatus'])->name('toggle-status');
    });

    // ===== RUBROS =====
    Route::apiResource('rubros', RubroController::class);
    Route::prefix('rubros')->name('rubros.')->group(function () {
        Route::get('activos', [RubroController::class, 'activos'])->name('activos');
        Route::post('{rubro}/toggle-status', [RubroController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{rubro}/empresas', [RubroController::class, 'empresas'])->name('empresas');
    });

    // ===== PLANES =====
    Route::apiResource('planes', PlanController::class);
    Route::prefix('planes')->name('planes.')->group(function () {
        Route::get('activos', [PlanController::class, 'activos'])->name('activos');
        Route::get('visibles', [PlanController::class, 'visibles'])->name('visibles');
        Route::post('{plan}/toggle-status', [PlanController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{plan}/empresas', [PlanController::class, 'empresas'])->name('empresas');
        Route::get('{plan}/addons', [PlanController::class, 'addons'])->name('addons');
    });

    // ===== PLAN ADDONS =====
    Route::apiResource('plan-addons', PlanAddonController::class);
    Route::prefix('plan-addons')->name('plan-addons.')->group(function () {
        Route::get('activos', [PlanAddonController::class, 'activos'])->name('activos');
        Route::get('tipo/{tipo}', [PlanAddonController::class, 'byTipo'])->name('by-tipo');
        Route::post('{planAddon}/toggle-status', [PlanAddonController::class, 'toggleStatus'])->name('toggle-status');
    });

});

// ===== RUTAS PÚBLICAS =====
Route::prefix('core/public')->name('core.public.')->group(function () {
    
    // Planes visibles para landing/registro
    Route::get('planes', [PlanController::class, 'planesPublicos'])->name('planes');
    
    // Rubros para selección en registro
    Route::get('rubros', [RubroController::class, 'rubrosPublicos'])->name('rubros');
    
});

