<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }

    public function show(Request $request, $id)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }

    public function destroy(Request $request, $id)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }

    public function activos(Request $request)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }

    public function visibles(Request $request)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }

    public function toggleStatus(Request $request, $id)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }

    public function empresas(Request $request, $id)
    {
        return response()->json(['message' => 'PlanController - Implementación pendiente'], 501);
    }
}
