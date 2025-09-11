<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MultiTenantController extends Controller
{
    /**
     * Obtener el contexto actual del usuario y opciones disponibles
     */
    public function getContext(Request $request)
    {
        $user = $request->user();
        $user->load(['empresaActiva', 'sucursalActiva']);

        // Obtener empresas accesibles
        $empresas = $user->empresasAccesibles()->get()->map(function ($empresa) use ($user) {
            return [
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
                'rut' => $empresa->rut,
                'rol' => $empresa->pivot->rol_en_empresa,
                'sucursales_count' => $empresa->sucursales()->where('activa', true)->count(),
                'activa' => $empresa->activa
            ];
        });

        // Obtener sucursales accesibles
        $sucursales = $user->sucursalesAccesibles()->with('empresa')->get()->map(function ($sucursal) use ($user) {
            return [
                'id' => $sucursal->id,
                'nombre' => $sucursal->nombre,
                'empresa' => [
                    'id' => $sucursal->empresa->id,
                    'nombre' => $sucursal->empresa->nombre
                ],
                'rol' => $sucursal->pivot->rol_en_sucursal,
                'activa' => $sucursal->activa
            ];
        });

        // Obtener contexto actual
        $currentContext = $user->getCurrentContext();

        return response()->json([
            'current_context' => $currentContext,
            'empresas_accesibles' => $empresas,
            'sucursales_accesibles' => $sucursales,
            'needs_selection' => $user->needsContextSelection(),
            'total_empresas' => $empresas->count(),
            'total_sucursales' => $sucursales->count()
        ]);
    }

    /**
     * Cambiar contexto de empresa
     */
    public function switchEmpresa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => 'required|integer|exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $empresaId = $request->empresa_id;

        // Verificar acceso
        if (!$user->canAccessEmpresa($empresaId)) {
            return response()->json(['error' => 'No tienes acceso a esta empresa'], 403);
        }

        // Obtener empresa
        $empresa = Empresa::findOrFail($empresaId);
        if (!$empresa->activa) {
            return response()->json(['error' => 'La empresa no está activa'], 422);
        }

        // Obtener sucursal por defecto o mantener la actual si es válida
        $sucursalId = null;
        if ($user->sucursal_activa_id && $user->canAccessSucursal($user->sucursal_activa_id)) {
            // Verificar que la sucursal actual pertenezca a la nueva empresa
            $sucursalActual = Sucursal::find($user->sucursal_activa_id);
            if ($sucursalActual && $sucursalActual->empresa_id == $empresaId) {
                $sucursalId = $user->sucursal_activa_id;
            }
        }

        // Si no hay sucursal válida, tomar la primera disponible
        if (!$sucursalId) {
            $sucursalDisponible = $user->sucursalesAccesibles()
                                      ->where('empresa_id', $empresaId)
                                      ->first();
            $sucursalId = $sucursalDisponible ? $sucursalDisponible->id : null;
        }

        // Cambiar contexto
        $success = $user->switchContext($empresaId, $sucursalId);

        if (!$success) {
            return response()->json(['error' => 'No se pudo cambiar el contexto'], 500);
        }

        // Obtener rol en la nueva empresa
        $rol = $user->getRolInEmpresa($empresaId);

        return response()->json([
            'message' => 'Contexto cambiado exitosamente',
            'new_context' => [
                'empresa' => [
                    'id' => $empresa->id,
                    'nombre' => $empresa->nombre,
                    'rol' => $rol
                ],
                'sucursal' => $sucursalId ? [
                    'id' => $sucursalId,
                    'nombre' => Sucursal::find($sucursalId)->nombre
                ] : null
            ]
        ]);
    }

    /**
     * Cambiar contexto de sucursal
     */
    public function switchSucursal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sucursal_id' => 'required|integer|exists:sucursales,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $sucursalId = $request->sucursal_id;

        // Verificar acceso
        if (!$user->canAccessSucursal($sucursalId)) {
            return response()->json(['error' => 'No tienes acceso a esta sucursal'], 403);
        }

        // Obtener sucursal
        $sucursal = Sucursal::with('empresa')->findOrFail($sucursalId);
        if (!$sucursal->activa) {
            return response()->json(['error' => 'La sucursal no está activa'], 422);
        }

        // Verificar que tenga acceso a la empresa de la sucursal
        if (!$user->canAccessEmpresa($sucursal->empresa_id)) {
            return response()->json(['error' => 'No tienes acceso a la empresa de esta sucursal'], 403);
        }

        // Cambiar contexto
        $success = $user->switchContext($sucursal->empresa_id, $sucursalId);

        if (!$success) {
            return response()->json(['error' => 'No se pudo cambiar el contexto'], 500);
        }

        // Obtener roles
        $rolEmpresa = $user->getRolInEmpresa($sucursal->empresa_id);
        $rolSucursal = $user->getRolInSucursal($sucursalId);

        return response()->json([
            'message' => 'Contexto cambiado exitosamente',
            'new_context' => [
                'empresa' => [
                    'id' => $sucursal->empresa->id,
                    'nombre' => $sucursal->empresa->nombre,
                    'rol' => $rolEmpresa
                ],
                'sucursal' => [
                    'id' => $sucursal->id,
                    'nombre' => $sucursal->nombre,
                    'rol' => $rolSucursal
                ]
            ]
        ]);
    }

    /**
     * Otorgar acceso a empresa a un usuario
     */
    public function grantEmpresaAccess(Request $request)
    {
        $currentUser = $request->user();

        // Solo owners y gerentes pueden otorgar accesos
        if (!$currentUser->isOwner() && $currentUser->rol_principal !== 'gerente') {
            return response()->json(['error' => 'Sin permisos para otorgar accesos'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'empresa_id' => 'required|integer|exists:empresas,id',
            'rol_en_empresa' => 'required|string|in:owner,gerente,admin'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $targetUser = User::findOrFail($request->user_id);
        $empresaId = $request->empresa_id;
        $rol = $request->rol_en_empresa;

        // Verificar que el usuario actual tenga acceso a la empresa
        if (!$currentUser->canAccessEmpresa($empresaId)) {
            return response()->json(['error' => 'No tienes acceso a esta empresa'], 403);
        }

        // Otorgar acceso
        $success = $targetUser->grantEmpresaAccess($empresaId, $rol);

        if (!$success) {
            return response()->json(['error' => 'No se pudo otorgar el acceso'], 500);
        }

        return response()->json([
            'message' => 'Acceso otorgado exitosamente',
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email
            ],
            'empresa_id' => $empresaId,
            'rol_otorgado' => $rol
        ]);
    }

    /**
     * Revocar acceso a empresa de un usuario
     */
    public function revokeEmpresaAccess(Request $request)
    {
        $currentUser = $request->user();

        // Solo owners y gerentes pueden revocar accesos
        if (!$currentUser->isOwner() && $currentUser->rol_principal !== 'gerente') {
            return response()->json(['error' => 'Sin permisos para revocar accesos'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'empresa_id' => 'required|integer|exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $targetUser = User::findOrFail($request->user_id);
        $empresaId = $request->empresa_id;

        // No se puede revocar acceso a sí mismo
        if ($targetUser->id === $currentUser->id) {
            return response()->json(['error' => 'No puedes revocar tu propio acceso'], 422);
        }

        // Verificar que el usuario actual tenga acceso a la empresa
        if (!$currentUser->canAccessEmpresa($empresaId)) {
            return response()->json(['error' => 'No tienes acceso a esta empresa'], 403);
        }

        // Revocar acceso
        $success = $targetUser->revokeEmpresaAccess($empresaId);

        if (!$success) {
            return response()->json(['error' => 'No se pudo revocar el acceso o el usuario no tenía acceso'], 404);
        }

        return response()->json([
            'message' => 'Acceso revocado exitosamente',
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email
            ],
            'empresa_id' => $empresaId
        ]);
    }
}
