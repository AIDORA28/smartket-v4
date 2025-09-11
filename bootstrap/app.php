<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Rutas del módulo Core API
            Route::prefix('api/core')
                ->name('api.core.')
                ->middleware('api')
                ->group(base_path('routes/core.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware de tenant
        $middleware->alias([
            'empresa.scope' => \App\Http\Middleware\EmpresaScope::class,
            'feature.guard' => \App\Http\Middleware\FeatureGuard::class,
        ]);
        
        // Inertia.js middleware
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

