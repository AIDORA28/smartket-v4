<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TenantService;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\UnidadMedida;
use App\Models\ProductoStock;

class ProductController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function store(Request $request)
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            if (!$empresa) {
                return back()->withErrors(['error' => 'No hay empresa configurada']);
            }

            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'codigo_barras' => 'nullable|string|unique:productos,codigo_barras',
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
                'unidad_medida_id' => 'required|exists:unidades_medida,id',
                'precio_compra' => 'nullable|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'stock_minimo' => 'nullable|integer|min:0',
                'stock_inicial' => 'nullable|integer|min:0',
                'activo' => 'boolean',
            ]);

            // Agregar empresa_id
            $validated['empresa_id'] = $empresa->id;
            $validated['stock_minimo'] = $validated['stock_minimo'] ?? 0;

            DB::beginTransaction();

            // Crear el producto
            $producto = Producto::create($validated);

            // Crear stock inicial si se especifica
            if (isset($validated['stock_inicial']) && $validated['stock_inicial'] > 0) {
                ProductoStock::create([
                    'empresa_id' => $empresa->id,
                    'producto_id' => $producto->id,
                    'sucursal_id' => $this->tenantService->getSucursal()?->id ?? 1,
                    'cantidad_actual' => $validated['stock_inicial'],
                    'cantidad_reservada' => 0,
                    'costo_promedio' => $validated['precio_compra'] ?? 0,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Producto creado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear producto: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear producto: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            $producto = Producto::where('empresa_id', $empresa->id)->findOrFail($id);

            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'codigo_barras' => 'nullable|string|unique:productos,codigo_barras,' . $id,
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
                'unidad_medida_id' => 'required|exists:unidades_medida,id',
                'precio_compra' => 'nullable|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'stock_minimo' => 'nullable|integer|min:0',
                'activo' => 'boolean',
            ]);

            $producto->update($validated);

            return back()->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar producto: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar producto: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $empresa = $this->tenantService->getEmpresa();
            
            $producto = Producto::where('empresa_id', $empresa->id)->findOrFail($id);
            
            // Verificar si tiene ventas asociadas
            if ($producto->ventaDetalles()->exists()) {
                return back()->withErrors(['error' => 'No se puede eliminar el producto porque tiene ventas asociadas']);
            }

            $producto->delete();

            return back()->with('success', 'Producto eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar producto: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar producto: ' . $e->getMessage()]);
        }
    }
}
