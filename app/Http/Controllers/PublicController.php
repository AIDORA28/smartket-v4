<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicController extends Controller
{
    /**
     * Health check endpoint
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'SmartKet v4 API is running',
            'timestamp' => now()->toISOString(),
            'version' => '4.0.0'
        ]);
    }

    /**
     * API information endpoint
     */
    public function info(): JsonResponse
    {
        return response()->json([
            'name' => 'SmartKet v4 ERP',
            'description' => 'Sistema ERP para pequeñas y medianas empresas',
            'version' => '4.0.0',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'modules' => [
                'empresa' => 'Gestión de empresas y sucursales',
                'inventario' => 'Gestión de productos e inventario',
                'pos' => 'Sistema de punto de venta',
                'reportes' => 'Reportes y analytics',
                'usuarios' => 'Gestión de usuarios y roles'
            ],
            'endpoints' => [
                'health' => '/api/public/health',
                'info' => '/api/public/info',
                'auth' => '/api/auth/*',
                'api' => '/api/*'
            ]
        ]);
    }
}
