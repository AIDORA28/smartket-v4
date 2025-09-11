<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Core\PlanAddon;
use App\Models\Core\EmpresaAddon;
use App\Models\Core\Plan;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class AddonsController extends Controller
{
    /**
     * Mostrar add-ons disponibles para el plan actual
     */
    public function index()
    {
        $user = Auth::user();
        $empresa = $user->empresa;
        $planActual = $empresa->plan;

        // Obtener add-ons disponibles para el plan actual
        $addonsDisponibles = PlanAddon::activos()
            ->where(function ($query) use ($planActual) {
                $query->whereJsonContains('restricciones_json->planes_compatibles', $planActual->nombre)
                    ->orWhereNull('restricciones_json->planes_compatibles');
            })
            ->orderBy('tipo')
            ->orderBy('precio_mensual')
            ->get()
            ->groupBy('tipo');

        // Obtener add-ons activos de la empresa
        $addonsActivos = EmpresaAddon::where('empresa_id', $empresa->id)
            ->vigentes()
            ->with('planAddon')
            ->get();

        return Inertia::render('Addons/Index', [
            'planActual' => $planActual,
            'addonsDisponibles' => $addonsDisponibles,
            'addonsActivos' => $addonsActivos,
        ]);
    }

    /**
     * Comprar un add-on
     */
    public function comprar(Request $request)
    {
        $request->validate([
            'addon_id' => 'required|exists:plan_addons,id',
            'periodo' => 'required|in:mensual,anual',
        ]);

        $user = Auth::user();
        $empresa = $user->empresa;
        $addon = PlanAddon::findOrFail($request->addon_id);

        // Verificar que el add-on sea compatible con el plan actual
        $planActual = $empresa->plan;
        $planesCompatibles = $addon->restricciones_json['planes_compatibles'] ?? [];
        
        if (!empty($planesCompatibles) && !in_array($planActual->nombre, $planesCompatibles)) {
            return back()->withErrors(['addon' => 'Este add-on no es compatible con tu plan actual.']);
        }

        // Verificar si ya tiene este add-on activo
        $addonExistente = EmpresaAddon::where('empresa_id', $empresa->id)
            ->where('plan_addon_id', $addon->id)
            ->vigentes()
            ->first();

        if ($addonExistente) {
            return back()->withErrors(['addon' => 'Ya tienes este add-on activo.']);
        }

        // Calcular fechas y precio
        $fechaInicio = now();
        $fechaFin = $request->periodo === 'anual' 
            ? $fechaInicio->copy()->addYear() 
            : $fechaInicio->copy()->addMonth();
        
        $precio = $addon->getPrecio($request->periodo);

        // Crear el add-on para la empresa
        EmpresaAddon::create([
            'empresa_id' => $empresa->id,
            'plan_addon_id' => $addon->id,
            'cantidad' => $addon->cantidad,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'precio_pagado' => $precio,
            'periodo' => $request->periodo,
            'activo' => true,
            'configuracion_json' => [
                'comprado_por' => $user->id,
                'fecha_compra' => now()->toISOString(),
            ],
        ]);

        // Aquí normalmente se procesaría el pago
        // Por ahora simulamos que el pago fue exitoso

        return back()->with('success', "Add-on '{$addon->nombre}' activado exitosamente por {$request->periodo}.");
    }

    /**
     * Cancelar un add-on
     */
    public function cancelar(Request $request)
    {
        $request->validate([
            'empresa_addon_id' => 'required|exists:empresa_addons,id',
        ]);

        $user = Auth::user();
        $empresaAddon = EmpresaAddon::where('id', $request->empresa_addon_id)
            ->where('empresa_id', $user->empresa_id)
            ->firstOrFail();

        $empresaAddon->update([
            'activo' => false,
            'configuracion_json' => array_merge(
                $empresaAddon->configuracion_json ?? [],
                [
                    'cancelado_por' => $user->id,
                    'fecha_cancelacion' => now()->toISOString(),
                ]
            ),
        ]);

        return back()->with('success', "Add-on '{$empresaAddon->planAddon->nombre}' cancelado exitosamente.");
    }

    /**
     * Obtener estadísticas de uso de add-ons
     */
    public function estadisticas()
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        $stats = [
            'usuarios_adicionales' => EmpresaAddon::where('empresa_id', $empresa->id)
                ->vigentes()
                ->whereHas('planAddon', function ($query) {
                    $query->where('tipo', 'usuario');
                })
                ->sum('cantidad'),
            
            'sucursales_adicionales' => EmpresaAddon::where('empresa_id', $empresa->id)
                ->vigentes()
                ->whereHas('planAddon', function ($query) {
                    $query->where('tipo', 'sucursal');
                })
                ->sum('cantidad'),
                
            'rubros_adicionales' => EmpresaAddon::where('empresa_id', $empresa->id)
                ->vigentes()
                ->whereHas('planAddon', function ($query) {
                    $query->where('tipo', 'rubro');
                })
                ->sum('cantidad'),
        ];

        // Calcular límites totales
        $plan = $empresa->plan;
        $limitesTotales = [
            'usuarios_totales' => $plan->max_usuarios + $stats['usuarios_adicionales'],
            'sucursales_totales' => $plan->max_sucursales + $stats['sucursales_adicionales'],
            'rubros_totales' => $plan->max_rubros + $stats['rubros_adicionales'],
        ];

        return response()->json([
            'plan_base' => [
                'usuarios' => $plan->max_usuarios,
                'sucursales' => $plan->max_sucursales,
                'rubros' => $plan->max_rubros,
            ],
            'addons_activos' => $stats,
            'limites_totales' => $limitesTotales,
        ]);
    }
}

