<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'SucursalController - Implementación pendiente'], 501);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'SucursalController - Implementación pendiente'], 501);
    }

    public function show(Request $request, $id)
    {
        return response()->json(['message' => 'SucursalController - Implementación pendiente'], 501);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'SucursalController - Implementación pendiente'], 501);
    }

    public function destroy(Request $request, $id)
    {
        return response()->json(['message' => 'SucursalController - Implementación pendiente'], 501);
    }

    public function byEmpresa(Request $request, $empresaId)
    {
        return response()->json(['message' => 'SucursalController - Implementación pendiente'], 501);
    }

    public function toggleStatus(Request $request, $id)
    {
        return response()->json(['message' => 'SucursalController - Implementación pendiente'], 501);
    }
}
