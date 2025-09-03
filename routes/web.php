<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified', 'empresa.scope'])->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    
    // Rutas de gestión de tenant
    Route::prefix('tenant')->name('tenant.')->group(function () {
        Route::post('/switch-sucursal', [TenantController::class, 'switchSucursal'])->name('switch-sucursal');
    });
    
    // Módulo de Productos e Inventario
    // Productos
Route::get('productos', \App\Livewire\Productos\Lista::class)->name('productos.index');
Route::get('productos/crear', \App\Livewire\Productos\Formulario::class)->name('productos.crear');
Route::get('productos/{producto}/editar', \App\Livewire\Productos\Formulario::class)->name('productos.editar');
    Route::post('productos/{producto}/ajustar-stock', [ProductoController::class, 'ajustarStock'])->name('productos.ajustar-stock');
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    
    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Inventario', 'description' => 'Control de stock por sucursal']);
        })->name('index');
        Route::get('/movimientos', function () {
            return view('placeholder', ['module' => 'Movimientos de Inventario']);
        })->name('movimientos');
        Route::get('/transferencias', function () {
            return view('placeholder', ['module' => 'Transferencias entre Sucursales']);
        })->name('transferencias');
    });
    
    // Módulo de Ventas y POS
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Punto de Venta', 'description' => 'Sistema POS táctil']);
        })->name('index');
    });
    
    Route::prefix('ventas')->name('ventas.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Historial de Ventas']);
        })->name('index');
        Route::get('/{venta}', function () {
            return view('placeholder', ['module' => 'Detalle de Venta']);
        })->name('show');
    });
    
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Gestión de Clientes']);
        })->name('index');
        Route::get('/create', function () {
            return view('placeholder', ['module' => 'Nuevo Cliente']);
        })->name('create');
    });
    
    // Módulo de Caja
    Route::prefix('caja')->name('caja.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Sistema de Caja', 'description' => 'Apertura, cierre y arqueo de caja']);
        })->name('index');
        Route::post('/abrir', function () {
            return redirect()->back()->with('status', 'Caja abierta exitosamente');
        })->name('abrir');
        Route::post('/cerrar', function () {
            return redirect()->back()->with('status', 'Caja cerrada exitosamente');
        })->name('cerrar');
    });
    
    // Módulo de Compras
    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Órdenes de Compra']);
        })->name('index');
        Route::get('/create', function () {
            return view('placeholder', ['module' => 'Nueva Orden de Compra']);
        })->name('create');
    });
    
    Route::prefix('proveedores')->name('proveedores.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Gestión de Proveedores']);
        })->name('index');
        Route::get('/create', function () {
            return view('placeholder', ['module' => 'Nuevo Proveedor']);
        })->name('create');
    });
    
    // Módulo de Lotes y Vencimientos
    Route::prefix('lotes')->name('lotes.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Lotes y Vencimientos', 'description' => 'Control FIFO y alertas de vencimiento']);
        })->name('index');
        Route::get('/vencimientos', function () {
            return view('placeholder', ['module' => 'Alertas de Vencimiento']);
        })->name('vencimientos');
        Route::get('/trazabilidad', function () {
            return view('placeholder', ['module' => 'Trazabilidad de Productos']);
        })->name('trazabilidad');
    });
    
    // Módulo de Reportes y Analytics
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', function () {
            return view('placeholder', ['module' => 'Generador de Reportes', 'description' => 'Reportes dinámicos con exportación PDF/Excel']);
        })->name('index');
        Route::get('/dashboard', function () {
            return view('placeholder', ['module' => 'Analytics Dashboard', 'description' => 'KPIs y métricas de negocio']);
        })->name('dashboard');
        Route::get('/ventas', function () {
            return view('placeholder', ['module' => 'Reportes de Ventas']);
        })->name('ventas');
        Route::get('/inventario', function () {
            return view('placeholder', ['module' => 'Reportes de Inventario']);
        })->name('inventario');
    });
    
    // Módulo de Administración (Solo admins)
    Route::prefix('admin')->name('admin.')->middleware('can:admin')->group(function () {
        Route::prefix('empresas')->name('empresas.')->group(function () {
            Route::get('/', function () {
                return view('placeholder', ['module' => 'Gestión de Empresas', 'description' => 'Configuración multi-tenant']);
            })->name('index');
        });
        
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', function () {
                return view('placeholder', ['module' => 'Gestión de Usuarios', 'description' => 'Roles y permisos']);
            })->name('index');
        });
        
        Route::prefix('feature-flags')->name('feature-flags.')->group(function () {
            Route::get('/', function () {
                return view('placeholder', ['module' => 'Feature Flags', 'description' => 'Control de funcionalidades por plan']);
            })->name('index');
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
