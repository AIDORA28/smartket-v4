<?php

namespace App\Livewire\Configuraciones;

use Livewire\Component;
use App\Services\TenantService;
use App\Models\Empresa;
use App\Models\FeatureFlag;
use App\Models\User;
use App\Models\Sucursal;

class Index extends Component
{
    public $empresa;
    public $activeTab = 'general';
    
    // Estadísticas generales
    public $totalUsuarios = 0;
    public $totalSucursales = 0;
    public $featuresActivos = 0;
    
    public function mount()
    {
        $tenantService = app(TenantService::class);
        $this->empresa = $tenantService->getEmpresa();
        
        if (!$this->empresa) {
            $this->empresa = Empresa::first();
        }
        
        $this->loadStats();
    }
    
    public function loadStats()
    {
        if (!$this->empresa) return;
        
        $this->totalUsuarios = User::where('empresa_id', $this->empresa->id)->count();
        $this->totalSucursales = Sucursal::where('empresa_id', $this->empresa->id)->count();
        $this->featuresActivos = FeatureFlag::where('empresa_id', $this->empresa->id)
            ->where('enabled', true)
            ->count();
    }
    
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function render()
    {
        return view('livewire.configuraciones.index');
    }
}
