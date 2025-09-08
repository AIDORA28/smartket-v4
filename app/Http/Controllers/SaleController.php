<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use App\Services\TenantService;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\Producto;
use Carbon\Carbon;

class SaleController extends Controller
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
            return Inertia::render('Sales', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        $query = Venta::where('empresa_id', $empresa->id)
            ->with(['cliente', 'detalles.producto']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_venta', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($clienteQuery) use ($search) {
                      $clienteQuery->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->get('metodo_pago'));
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_venta', '>=', $request->get('fecha_inicio'));
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_venta', '<=', $request->get('fecha_fin'));
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->get('cliente_id'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'fecha_venta');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSorts = ['fecha_venta', 'numero_venta', 'total', 'estado', 'metodo_pago'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $sales = $query->paginate(15);

        // Estadísticas rápidas
        $stats = $this->getSalesStats($empresa->id, $request);

        // Opciones para filtros
        $filterOptions = [
            'clientes' => Cliente::where('empresa_id', $empresa->id)
                ->where('activo', true)
                ->select('id', 'nombre')
                ->orderBy('nombre')
                ->get(),
            'estados' => ['completed', 'pending', 'cancelled'],
            'metodos_pago' => ['cash', 'card'],
        ];

        return Inertia::render('Sales', [
            'sales' => $sales,
            'stats' => $stats,
            'filterOptions' => $filterOptions,
            'filters' => $request->only(['search', 'estado', 'metodo_pago', 'fecha_inicio', 'fecha_fin', 'cliente_id']),
            'sort' => [
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
            ],
        ]);
    }

    public function show($id): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $sale = Venta::where('empresa_id', $empresa->id)
            ->where('id', $id)
            ->with([
                'cliente',
                'detalles.producto.categoria',
                'user'
            ])
            ->firstOrFail();

        return Inertia::render('Sales/Show', [
            'sale' => $sale,
        ]);
    }

    public function edit($id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $sale = Venta::where('empresa_id', $empresa->id)
            ->where('id', $id)
            ->with([
                'cliente',
                'detalles.producto'
            ])
            ->firstOrFail();

        // Solo permitir editar ventas pendientes
        if ($sale->estado !== 'pending') {
            return Inertia::render('Sales/Show', [
                'sale' => $sale,
                'error' => 'Solo se pueden editar ventas pendientes'
            ]);
        }

        $clientes = Cliente::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->select('id', 'nombre', 'email', 'telefono')
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->with(['stocks', 'categoria'])
            ->get()
            ->filter(function($producto) {
                $stockTotal = $producto->stocks->sum('cantidad_actual') ?? 0;
                return $stockTotal > 0;
            })
            ->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio_venta,
                    'stock' => $producto->stocks->sum('cantidad_actual') ?? 0,
                    'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                ];
            })
            ->values();

        return Inertia::render('Sales/Edit', [
            'sale' => $sale,
            'clientes' => $clientes,
            'productos' => $productos,
        ]);
    }

    public function update(Request $request, $id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $sale = Venta::where('empresa_id', $empresa->id)
            ->where('id', $id)
            ->firstOrFail();

        // Solo permitir editar ventas pendientes
        if ($sale->estado !== 'pending') {
            return redirect()->route('sales.show', $id)
                ->with('error', 'Solo se pueden editar ventas pendientes');
        }

        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'items' => 'required|json',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:cash,card',
            'monto_pagado' => 'nullable|numeric|min:0',
            'estado' => 'required|in:pending,completed,cancelled',
        ]);

        $items = json_decode($validated['items'], true);
        
        if (empty($items)) {
            return redirect()->back()->with('error', 'No hay productos en la venta');
        }

        DB::beginTransaction();
        try {
            // Restaurar stock de los items anteriores si la venta estaba completada
            if ($sale->estado === 'completed') {
                foreach ($sale->detalles as $detalle) {
                    $this->restoreProductStock($detalle->producto, $detalle->cantidad);
                }
            }

            // Actualizar la venta
            $sale->update([
                'cliente_id' => $validated['cliente_id'],
                'subtotal' => $validated['total'],
                'total' => $validated['total'],
                'metodo_pago' => $validated['metodo_pago'],
                'monto_pagado' => $validated['monto_pagado'] ?? $validated['total'],
                'cambio' => ($validated['monto_pagado'] ?? $validated['total']) - $validated['total'],
                'estado' => $validated['estado'],
                'fecha_venta' => now(),
            ]);

            // Eliminar detalles anteriores
            $sale->detalles()->delete();

            // Crear nuevos detalles
            foreach ($items as $item) {
                VentaDetalle::create([
                    'venta_id' => $sale->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'total' => $item['subtotal'],
                ]);

                // Actualizar stock solo si la venta se completa
                if ($validated['estado'] === 'completed') {
                    $producto = Producto::find($item['id']);
                    if ($producto) {
                        $this->updateProductStock($producto, $item['cantidad']);
                    }
                }
            }

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Venta actualizada exitosamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $sale = Venta::where('empresa_id', $empresa->id)
            ->where('id', $id)
            ->firstOrFail();

        // Solo permitir eliminar ventas pendientes
        if ($sale->estado === 'completed') {
            return redirect()->back()->with('error', 'No se pueden eliminar ventas completadas');
        }

        DB::beginTransaction();
        try {
            // Eliminar detalles
            $sale->detalles()->delete();
            
            // Eliminar venta
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Venta eliminada exitosamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $sale = Venta::where('empresa_id', $empresa->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($sale->estado === 'cancelled') {
            return redirect()->back()->with('error', 'La venta ya está cancelada');
        }

        DB::beginTransaction();
        try {
            // Si la venta estaba completada, restaurar stock
            if ($sale->estado === 'completed') {
                foreach ($sale->detalles as $detalle) {
                    $this->restoreProductStock($detalle->producto, $detalle->cantidad);
                }
            }

            $sale->update(['estado' => 'cancelled']);

            DB::commit();

            return redirect()->back()->with('success', 'Venta cancelada exitosamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al cancelar la venta: ' . $e->getMessage());
        }
    }

    public function complete($id)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $sale = Venta::where('empresa_id', $empresa->id)
            ->where('id', $id)
            ->with('detalles.producto')
            ->firstOrFail();

        if ($sale->estado !== 'pending') {
            return redirect()->back()->with('error', 'Solo se pueden completar ventas pendientes');
        }

        DB::beginTransaction();
        try {
            // Verificar stock disponible
            foreach ($sale->detalles as $detalle) {
                $stockDisponible = $detalle->producto->stocks->sum('cantidad_actual') ?? 0;
                if ($stockDisponible < $detalle->cantidad) {
                    throw new \Exception("Stock insuficiente para el producto: {$detalle->producto->nombre}");
                }
            }

            // Actualizar stock de cada producto
            foreach ($sale->detalles as $detalle) {
                $this->updateProductStock($detalle->producto, $detalle->cantidad);
            }

            $sale->update([
                'estado' => 'completed',
                'fecha_venta' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Venta completada exitosamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al completar la venta: ' . $e->getMessage());
        }
    }

    private function getSalesStats($empresaId, $request)
    {
        $query = Venta::where('empresa_id', $empresaId);

        // Aplicar los mismos filtros que en la lista
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_venta', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($clienteQuery) use ($search) {
                      $clienteQuery->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->get('metodo_pago'));
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_venta', '>=', $request->get('fecha_inicio'));
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_venta', '<=', $request->get('fecha_fin'));
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->get('cliente_id'));
        }

        return [
            'total_ventas' => $query->count(),
            'total_ingresos' => $query->where('estado', 'completed')->sum('total'),
            'ticket_promedio' => $query->where('estado', 'completed')->avg('total'),
            'ventas_completadas' => $query->where('estado', 'completed')->count(),
            'ventas_pendientes' => $query->where('estado', 'pending')->count(),
            'ventas_canceladas' => $query->where('estado', 'cancelled')->count(),
        ];
    }

    private function updateProductStock($producto, $cantidadVendida)
    {
        $cantidadRestante = $cantidadVendida;
        
        // Obtener stocks ordenados por fecha de vencimiento (FIFO)
        $stocks = $producto->stocks()
            ->where('cantidad_actual', '>', 0)
            ->join('lotes', 'producto_stocks.lote_id', '=', 'lotes.id')
            ->orderBy('lotes.fecha_vencimiento', 'asc')
            ->select('producto_stocks.*')
            ->get();

        foreach ($stocks as $stock) {
            if ($cantidadRestante <= 0) break;

            $cantidadADescontar = min($cantidadRestante, $stock->cantidad_actual);
            
            $stock->decrement('cantidad_actual', $cantidadADescontar);
            $cantidadRestante -= $cantidadADescontar;
        }
    }

    private function restoreProductStock($producto, $cantidadARestaurar)
    {
        // Obtener el primer stock disponible (o crear uno nuevo si no existe)
        $stock = $producto->stocks()->first();
        
        if ($stock) {
            $stock->increment('cantidad_actual', $cantidadARestaurar);
        } else {
            // Si no hay stock, crear uno temporal
            // Esto es un caso edge que debería manejarse mejor en producción
            $producto->stocks()->create([
                'cantidad_inicial' => $cantidadARestaurar,
                'cantidad_actual' => $cantidadARestaurar,
                'lote_id' => null, // Crear un lote temporal si es necesario
            ]);
        }
    }
}
