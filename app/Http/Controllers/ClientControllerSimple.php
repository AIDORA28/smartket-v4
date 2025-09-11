<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientControllerSimple extends Controller
{
    public function index(Request $request): Response
    {
        // Datos de prueba simplificados
        $clients = [
            [
                'id' => 1,
                'nombre' => 'Cliente Test 1',
                'email' => 'cliente1@test.com',
                'telefono' => '999123456',
                'ruc' => '12345678901',
                'credito_limite' => 1000,
                'credito_usado' => 200,
                'activo' => true,
                'total_compras' => 2500
            ],
            [
                'id' => 2,
                'nombre' => 'Cliente Test 2',
                'email' => 'cliente2@test.com',
                'telefono' => '999654321',
                'ruc' => '10987654321',
                'credito_limite' => 500,
                'credito_usado' => 0,
                'activo' => true,
                'total_compras' => 1200
            ],
            [
                'id' => 3,
                'nombre' => 'Cliente Test 3',
                'email' => 'cliente3@test.com',
                'telefono' => '999111222',
                'ruc' => '20123456789',
                'credito_limite' => 0,
                'credito_usado' => 0,
                'activo' => false,
                'total_compras' => 800
            ]
        ];

        $stats = [
            'total_clientes' => 3,
            'clientes_activos' => 2,
            'con_credito' => 2,
            'credito_pendiente' => 200
        ];

        return Inertia::render('Clients', [
            'clients' => [
                'data' => $clients,
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 20,
                'total' => 3
            ],
            'stats' => $stats,
            'filters' => [
                'search' => '',
                'status' => '',
                'credit' => ''
            ]
        ]);
    }
}

