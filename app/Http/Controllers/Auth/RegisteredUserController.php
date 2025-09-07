<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Plan;
use App\Models\Rubro;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        // Obtener el plan seleccionado del parámetro de consulta
        $selectedPlan = $request->query('plan', 'basico');
        
        // Obtener todos los planes disponibles (incluyendo FREE que ahora es visible)
        $planes = Plan::activos()
            ->visibles()
            ->ordenados()
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'nombre' => $plan->nombre,
                    'descripcion' => $plan->descripcion,
                    'precio_mensual' => $plan->precio_mensual,
                    'precio_anual' => $plan->precio_anual,
                    'max_usuarios' => $plan->max_usuarios,
                    'max_sucursales' => $plan->max_sucursales,
                    'max_rubros' => $plan->max_rubros,
                    'max_productos' => $plan->max_productos,
                    'dias_prueba' => $plan->dias_prueba,
                    'es_gratis' => $plan->esGratis(),
                    'caracteristicas' => $plan->getCaracteristicas(),
                    'descuento_anual' => $plan->getDescuentoAnual(),
                ];
            });
        
        // Obtener todos los rubros disponibles  
        $rubros = Rubro::where('activo', true)->get()->map(function ($rubro) {
            return [
                'id' => $rubro->id,
                'nombre' => ucfirst($rubro->nombre),
                'modulos_default' => $rubro->modulos_default_json ?? [],
            ];
        });

        return Inertia::render('Auth/Register', [
            'selectedPlan' => $selectedPlan,
            'planes' => $planes,
            'rubros' => $rubros
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'empresa_nombre' => ['required', 'string', 'max:120'],
            'empresa_ruc' => ['nullable', 'string', 'max:15'],
            'tiene_ruc' => ['boolean'],
            'plan_id' => ['required', 'integer', 'exists:planes,id'],
            'rubro_id' => ['required', 'integer', 'exists:rubros,id'],
            'terms' => ['required', 'accepted'],
        ], [
            'email.unique' => 'Ya existe una cuenta con este email.',
            'plan_id.exists' => 'El plan seleccionado no es válido.',
            'rubro_id.exists' => 'El rubro seleccionado no es válido.',
        ]);

        // Verificar que el email no esté en uso por otra empresa
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return back()->withErrors([
                'email' => 'Ya existe una cuenta con este email.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            // 1. Obtener el plan seleccionado
            $plan = Plan::findOrFail($request->plan_id);

            // 2. Crear la empresa
            $empresa = Empresa::create([
                'nombre' => $request->empresa_nombre,
                'ruc' => $request->tiene_ruc ? $request->empresa_ruc : null,
                'tiene_ruc' => (bool) $request->tiene_ruc,
                'plan_id' => $plan->id,
                'features_json' => $plan->limites_json, // Usar los límites del plan
                'sucursales_enabled' => $plan->max_sucursales > 1,
                'sucursales_count' => 1,
                'allow_negative_stock' => false,
                'precio_incluye_igv' => true,
                'timezone' => 'America/Lima',
                'activa' => true,
            ]);

            // 3. Crear la sucursal principal
            $sucursal = Sucursal::create([
                'empresa_id' => $empresa->id,
                'nombre' => 'Sucursal Principal',
                'codigo_interno' => 'PRIN',
                'direccion' => '',
                'activa' => true,
            ]);

            // 4. Crear el usuario owner
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'empresa_id' => $empresa->id,
                'sucursal_id' => $sucursal->id,
                'rol_principal' => 'owner',
                'activo' => true,
                'email_verified_at' => now(),
            ]);

            // 5. Obtener el rubro y asociarlo a la empresa
            $rubro = Rubro::findOrFail($request->rubro_id);
            $empresa->rubros()->attach($rubro->id, [
                'es_principal' => true,
                'configuracion_json' => json_encode([
                    'setup_completed' => false,
                    'created_at' => now(),
                ]),
            ]);

            // 6. Crear feature flags basado en el plan
            $this->createFeatureFlags($empresa->id, $plan);

            DB::commit();

            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 
                '¡Bienvenido a SmartKet! Tu cuenta ha sido creada exitosamente.'
            );

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors([
                'general' => 'Ocurrió un error al crear tu cuenta: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Crear feature flags para la empresa basado en el plan
     */
    private function createFeatureFlags($empresaId, $plan)
    {
        $features = [
            'pos' => true,
            'inventario' => true,
            'reportes_basicos' => true,
            'multi_sucursal' => $plan->max_sucursales > 1,
            'usuarios_multiples' => $plan->max_usuarios > 1,
            'inventario_avanzado' => $plan->max_productos > 100,
            'reportes_avanzados' => $plan->max_productos > 100,
            'facturacion_electronica' => $plan->max_productos > 1000,
        ];

        foreach ($features as $feature => $enabled) {
            \App\Models\FeatureFlag::create([
                'empresa_id' => $empresaId,
                'feature_key' => $feature,
                'enabled' => $enabled
            ]);
        }
    }
}
