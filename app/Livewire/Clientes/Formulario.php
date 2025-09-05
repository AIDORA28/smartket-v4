<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Services\TenantService;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Formulario extends Component
{
    // Propiedades del cliente
    public $cliente_id = null;
    public $nombre = '';
    public $tipo_documento = 'DNI';
    public $numero_documento = '';
    public $email = '';
    public $telefono = '';
    public $direccion = '';
    public $fecha_nacimiento = '';
    public $genero = '';
    public $es_empresa = false;
    public $limite_credito = 0;
    public $permite_credito = false;
    public $descuento_porcentaje = 0;
    public $activo = true;
    
    // Control de formulario
    public $isEditing = false;
    public $showModal = false;

    protected $listeners = [
        'abrirFormulario' => 'abrirFormulario',
        'cerrarFormulario' => 'cerrarFormulario',
    ];

    public function mount($cliente = null)
    {
        if ($cliente) {
            $this->isEditing = true;
            $this->cliente_id = $cliente->id;
            $this->cargarCliente($cliente);
        }
    }

    protected function rules()
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
        
        // Si no hay empresa, usar la primera disponible
        if (!$empresa) {
            $empresa = \App\Models\Empresa::first();
        }
        
        $empresaId = $empresa?->id ?? 1;
        
        return [
            'nombre' => 'required|string|max:150|min:2',
            'tipo_documento' => 'required|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento' => [
                'required',
                'string',
                'max:20',
                Rule::unique('clientes')->where(function ($query) use ($empresaId) {
                    return $query->where('empresa_id', $empresaId);
                })->ignore($this->cliente_id)
            ],
            'email' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero' => 'nullable|in:M,F,O',
            'es_empresa' => 'boolean',
            'limite_credito' => 'nullable|numeric|min:0|max:999999.99',
            'permite_credito' => 'boolean',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'activo' => 'boolean',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
        'tipo_documento.required' => 'Seleccione un tipo de documento.',
        'numero_documento.required' => 'El número de documento es obligatorio.',
        'numero_documento.unique' => 'Este número de documento ya está registrado.',
        'email.email' => 'Ingrese un email válido.',
        'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        'limite_credito.min' => 'El límite de crédito no puede ser negativo.',
        'descuento_porcentaje.max' => 'El descuento no puede ser mayor a 100%.',
    ];

    public function abrirFormulario($clienteId = null)
    {
        if ($clienteId) {
            $cliente = Cliente::find($clienteId);
            if ($cliente) {
                $this->isEditing = true;
                $this->cliente_id = $cliente->id;
                $this->cargarCliente($cliente);
            }
        } else {
            $this->resetForm();
            $this->isEditing = false;
        }
        
        $this->showModal = true;
    }

    public function cerrarFormulario()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetErrorBag();
    }

    private function cargarCliente($cliente)
    {
        $this->nombre = $cliente->nombre;
        $this->tipo_documento = $cliente->tipo_documento;
        $this->numero_documento = $cliente->numero_documento;
        $this->email = $cliente->email;
        $this->telefono = $cliente->telefono;
        $this->direccion = $cliente->direccion;
        $this->fecha_nacimiento = $cliente->fecha_nacimiento?->format('Y-m-d');
        $this->genero = $cliente->genero;
        $this->es_empresa = $cliente->es_empresa;
        $this->limite_credito = $cliente->limite_credito;
        $this->permite_credito = $cliente->permite_credito;
        $this->descuento_porcentaje = $cliente->descuento_porcentaje;
        $this->activo = $cliente->activo;
    }

    private function resetForm()
    {
        $this->cliente_id = null;
        $this->nombre = '';
        $this->tipo_documento = 'DNI';
        $this->numero_documento = '';
        $this->email = '';
        $this->telefono = '';
        $this->direccion = '';
        $this->fecha_nacimiento = '';
        $this->genero = '';
        $this->es_empresa = false;
        $this->limite_credito = 0;
        $this->permite_credito = false;
        $this->descuento_porcentaje = 0;
        $this->activo = true;
    }

    public function guardar()
    {
        $this->validate();

        try {
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
            
            // Si no hay empresa, usar la primera disponible
            if (!$empresa) {
                $empresa = \App\Models\Empresa::first();
            }
            
            if (!$empresa) {
                throw new \Exception('No se pudo obtener la empresa');
            }

            $data = [
                'empresa_id' => $empresa->id,
                'nombre' => trim($this->nombre),
                'tipo_documento' => $this->tipo_documento,
                'numero_documento' => trim($this->numero_documento),
                'email' => $this->email ? trim($this->email) : null,
                'telefono' => $this->telefono ? trim($this->telefono) : null,
                'direccion' => $this->direccion ? trim($this->direccion) : null,
                'fecha_nacimiento' => $this->fecha_nacimiento ?: null,
                'genero' => $this->genero ?: null,
                'es_empresa' => $this->es_empresa,
                'limite_credito' => $this->limite_credito ?: 0,
                'permite_credito' => $this->permite_credito,
                'descuento_porcentaje' => $this->descuento_porcentaje ?: 0,
                'activo' => $this->activo,
            ];

            if ($this->isEditing) {
                $cliente = Cliente::findOrFail($this->cliente_id);
                $cliente->update($data);
                
                session()->flash('message', 'Cliente actualizado exitosamente.');
                $this->dispatch('clienteActualizado', $cliente->id);
            } else {
                $cliente = Cliente::create($data);
                
                session()->flash('message', 'Cliente creado exitosamente.');
                $this->dispatch('clienteCreado', $cliente->id);
            }

            $this->cerrarFormulario();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el cliente: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.clientes.formulario');
    }
}
