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

    public function switchSucursal(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|integer|exists:sucursales,id'
        ]);

        try {
            $this->tenantService->setSucursal($request->sucursal_id);
            
            return redirect()->back()->with('success', 'Sucursal cambiada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo cambiar la sucursal');
        }
    }
}
