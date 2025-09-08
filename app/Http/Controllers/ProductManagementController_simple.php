<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\TenantService;

class ProductManagementController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Versión simple para debugging
     */
    public function index(Request $request): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if (!$empresa) {
            return Inertia::render('Products', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        // Datos simples para probar
        $productos = [];
        $categorias = [
            ['id' => 1, 'nombre' => 'Bebidas', 'descripcion' => 'Bebidas varias', 'productos_count' => 0],
            ['id' => 2, 'nombre' => 'Abarrotes', 'descripcion' => 'Productos de abarrotes', 'productos_count' => 0],
            ['id' => 3, 'nombre' => 'Lácteos', 'descripcion' => 'Productos lácteos', 'productos_count' => 0],
        ];
        
        $marcas = [
            ['id' => 1, 'nombre' => 'Coca-Cola', 'descripcion' => 'Marca de bebidas', 'productos_count' => 0],
            ['id' => 2, 'nombre' => 'Gloria', 'descripcion' => 'Marca de lácteos', 'productos_count' => 0],
            ['id' => 3, 'nombre' => 'Bimbo', 'descripcion' => 'Marca de panadería', 'productos_count' => 0],
        ];
        
        $unidades = [
            ['id' => 1, 'nombre' => 'Unidad', 'abreviacion' => 'Und', 'tipo' => 'Contable', 'productos_count' => 0, 'activo' => true],
            ['id' => 2, 'nombre' => 'Kilogramo', 'abreviacion' => 'Kg', 'tipo' => 'Peso', 'productos_count' => 0, 'activo' => true],
            ['id' => 3, 'nombre' => 'Litro', 'abreviacion' => 'L', 'tipo' => 'Volumen', 'productos_count' => 0, 'activo' => true],
        ];

        return Inertia::render('Products', [
            'productos' => $productos,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'unidades' => $unidades,
            'categoriasParaFiltros' => ['Bebidas', 'Abarrotes', 'Lácteos'],
            'filters' => [
                'search' => '',
                'category' => '',
                'status' => '',
            ],
            'empresa' => [
                'id' => $empresa->id,
                'nombre' => $empresa->nombre_comercial,
            ]
        ]);
    }
}
