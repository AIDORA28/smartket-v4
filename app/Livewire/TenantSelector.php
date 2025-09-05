<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Services\TenantService;

class TenantSelector extends Component
{
    public $empresas;
    public $sucursales;
    public $empresaActual;
    public $sucursalActual;
    public $showDropdown = false;

    protected $tenantService;

    public function mount()
    {
        $this->tenantService = app(TenantService::class);
        $this->empresas = Empresa::where('activa', true)->get();
        $this->empresaActual = $this->tenantService->getEmpresa();
        $this->loadSucursales();
    }

    public function loadSucursales()
    {
        if ($this->empresaActual) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresaActual->id)
                                      ->where('activa', true)
                                      ->get();
            // TODO: Implementar sucursal actual desde session
            $this->sucursalActual = $this->sucursales->first();
        } else {
            $this->sucursales = collect();
            $this->sucursalActual = null;
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
        // Removido el dispatch que causaba problemas con Alpine
    }

    public function cambiarEmpresa($empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);
        $this->tenantService->setEmpresa($empresaId);
        
        $this->empresaActual = $empresa;
        $this->loadSucursales();
        $this->showDropdown = false;
        
        // Emit event for other components to refresh
        $this->dispatch('empresa-changed', $empresaId);
        
        // Redirigir al dashboard para refrescar todo el contexto
        return redirect()->route('dashboard');
    }

    public function cambiarSucursal($sucursalId)
    {
        // TODO: Implementar cambio de sucursal en session
        $sucursal = Sucursal::findOrFail($sucursalId);
        $this->sucursalActual = $sucursal;
        $this->showDropdown = false;
        
        // Emit event for other components to refresh
        $this->dispatch('sucursal-changed', $sucursalId);
    }

    public function render()
    {
        return view('livewire.tenant-selector');
    }
}
