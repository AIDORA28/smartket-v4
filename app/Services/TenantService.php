<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;

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
        // Si no hay empresa actual, intentar obtener una automáticamente
        if (!$this->currentEmpresa) {
            $this->initializeEmpresa();
        }
        
        return $this->currentEmpresa;
    }
    
    /**
     * Inicializar empresa automáticamente
     */
    private function initializeEmpresa(): void
    {
        // Intento 1: Empresa en sesión
        if (session('empresa_id')) {
            try {
                $this->currentEmpresa = Empresa::findOrFail(session('empresa_id'));
                return;
            } catch (\Exception $e) {
                // Continuar con otros métodos
            }
        }
        
        // Intento 2: Usuario autenticado con empresas
        if (Auth::check()) {
            $user = Auth::user();
            // Verificar si el usuario tiene relación con empresas
            if (method_exists($user, 'empresa_id') && $user->empresa_id) {
                try {
                    $this->currentEmpresa = Empresa::findOrFail($user->empresa_id);
                    return;
                } catch (\Exception $e) {
                    // Continuar con otros métodos
                }
            }
        }
        
        // Intento 3: Primera empresa disponible (fallback)
        $this->currentEmpresa = Empresa::first();
        
        // Guardar en sesión para futuras solicitudes
        if ($this->currentEmpresa) {
            session(['empresa_id' => $this->currentEmpresa->id]);
        }
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
