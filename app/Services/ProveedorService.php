<?php

namespace App\Services;

use App\Models\Proveedor;
use App\Models\Core\Empresa;
use Illuminate\Database\Eloquent\Collection;

class ProveedorService
{
    /**
     * Obtener proveedores de una empresa
     */
    public function getProveedoresPorEmpresa(int $empresaId): Collection
    {
        return Proveedor::where('empresa_id', $empresaId)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Crear nuevo proveedor
     */
    public function crearProveedor(array $datos): Proveedor
    {
        return Proveedor::create($datos);
    }

    /**
     * Actualizar proveedor
     */
    public function actualizarProveedor(int $id, array $datos): bool
    {
        $proveedor = Proveedor::findOrFail($id);
        return $proveedor->update($datos);
    }

    /**
     * Buscar proveedores
     */
    public function buscarProveedores(int $empresaId, string $termino): Collection
    {
        return Proveedor::where('empresa_id', $empresaId)
            ->where(function($query) use ($termino) {
                $query->where('nombre', 'like', "%{$termino}%")
                      ->orWhere('documento_numero', 'like', "%{$termino}%")
                      ->orWhere('email', 'like', "%{$termino}%");
            })
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Verificar si se puede eliminar proveedor
     */
    public function puedeEliminar(int $id): bool
    {
        $proveedor = Proveedor::findOrFail($id);
        return !$proveedor->tieneCompras();
    }

    /**
     * Eliminar proveedor
     */
    public function eliminarProveedor(int $id): bool
    {
        if (!$this->puedeEliminar($id)) {
            return false;
        }

        $proveedor = Proveedor::findOrFail($id);
        return $proveedor->delete();
    }
}

