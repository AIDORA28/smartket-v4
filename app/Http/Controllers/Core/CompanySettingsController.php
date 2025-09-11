<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Empresa;
use App\Models\Core\EmpresaSettings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class CompanySettingsController extends Controller
{
    /**
     * Display company settings dashboard
     */
    public function index(): Response
    {
        $empresa = Auth::user()->empresa;
        $settings = $empresa->settings ?? new EmpresaSettings();
        
        return Inertia::render('Core/CompanyManagement/Settings/Index', [
            'empresa' => $empresa->load(['plan', 'settings']),
            'settings' => $settings,
            'can' => [
                'update_settings' => true, // Auth::user()->can('update', $empresa),
                'view_security' => true, // Auth::user()->hasRole(['Super Admin', 'Admin Empresa']),
            ]
        ]);
    }

    /**
     * Show the form for creating new settings
     */
    public function create(): Response
    {
        $empresa = Auth::user()->empresa;
        
        return Inertia::render('Core/CompanyManagement/Settings/Create', [
            'empresa' => $empresa,
            'timezones' => $this->getAvailableTimezones(),
            'languages' => $this->getAvailableLanguages(),
            'currencies' => $this->getAvailableCurrencies(),
        ]);
    }

    /**
     * Store newly created settings
     */
    public function store(Request $request): JsonResponse
    {
        $empresa = Auth::user()->empresa;
        
        $validator = Validator::make($request->all(), [
            'configuracion_notificaciones' => 'nullable|array',
            'configuracion_facturacion' => 'nullable|array',
            'configuracion_inventario' => 'nullable|array',
            'configuracion_ventas' => 'nullable|array',
            'configuracion_backup' => 'nullable|array',
            'zona_horaria_predeterminada' => 'required|string|max:50',
            'idioma_predeterminado' => 'required|string|max:5',
            'moneda_predeterminada' => 'required|string|max:5',
            'configuracion_ui' => 'nullable|array',
            'configuracion_seguridad' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $settings = $empresa->settings()->create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Configuraciones creadas exitosamente',
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear configuraciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display specific settings section
     */
    public function show(string $section)
    {
        $empresa = Auth::user()->empresa;
        $settings = $empresa->settings;
        
        if (!$settings) {
            return Inertia::render('Core/CompanyManagement/Settings/Create', [
                'empresa' => $empresa,
                'message' => 'No se han configurado ajustes para esta empresa. Por favor, configure los ajustes iniciales.',
                'timezones' => $this->getAvailableTimezones(),
                'languages' => $this->getAvailableLanguages(),
                'currencies' => $this->getAvailableCurrencies(),
            ]);
        }
        
        return Inertia::render('Core/CompanyManagement/Settings/Show', [
            'empresa' => $empresa,
            'settings' => $settings,
            'section' => $section,
            'sectionData' => $this->getSectionData($settings, $section),
        ]);
    }

    /**
     * Show form for editing settings
     */
    public function edit(string $section): Response
    {
        $empresa = Auth::user()->empresa;
        $settings = $empresa->settings;
        
        return Inertia::render('Core/CompanyManagement/Settings/Edit', [
            'empresa' => $empresa,
            'settings' => $settings,
            'section' => $section,
            'sectionData' => $this->getSectionData($settings, $section),
            'timezones' => $this->getAvailableTimezones(),
            'languages' => $this->getAvailableLanguages(),
            'currencies' => $this->getAvailableCurrencies(),
        ]);
    }

    /**
     * Update company settings
     */
    public function update(Request $request, string $section): JsonResponse
    {
        $empresa = Auth::user()->empresa;
        $settings = $empresa->settings;
        
        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron configuraciones'
            ], 404);
        }

        $validator = $this->getValidatorForSection($request, $section);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $this->prepareUpdateData($request, $section);
            $settings->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Configuraciones actualizadas exitosamente',
                'settings' => $settings->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuraciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset settings to default
     */
    public function destroy(string $section): JsonResponse
    {
        $empresa = Auth::user()->empresa;
        $settings = $empresa->settings;
        
        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron configuraciones'
            ], 404);
        }

        try {
            $defaultData = $this->getDefaultSettingsForSection($section);
            $settings->update($defaultData);
            
            return response()->json([
                'success' => true,
                'message' => 'Configuraciones restablecidas a valores por defecto'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer configuraciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get section-specific data
     */
    private function getSectionData($settings, string $section): array
    {
        return match ($section) {
            'notifications' => $settings->configuracion_notificaciones ?? [],
            'billing' => $settings->configuracion_facturacion ?? [],
            'inventory' => $settings->configuracion_inventario ?? [],
            'sales' => $settings->configuracion_ventas ?? [],
            'backup' => $settings->configuracion_backup ?? [],
            'regional' => [
                'zona_horaria_predeterminada' => $settings->zona_horaria_predeterminada,
                'idioma_predeterminado' => $settings->idioma_predeterminado,
                'moneda_predeterminada' => $settings->moneda_predeterminada,
            ],
            'ui' => $settings->configuracion_ui ?? [],
            'security' => $settings->configuracion_seguridad ?? [],
            default => []
        };
    }

    /**
     * Get validator for specific section
     */
    private function getValidatorForSection(Request $request, string $section): \Illuminate\Validation\Validator
    {
        $rules = match ($section) {
            'notifications' => ['configuracion_notificaciones' => 'required|array'],
            'billing' => ['configuracion_facturacion' => 'required|array'],
            'inventory' => ['configuracion_inventario' => 'required|array'],
            'sales' => ['configuracion_ventas' => 'required|array'],
            'backup' => ['configuracion_backup' => 'required|array'],
            'regional' => [
                'zona_horaria_predeterminada' => 'required|string|max:50',
                'idioma_predeterminado' => 'required|string|max:5',
                'moneda_predeterminada' => 'required|string|max:5',
            ],
            'ui' => ['configuracion_ui' => 'required|array'],
            'security' => ['configuracion_seguridad' => 'required|array'],
            default => []
        };

        return Validator::make($request->all(), $rules);
    }

    /**
     * Prepare update data for specific section
     */
    private function prepareUpdateData(Request $request, string $section): array
    {
        return match ($section) {
            'notifications' => ['configuracion_notificaciones' => $request->configuracion_notificaciones],
            'billing' => ['configuracion_facturacion' => $request->configuracion_facturacion],
            'inventory' => ['configuracion_inventario' => $request->configuracion_inventario],
            'sales' => ['configuracion_ventas' => $request->configuracion_ventas],
            'backup' => ['configuracion_backup' => $request->configuracion_backup],
            'regional' => [
                'zona_horaria_predeterminada' => $request->zona_horaria_predeterminada,
                'idioma_predeterminado' => $request->idioma_predeterminado,
                'moneda_predeterminada' => $request->moneda_predeterminada,
            ],
            'ui' => ['configuracion_ui' => $request->configuracion_ui],
            'security' => ['configuracion_seguridad' => $request->configuracion_seguridad],
            default => []
        };
    }

    /**
     * Get default settings for section
     */
    private function getDefaultSettingsForSection(string $section): array
    {
        return match ($section) {
            'notifications' => ['configuracion_notificaciones' => null],
            'billing' => ['configuracion_facturacion' => null],
            'inventory' => ['configuracion_inventario' => null],
            'sales' => ['configuracion_ventas' => null],
            'backup' => ['configuracion_backup' => null],
            'regional' => [
                'zona_horaria_predeterminada' => 'America/Lima',
                'idioma_predeterminado' => 'es',
                'moneda_predeterminada' => 'PEN',
            ],
            'ui' => ['configuracion_ui' => null],
            'security' => ['configuracion_seguridad' => null],
            default => []
        };
    }

    /**
     * Get available timezones
     */
    private function getAvailableTimezones(): array
    {
        return [
            'America/Lima' => 'Lima (UTC-5)',
            'America/Bogota' => 'Bogotá (UTC-5)',
            'America/Mexico_City' => 'Ciudad de México (UTC-6)',
            'America/New_York' => 'Nueva York (UTC-5/-4)',
            'Europe/Madrid' => 'Madrid (UTC+1/+2)',
            'UTC' => 'UTC (UTC+0)',
        ];
    }

    /**
     * Get available languages
     */
    private function getAvailableLanguages(): array
    {
        return [
            'es' => 'Español',
            'en' => 'English',
            'pt' => 'Português',
            'fr' => 'Français',
        ];
    }

    /**
     * Get available currencies
     */
    private function getAvailableCurrencies(): array
    {
        return [
            'PEN' => 'Sol Peruano (S/)',
            'USD' => 'Dólar Americano ($)',
            'EUR' => 'Euro (€)',
            'COP' => 'Peso Colombiano ($)',
            'MXN' => 'Peso Mexicano ($)',
        ];
    }
}
