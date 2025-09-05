<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Services\TenantService;
use Livewire\Component;

class Detalle extends Component
{
    public Cliente $cliente;
    public $activeTab = 'info';
    public $showEditForm = false;

    protected $listeners = [
        'clienteActualizado' => 'refresh',
    ];

    public function mount(Cliente $cliente)
    {
        // Verificar que el cliente pertenece a la empresa actual
        $empresa = null;
        try {
            $tenantService = app(TenantService::class);
            if ($tenantService) {
                $empresa = $tenantService->getEmpresa();
            }
        } catch (\Exception $e) {
            // Log error pero continúa
        }
        
        // Si no hay empresa, usar la primera disponible
        if (!$empresa) {
            $empresa = \App\Models\Empresa::first();
        }
        
        if (!$empresa || $cliente->empresa_id !== $empresa->id) {
            abort(404);
        }

        $this->cliente = $cliente;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function editarCliente()
    {
        $this->showEditForm = true;
    }

    public function cerrarEditForm()
    {
        $this->showEditForm = false;
    }

    public function refresh()
    {
        $this->cliente->refresh();
        $this->showEditForm = false;
    }

    public function toggleEstado()
    {
        try {
            $this->cliente->update([
                'activo' => !$this->cliente->activo
            ]);
            
            $status = $this->cliente->activo ? 'activado' : 'desactivado';
            session()->flash('message', "Cliente {$status} exitosamente.");
            
            $this->cliente->refresh();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado del cliente.');
        }
    }

    public function render()
    {
        // Cargar estadísticas del cliente
        $stats = [
            'total_ventas' => $this->cliente->ventas()->count(),
            'monto_total_comprado' => $this->cliente->ventas()
                ->where('estado', 'completada')
                ->sum('total_final'),
            'ultima_compra' => $this->cliente->ventas()
                ->orderBy('fecha_venta', 'desc')
                ->first()?->fecha_venta,
            'ticket_promedio' => $this->cliente->ventas()
                ->where('estado', 'completada')
                ->avg('total_final') ?: 0,
        ];

        // Historial de compras recientes
        $ventasRecientes = $this->cliente->ventas()
            ->with(['usuario'])
            ->orderBy('fecha_venta', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.clientes.detalle', [
            'stats' => $stats,
            'ventasRecientes' => $ventasRecientes
        ]);
    }
}
