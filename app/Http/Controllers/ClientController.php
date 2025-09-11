<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantService;
use App\Models\Cliente;

class ClientController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index(Request $request): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if (!$empresa) {
            return Inertia::render('Clients', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        // Filtros
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $credit = $request->get('credit', '');

        // Query base
        $query = Cliente::where('empresa_id', $empresa->id)
            ->withSum('ventas', 'total')
            ->withCount('ventas')
            ->with('ventas');

        // Aplicar filtros
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%")
                  ->orWhere('ruc', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('activo', true);
        } elseif ($status === 'inactive') {
            $query->where('activo', false);
        }

        if ($credit === 'with_credit') {
            $query->where('credito_limite', '>', 0);
        } elseif ($credit === 'no_credit') {
            $query->where('credito_limite', 0);
        } elseif ($credit === 'with_debt') {
            $query->where('credito_usado', '>', 0);
        }

        // Obtener clientes paginados
        $clients = $query->orderBy('nombre')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono,
                    'direccion' => $cliente->direccion,
                    'ruc' => $cliente->ruc,
                    'credito_limite' => $cliente->credito_limite ?? 0,
                    'credito_usado' => $cliente->credito_usado ?? 0,
                    'activo' => $cliente->activo,
                    'total_compras' => $cliente->ventas_sum_total ?? 0,
                    'ultima_compra' => $cliente->ventas->max('fecha_venta'),
                    'created_at' => $cliente->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Obtener estadísticas
        $stats = Cache::remember("clients_stats_{$empresa->id}", 600, function () use ($empresa) {
            $allClients = Cliente::where('empresa_id', $empresa->id)->get();
            
            return [
                'total_clientes' => $allClients->count(),
                'clientes_activos' => $allClients->where('activo', true)->count(),
                'con_credito' => $allClients->where('credito_limite', '>', 0)->count(),
                'credito_pendiente' => $allClients->sum('credito_usado'),
            ];
        });

        return Inertia::render('Clients', [
            'clients' => $clients,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'credit' => $credit,
            ]
        ]);
    }

    public function show($id): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $client = Cliente::where('empresa_id', $empresa->id)
            ->with(['ventas' => function($query) {
                $query->orderBy('fecha_venta', 'desc')->take(10);
            }])
            ->findOrFail($id);

        $clientData = [
            'id' => $client->id,
            'nombre' => $client->nombre,
            'email' => $client->email,
            'telefono' => $client->telefono,
            'direccion' => $client->direccion,
            'ruc' => $client->ruc,
            'credito_limite' => $client->credito_limite ?? 0,
            'credito_usado' => $client->credito_usado ?? 0,
            'activo' => $client->activo,
            'created_at' => $client->created_at->format('Y-m-d H:i:s'),
            'recent_sales' => $client->ventas->map(function($venta) {
                return [
                    'id' => $venta->id,
                    'numero_venta' => $venta->numero_venta,
                    'total' => $venta->total,
                    'fecha_venta' => $venta->fecha_venta->format('d/m/Y H:i'),
                ];
            }),
        ];

        return Inertia::render('ClientDetail', [
            'client' => $clientData
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ClientForm', [
            'client' => null
        ]);
    }

    public function store(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clientes,email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'ruc' => 'nullable|string|unique:clientes,ruc',
            'credito_limite' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        $validated['empresa_id'] = $empresa->id;
        $validated['credito_usado'] = 0;

        $client = Cliente::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado exitosamente');
    }

    public function edit($id): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $client = Cliente::where('empresa_id', $empresa->id)->findOrFail($id);

        return Inertia::render('ClientForm', [
            'client' => [
                'id' => $client->id,
                'nombre' => $client->nombre,
                'email' => $client->email,
                'telefono' => $client->telefono,
                'direccion' => $client->direccion,
                'ruc' => $client->ruc,
                'credito_limite' => $client->credito_limite ?? 0,
                'activo' => $client->activo,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $client = Cliente::where('empresa_id', $empresa->id)->findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clientes,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'ruc' => 'nullable|string|unique:clientes,ruc,' . $id,
            'credito_limite' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy($id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $client = Cliente::where('empresa_id', $empresa->id)->findOrFail($id);
        
        // Verificar si tiene ventas asociadas
        if ($client->ventas()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el cliente porque tiene ventas asociadas');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado exitosamente');
    }
}

