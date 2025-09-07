<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use App\Services\TenantService;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use App\Models\VentaDetalle;

class PosController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index(): Response
    {
        $empresa = $this->tenantService->getEmpresa();
        
        if (!$empresa) {
            return Inertia::render('POS', [
                'error' => 'No hay empresa configurada'
            ]);
        }

        // Obtener productos disponibles
        $products = Producto::where('empresa_id', $empresa->id)
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
                    'imagen' => $producto->imagen,
                ];
            })
            ->values();

        // Obtener clientes
        $clients = Cliente::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->select(['id', 'nombre', 'email', 'telefono'])
            ->get();

        // Obtener categorías
        $categories = Categoria::where('empresa_id', $empresa->id)
            ->orderBy('nombre')
            ->pluck('nombre')
            ->unique()
            ->values();

        return Inertia::render('POS', [
            'products' => $products,
            'clients' => $clients,
            'categories' => $categories,
        ]);
    }

    public function processSale(Request $request)
    {
        $empresa = $this->tenantService->getEmpresa();
        
        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'items' => 'required|json',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:cash,card',
            'monto_pagado' => 'required|numeric|min:0',
        ]);

        $items = json_decode($validated['items'], true);
        
        if (empty($items)) {
            return redirect()->back()->with('error', 'No hay productos en el carrito');
        }

        DB::beginTransaction();
        try {
            // Crear la venta
            $venta = Venta::create([
                'empresa_id' => $empresa->id,
                'cliente_id' => $validated['cliente_id'],
                'numero_venta' => $this->generateSaleNumber($empresa->id),
                'fecha_venta' => now(),
                'subtotal' => $validated['total'],
                'total' => $validated['total'],
                'metodo_pago' => $validated['metodo_pago'],
                'monto_pagado' => $validated['monto_pagado'],
                'cambio' => $validated['monto_pagado'] - $validated['total'],
                'estado' => 'completed',
                'user_id' => Auth::id(),
            ]);

            // Crear los detalles de venta
            foreach ($items as $item) {
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'total' => $item['subtotal'],
                ]);

                // Actualizar stock del producto
                $producto = Producto::find($item['id']);
                if ($producto && $producto->stocks->isNotEmpty()) {
                    $stockActual = $producto->stocks->sum('cantidad_actual');
                    if ($stockActual >= $item['cantidad']) {
                        // Lógica FIFO para descontar del stock más antiguo primero
                        $this->updateProductStock($producto, $item['cantidad']);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'venta_id' => $venta->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateSaleNumber($empresaId)
    {
        $lastSale = Venta::where('empresa_id', $empresaId)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastSale ? (intval(substr($lastSale->numero_venta, -6)) + 1) : 1;
        
        return 'V-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
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
}
