<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Sucursal;

class TenantService
{
    private ?Empresa $currentEmpresa = null;
    private ?Sucursal $currentSucursal = null;

    public function setEmpresa(int $empresaId): void
    {
        $this->currentEmpresa = Empresa::findOrFail($empresaId);
        // Auto-seleccionar sucursal principal si no hay una seleccionada
        if (!$this->currentSucursal) {
            $this->currentSucursal = $this->currentEmpresa->getSucursalPrincipal();
        }
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->currentEmpresa;
    }

    public function getEmpresaId(): ?int
    {
        return $this->currentEmpresa?->id;
    }

    public function setSucursal(int $sucursalId): void
    {
        // Verificar que la sucursal pertenece a la empresa actual
        if ($this->currentEmpresa) {
            $sucursal = $this->currentEmpresa->sucursales()->findOrFail($sucursalId);
            $this->currentSucursal = $sucursal;
            
            // Guardar en sesión
            session(['sucursal_id' => $sucursalId]);
        }
    }

    public function getSucursal(): ?Sucursal
    {
        // Si hay una sucursal en sesión, cargarla
        if (!$this->currentSucursal && session('sucursal_id') && $this->currentEmpresa) {
            try {
                $this->currentSucursal = $this->currentEmpresa->sucursales()->findOrFail(session('sucursal_id'));
            } catch (\Exception $e) {
                // Si falla, usar sucursal principal
                $this->currentSucursal = $this->currentEmpresa->getSucursalPrincipal();
            }
        }
        
        return $this->currentSucursal;
    }

    public function getSucursalId(): ?int
    {
        return $this->getSucursal()?->id;
    }

    public function hasFeature(string $feature): bool
    {
        if (!$this->currentEmpresa) {
            return false;
        }

        return $this->currentEmpresa->hasFeature($feature);
    }

    public function validateEmpresaAccess(int $empresaId): bool
    {
        return $this->currentEmpresa && $this->currentEmpresa->id === $empresaId;
    }

    public function getSucursalPrincipal()
    {
        return $this->currentEmpresa?->getSucursalPrincipal();
    }

    public function getRubroPrincipal()
    {
        return $this->currentEmpresa?->getRubroPrincipal();
    }

    public function clearContext(): void
    {
        $this->currentEmpresa = null;
        $this->currentSucursal = null;
        session()->forget(['empresa_id', 'sucursal_id']);
    }
}
