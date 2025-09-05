<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Services\TenantService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Lista extends Component
{
    use WithPagination;

    public $search = '';
    public $filterEstado = '';
    public $filterTipoDocumento = '';
    public $filterCredito = '';
    public $sortBy = 'nombre';
    public $sortDirection = 'asc';
    
    public $showClienteForm = false;
    public $clienteSeleccionado = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'filterTipoDocumento' => ['except' => ''],
        'filterCredito' => ['except' => ''],
    ];

    protected $listeners = [
        'clienteCreado' => 'refresh',
        'clienteActualizado' => 'refresh',
        'clienteEliminado' => 'refresh',
    ];

    public function mount()
    {
        // Inicializar filtros si es necesario
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterEstado()
    {
        $this->resetPage();
    }

    public function updatedFilterTipoDocumento()
    {
        $this->resetPage();
    }

    public function updatedFilterCredito()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->resetPage();
    }

    public function abrirFormularioCrear()
    {
        $this->clienteSeleccionado = null;
        $this->showClienteForm = true;
    }

    public function editarCliente($clienteId)
    {
        $this->clienteSeleccionado = Cliente::find($clienteId);
        $this->showClienteForm = true;
    }

    public function verCliente($clienteId)
    {
        return redirect()->route('clientes.show', $clienteId);
    }

    public function refresh()
    {
        $this->showClienteForm = false;
        $this->clienteSeleccionado = null;
        $this->resetPage();
    }

    public function render()
    {
        // Obtener empresa con fallback robusto
        $empresa = null;
        try {
            $tenantService = app(TenantService::class);
            if ($tenantService) {
                $empresa = $tenantService->getEmpresa();
            }
        } catch (\Exception $e) {
            // Log error pero continúa
        }
        
        // Si no hay empresa desde TenantService, intentar alternativas
        if (!$empresa) {
            $user = Auth::user();
            if ($user) {
                $empresa = $user->empresas?->first();
            }
        }
        
        // Si aún no hay empresa, usar la primera disponible
        if (!$empresa) {
            $empresa = \App\Models\Empresa::first();
        }
        
        if (!$empresa) {
            // Fallback absoluto
            return view('livewire.clientes.lista', [
                'clientes' => collect(),
                'stats' => [
                    'total_clientes' => 0,
                    'clientes_activos' => 0,
                    'con_credito' => 0,
                    'credito_pendiente' => 0
                ]
            ]);
        }

        $query = Cliente::where('empresa_id', $empresa->id);

        // Aplicar filtro de búsqueda
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', $search)
                  ->orWhere('numero_documento', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('telefono', 'like', $search);
            });
        }

        // Filtro por estado
        if ($this->filterEstado !== '') {
            $query->where('activo', $this->filterEstado);
        }

        // Filtro por tipo de documento
        if (!empty($this->filterTipoDocumento)) {
            $query->where('tipo_documento', $this->filterTipoDocumento);
        }

        // Filtro por crédito
        if ($this->filterCredito !== '') {
            if ($this->filterCredito === '1') {
                $query->where('permite_credito', true);
            } elseif ($this->filterCredito === '0') {
                $query->where('permite_credito', false);
            }
        }

        // Aplicar ordenamiento
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener datos paginados
        $clientes = $query->paginate(12);

        // Estadísticas rápidas
        $stats = [
            'total_clientes' => Cliente::where('empresa_id', $empresa->id)->count(),
            'clientes_activos' => Cliente::where('empresa_id', $empresa->id)->where('activo', true)->count(),
            'con_credito' => Cliente::where('empresa_id', $empresa->id)->where('permite_credito', true)->count(),
            'credito_pendiente' => Cliente::where('empresa_id', $empresa->id)
                ->where('credito_usado', '>', 0)
                ->sum('credito_usado')
        ];

        return view('livewire.clientes.lista', [
            'clientes' => $clientes,
            'stats' => $stats
        ]);
    }
}
