<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Rubro;
use Illuminate\Http\Request;

class RubroController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'RubroController - Implementación pendiente'], 501);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'RubroController - Implementación pendiente'], 501);
    }

    public function show(Request $request, $id)
    {
        return response()->json(['message' => 'RubroController - Implementación pendiente'], 501);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'RubroController - Implementación pendiente'], 501);
    }

    public function destroy(Request $request, $id)
    {
        return response()->json(['message' => 'RubroController - Implementación pendiente'], 501);
    }

    public function activos(Request $request)
    {
        return response()->json(['message' => 'RubroController - Implementación pendiente'], 501);
    }

    public function toggleStatus(Request $request, $id)
    {
        return response()->json(['message' => 'RubroController - Implementación pendiente'], 501);
    }

    public function empresas(Request $request, $id)
    {
        return response()->json(['message' => 'RubroController - Implementación pendiente'], 501);
    }
}
