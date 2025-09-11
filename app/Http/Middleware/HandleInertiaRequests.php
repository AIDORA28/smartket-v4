<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'email_verified_at' => $request->user()->email_verified_at,
                    'empresa_id' => $request->user()->empresa_id,
                    'sucursal_id' => $request->user()->sucursal_id,
                    'rol_principal' => $request->user()->rol_principal ?? 'staff',
                    'activo' => $request->user()->activo ?? true,
                ] : null,
            ],
            'empresa' => function () use ($request) {
                if (!$request->user()) return null;
                
                $tenantService = app('App\Services\TenantService');
                $empresa = $tenantService->getEmpresa();
                return $empresa ? [
                    'id' => $empresa->id,
                    'nombre' => $empresa->nombre,
                    'logo' => $empresa->logo,
                ] : null;
            },
            'sucursal' => function () use ($request) {
                if (!$request->user()) return null;
                
                $tenantService = app('App\Services\TenantService');
                $sucursal = $tenantService->getSucursal();
                return $sucursal ? [
                    'id' => $sucursal->id,
                    'nombre' => $sucursal->nombre,
                ] : null;
            },
            'empresas_disponibles' => function () use ($request) {
                if (!$request->user()) return [];
                
                return $request->user()->empresasAccesibles()
                    ->get()
                    ->toArray();
            },
            'sucursales_disponibles' => function () use ($request) {
                if (!$request->user()) return [];
                
                $tenantService = app('App\Services\TenantService');
                $empresa = $tenantService->getEmpresa();
                
                if (!$empresa) return [];
                
                return $empresa->sucursales()
                    ->where('activa', true)
                    ->select(['sucursales.id', 'sucursales.nombre'])
                    ->get()
                    ->toArray();
            },
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'status' => fn () => $request->session()->get('status'),
            ],
            'errors' => fn () => $request->session()->get('errors') 
                ? $request->session()->get('errors')->getBag('default')->getMessages()
                : [],
        ];
    }
}

