<?php

use App\Http\Controllers\CRM\ClienteController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\Sales\VentaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\Inventory\ProductoController;
use App\Http\Controllers\Inventory\CategoriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas autenticadas con middleware de empresa
Route::middleware(['auth:sanctum', 'empresa.scope'])->group(function () {
    
    // Información del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ========================================
    // MÓDULO CRM - CLIENTES 
    // ========================================
    Route::prefix('clientes')->name('api.clientes.')->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('index');
        Route::post('/', [ClienteController::class, 'store'])->name('store');
        Route::get('/search', [ClienteController::class, 'search'])->name('search');
        Route::get('/{cliente}', [ClienteController::class, 'show'])->name('show');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('update');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('destroy');
        Route::get('/{cliente}/estadisticas', [ClienteController::class, 'estadisticas'])->name('estadisticas');
    });

    // ========================================
    // MÓDULO POS - MÉTODOS DE PAGO
    // ========================================
    Route::prefix('metodos-pago')->name('api.metodos-pago.')->group(function () {
        Route::get('/', [MetodoPagoController::class, 'index'])->name('index');
        Route::post('/', [MetodoPagoController::class, 'store'])->name('store');
        Route::get('/activos', [MetodoPagoController::class, 'activos'])->name('activos');
        Route::post('/reordenar', [MetodoPagoController::class, 'reordenar'])->name('reordenar');
        Route::get('/{metodoPago}', [MetodoPagoController::class, 'show'])->name('show');
        Route::put('/{metodoPago}', [MetodoPagoController::class, 'update'])->name('update');
        Route::delete('/{metodoPago}', [MetodoPagoController::class, 'destroy'])->name('destroy');
        Route::post('/{metodoPago}/toggle', [MetodoPagoController::class, 'toggleEstado'])->name('toggle');
    });

    // ========================================
    // MÓDULO POS - VENTAS
    // ========================================
    Route::prefix('ventas')->name('api.ventas.')->group(function () {
        Route::get('/', [VentaController::class, 'index'])->name('index');
        Route::post('/', [VentaController::class, 'store'])->name('store');
        Route::get('/del-dia', [VentaController::class, 'ventasDelDia'])->name('del-dia');
        Route::get('/resumen', [VentaController::class, 'resumen'])->name('resumen');
        Route::get('/productos-mas-vendidos', [VentaController::class, 'productosMasVendidos'])->name('productos-mas-vendidos');
        Route::get('/reporte', [VentaController::class, 'reporte'])->name('reporte');
        Route::get('/dashboard', [VentaController::class, 'dashboard'])->name('dashboard');
        Route::get('/{venta}', [VentaController::class, 'show'])->name('show');
        Route::post('/{venta}/anular', [VentaController::class, 'anular'])->name('anular');
        
        // Rutas de pagos específicas para ventas
        Route::post('/{venta}/pagos', [PagoController::class, 'procesarPago'])->name('pagos.store');
        Route::post('/{venta}/pagos-multiples', [PagoController::class, 'procesarPagosMultiples'])->name('pagos.multiples');
        Route::get('/{venta}/pagos', [PagoController::class, 'pagosPorVenta'])->name('pagos.index');
    });

    // ========================================
    // MÓDULO POS - PAGOS
    // ========================================
    Route::prefix('pagos')->name('api.pagos.')->group(function () {
        Route::get('/metodos-disponibles', [PagoController::class, 'metodosDisponibles'])->name('metodos-disponibles');
        Route::get('/resumen-del-dia', [PagoController::class, 'resumenDelDia'])->name('resumen-del-dia');
        Route::get('/pendientes', [PagoController::class, 'pagosPendientes'])->name('pendientes');
        Route::post('/validar-tarjeta', [PagoController::class, 'validarTarjeta'])->name('validar-tarjeta');
        Route::post('/{pago}/anular', [PagoController::class, 'anularPago'])->name('anular');
        Route::post('/{pago}/confirmar', [PagoController::class, 'confirmarPago'])->name('confirmar');
    });

    // ========================================
    // MÓDULO POS - CAJA
    // ========================================
    Route::prefix('caja')->name('api.caja.')->group(function () {
        Route::post('/abrir', [CajaController::class, 'abrirCaja'])->name('abrir');
        Route::post('/cerrar', [CajaController::class, 'cerrarCaja'])->name('cerrar');
        Route::get('/estado', [CajaController::class, 'estadoCaja'])->name('estado');
        Route::get('/arqueo', [CajaController::class, 'arqueo'])->name('arqueo');
        Route::get('/historial', [CajaController::class, 'historial'])->name('historial');
        Route::get('/exportar-arqueo', [CajaController::class, 'exportarArqueo'])->name('exportar-arqueo');
        Route::get('/validar-apertura', [CajaController::class, 'validarApertura'])->name('validar-apertura');
        Route::get('/validar-cierre', [CajaController::class, 'validarCierre'])->name('validar-cierre');
    });

    // ========================================
    // MÓDULO PRODUCTOS (API)
    // ========================================
    Route::prefix('productos')->name('api.productos.')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('index');
        Route::post('/', [ProductoController::class, 'store'])->name('store');
        Route::get('/search', [ProductoController::class, 'search'])->name('search');
        Route::get('/barcode/{codigo}', [ProductoController::class, 'buscarPorCodigoBarras'])->name('barcode');
        Route::get('/stock-bajo', [ProductoController::class, 'stockBajo'])->name('stock-bajo');
        Route::get('/{producto}', [ProductoController::class, 'show'])->name('show');
        Route::put('/{producto}', [ProductoController::class, 'update'])->name('update');
        Route::delete('/{producto}', [ProductoController::class, 'destroy'])->name('destroy');
        Route::post('/{producto}/ajustar-stock', [ProductoController::class, 'ajustarStock'])->name('ajustar-stock');
    });

    // ========================================
    // MÓDULO CATEGORÍAS (API)
    // ========================================
    Route::prefix('categorias')->name('api.categorias.')->group(function () {
        Route::get('/', [CategoriaController::class, 'index'])->name('index');
        Route::post('/', [CategoriaController::class, 'store'])->name('store');
        Route::get('/activas', [CategoriaController::class, 'activas'])->name('activas');
        Route::get('/{categoria}', [CategoriaController::class, 'show'])->name('show');
        Route::put('/{categoria}', [CategoriaController::class, 'update'])->name('update');
        Route::delete('/{categoria}', [CategoriaController::class, 'destroy'])->name('destroy');
    });

});

// ========================================
// RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
// ========================================
Route::prefix('public')->name('api.public.')->group(function () {
    
    // Información del sistema
    Route::get('/info', function () {
        return response()->json([
            'app' => config('app.name'),
            'version' => '4.0.0',
            'status' => 'active',
            'timestamp' => now()->toISOString()
        ]);
    })->name('info');

    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'database' => 'connected',
            'timestamp' => now()->toISOString()
        ]);
    })->name('health');

});

