<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Empresa;
use App\Services\TenantService;

class TenantSelector extends Component
{
    public $empresas;
    public $empresaActual;
    public $sucursalActual;

    protected $tenantService;

    public function mount()
    {
        $this->tenantService = app(TenantService::class);
        $this->empresas = Empresa::where('activa', true)->get();
        $this->empresaActual = $this->tenantService->getEmpresa();
        $this->sucursalActual = null; // TODO: Implementar sucursal actual
    }

    public function cambiarEmpresa($empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);
        $this->tenantService->setEmpresa($empresaId);
        
        $this->empresaActual = $empresa;
        
        // Redirigir al dashboard para refrescar todo el contexto
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.tenant-selector');
    }
}
