<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlaceholderController extends Controller
{
    public function show(Request $request, string $module): Response
    {
        $moduleConfigs = [
            'caja' => [
                'title' => 'Sistema de Caja',
                'description' => 'Apertura, cierre y arqueo de caja diario',
                'status' => 'development'
            ],
            'compras' => [
                'title' => 'Órdenes de Compra',
                'description' => 'Gestión de compras y órdenes a proveedores',
                'status' => 'development'
            ],
            'proveedores' => [
                'title' => 'Gestión de Proveedores',
                'description' => 'Administración de información de proveedores',
                'status' => 'development'
            ],
            'lotes' => [
                'title' => 'Lotes y Vencimientos',
                'description' => 'Control FIFO y alertas de vencimiento',
                'status' => 'development'
            ],
            'transferencias' => [
                'title' => 'Transferencias entre Sucursales',
                'description' => 'Movimiento de inventario entre ubicaciones',
                'status' => 'development'
            ],
            'admin-empresas' => [
                'title' => 'Gestión de Empresas',
                'description' => 'Configuración multi-tenant y administración',
                'status' => 'development'
            ],
            'admin-usuarios' => [
                'title' => 'Gestión de Usuarios',
                'description' => 'Roles, permisos y administración de usuarios',
                'status' => 'development'
            ],
            'feature-flags' => [
                'title' => 'Feature Flags',
                'description' => 'Control de funcionalidades por plan',
                'status' => 'development'
            ],
        ];

        $config = $moduleConfigs[$module] ?? [
            'title' => ucfirst($module),
            'description' => 'Este módulo está en desarrollo',
            'status' => 'development'
        ];

        return Inertia::render('Placeholder', [
            'module' => $config['title'],
            'description' => $config['description'],
            'status' => $config['status']
        ]);
    }
}
