<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TenantService;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function switchEmpresa(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|integer|exists:empresas,id'
        ]);

        try {
            $this->tenantService->setEmpresa($request->empresa_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Empresa cambiada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo cambiar la empresa'
            ], 500);
        }
    }

    public function switchSucursal(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|integer|exists:sucursales,id'
        ]);

        try {
            $this->tenantService->setSucursal($request->sucursal_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Sucursal cambiada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo cambiar la sucursal'
            ], 500);
        }
    }
}
