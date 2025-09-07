<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Plan;

class PublicController extends Controller
{
    /**
     * Landing page principal
     */
    public function landing(): Response
    {
        return Inertia::render('Public/Landing', [
            'planes' => $this->getPlanesFromDatabase(),
            'features' => $this->getFeatures(),
            'testimonios' => $this->getTestimonios(),
        ]);
    }

    /**
     * Página de precios
     */
    public function precios(): Response
    {
        return Inertia::render('Public/Precios', [
            'planes' => $this->getPlanesFromDatabase(),
        ]);
    }

    /**
     * Página de características
     */
    public function caracteristicas(): Response
    {
        return Inertia::render('Public/Caracteristicas', [
            'features' => $this->getFeatures(),
        ]);
    }

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

    private function getPlanesFromDatabase()
    {
        return Plan::activos()
            ->visibles()
            ->ordenados()
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => strtolower($plan->nombre),
                    'nombre' => "Plan {$plan->nombre}",
                    'precio' => $plan->precio_mensual,
                    'precio_anual' => $plan->precio_anual,
                    'descripcion' => $plan->descripcion ?? 'Plan para tu negocio',
                    'features' => $plan->getCaracteristicas(),
                    'popular' => $plan->nombre === 'PROFESIONAL',
                    'max_usuarios' => $plan->max_usuarios,
                    'max_sucursales' => $plan->max_sucursales,
                    'max_rubros' => $plan->max_rubros,
                    'max_productos' => $plan->max_productos,
                    'dias_prueba' => $plan->dias_prueba,
                    'es_gratis' => $plan->esGratis(),
                    'descuento_anual' => $plan->getDescuentoAnual(),
                ];
            });
    }

    private function getFeatures()
    {
        return [
            [
                'titulo' => 'Punto de Venta Intuitivo',
                'descripcion' => 'Sistema de ventas fácil de usar, diseñado para que cualquier persona pueda operarlo sin complicaciones.',
                'icono' => '🛒',
            ],
            [
                'titulo' => 'Control de Inventario',
                'descripcion' => 'Mantén tu stock bajo control con alertas automáticas, movimientos en tiempo real y reportes detallados.',
                'icono' => '📦',
            ],
            [
                'titulo' => 'Multi-sucursal',
                'descripcion' => 'Gestiona múltiples ubicaciones desde un solo lugar, con control independiente de cada sucursal.',
                'icono' => '🏪',
            ],
            [
                'titulo' => 'Reportes Inteligentes',
                'descripcion' => 'Obtén insights de tu negocio con reportes automáticos que te ayudan a tomar mejores decisiones.',
                'icono' => '📊',
            ],
            [
                'titulo' => 'Seguridad Total',
                'descripcion' => 'Tus datos están seguros con respaldos automáticos y acceso controlado por roles de usuario.',
                'icono' => '🔒',
            ],
            [
                'titulo' => 'Soporte Especializado',
                'descripcion' => 'Nuestro equipo te acompaña en todo momento para que aproveches al máximo tu ERP.',
                'icono' => '🤝',
            ],
        ];
    }

    private function getTestimonios()
    {
        return [
            [
                'nombre' => 'María González',
                'negocio' => 'Panadería La Esperanza',
                'testimonio' => 'SmartKet transformó nuestra panadería. Ahora controlamos mejor nuestro inventario y las ventas han aumentado 30%.',
                'avatar' => '/img/user.jpg',
                'rating' => 5,
            ],
            [
                'nombre' => 'Carlos Mendez',
                'negocio' => 'Minimarket Don Carlos',
                'testimonio' => 'Fácil de usar y muy completo. Mis empleados aprendieron a usarlo en pocos días.',
                'avatar' => '/img/user2-160x160.jpg',
                'rating' => 5,
            ],
            [
                'nombre' => 'Ana Rodriguez',
                'negocio' => 'Distribuidora El Sol',
                'testimonio' => 'Los reportes me ayudan a entender mejor mi negocio. Ahora tomo decisiones basadas en datos reales.',
                'avatar' => '/img/muser2-160x160.jpg',
                'rating' => 5,
            ],
        ];
    }
}
