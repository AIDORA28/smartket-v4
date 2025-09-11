<?php

namespace App\Services;

use App\Models\Reporte;
use App\Models\ReporteTemplate;
use App\Models\Sales\Venta;
use App\Models\Inventory\Producto;
use App\Models\Inventory\ProductoStock;
use App\Models\Lote;
use App\Models\CajaSesion;
use App\Models\Compra;
use App\Models\AnalyticsEvento;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
// use Maatwebsite\Excel\Facades\Excel;
// use Barryvdh\DomPDF\Facade\Pdf;

class ReporteService
{
    /**
     * Generar reporte desde template
     */
    public function generarReporteDesdeTemplate(
        int $templateId, 
        int $usuarioId, 
        array $filtros = [], 
        string $formato = Reporte::FORMATO_HTML
    ): Reporte {
        $template = ReporteTemplate::findOrFail($templateId);
        
        // Crear registro de reporte
        $reporte = $template->crearReporte($usuarioId, $filtros, $formato);
        
        try {
            // Generar datos del reporte
            $datos = $this->generarDatos($reporte);
            
            // Generar archivo según formato
            $archivoPath = $this->generarArchivo($reporte, $datos);
            
            // Marcar como completado
            $reporte->marcarCompletado($archivoPath, count($datos));
            
            // Registrar evento en analytics
            AnalyticsEvento::registrarEvento([
                'empresa_id' => $reporte->empresa_id,
                'usuario_id' => $usuarioId,
                'evento' => AnalyticsEvento::EVENTO_REPORTE_GENERADO,
                'categoria' => AnalyticsEvento::CATEGORIA_SISTEMA,
                'entidad_tipo' => 'reporte',
                'entidad_id' => (string)$reporte->id,
                'datos_json' => [
                    'tipo' => $reporte->tipo,
                    'formato' => $formato,
                    'total_registros' => count($datos)
                ]
            ]);
            
        } catch (\Exception $e) {
            $reporte->marcarError($e->getMessage());
        }
        
        return $reporte;
    }

    /**
     * Generar datos según tipo de reporte
     */
    private function generarDatos(Reporte $reporte): array
    {
        $filtros = $reporte->filtros_json ?? [];
        
        switch ($reporte->tipo) {
            case Reporte::TIPO_VENTAS:
                return $this->generarDatosVentas($reporte->empresa_id, $filtros);
                
            case Reporte::TIPO_PRODUCTOS:
                return $this->generarDatosProductos($reporte->empresa_id, $filtros);
                
            case Reporte::TIPO_INVENTARIO:
                return $this->generarDatosInventario($reporte->empresa_id, $filtros);
                
            case Reporte::TIPO_CAJA:
                return $this->generarDatosCaja($reporte->empresa_id, $filtros);
                
            case Reporte::TIPO_COMPRAS:
                return $this->generarDatosCompras($reporte->empresa_id, $filtros);
                
            case Reporte::TIPO_LOTES:
                return $this->generarDatosLotes($reporte->empresa_id, $filtros);
                
            case Reporte::TIPO_ANALYTICS:
                return $this->generarDatosAnalytics($reporte->empresa_id, $filtros);
                
            default:
                throw new \Exception("Tipo de reporte no soportado: {$reporte->tipo}");
        }
    }

    /**
     * Generar datos de ventas
     */
    private function generarDatosVentas(int $empresaId, array $filtros): array
    {
        $query = Venta::where('empresa_id', $empresaId)
            ->with(['cliente', 'usuario', 'sucursal']);
        
        // Aplicar filtros
        if (isset($filtros['fecha_desde'])) {
            $query->whereDate('fecha', '>=', $filtros['fecha_desde']);
        }
        
        if (isset($filtros['fecha_hasta'])) {
            $query->whereDate('fecha', '<=', $filtros['fecha_hasta']);
        }
        
        if (isset($filtros['sucursal_id'])) {
            $query->where('sucursal_id', $filtros['sucursal_id']);
        }
        
        if (isset($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }
        
        return $query->orderBy('fecha', 'desc')->get()->map(function($venta) {
            return [
                'id' => $venta->id,
                'numero' => $venta->numero_venta,
                'fecha' => $venta->fecha->format('d/m/Y H:i'),
                'cliente' => $venta->cliente ? $venta->cliente->nombre : 'Cliente general',
                'sucursal' => $venta->sucursal->nombre,
                'usuario' => $venta->usuario->name,
                'subtotal' => $venta->subtotal,
                'igv' => $venta->igv,
                'total' => $venta->total,
                'estado' => $venta->estado
            ];
        })->toArray();
    }

    /**
     * Generar datos de productos
     */
    private function generarDatosProductos(int $empresaId, array $filtros): array
    {
        $query = Producto::where('empresa_id', $empresaId)
            ->with(['categoria', 'stocks']);
        
        // Aplicar filtros
        if (isset($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }
        
        if (isset($filtros['activo'])) {
            $query->where('activo', $filtros['activo']);
        }
        
        if (isset($filtros['con_stock'])) {
            $query->whereHas('stocks', function($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        }
        
        return $query->orderBy('nombre')->get()->map(function($producto) {
            $stockTotal = $producto->stocks->sum('cantidad_actual');
            
            return [
                'id' => $producto->id,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'categoria' => $producto->categoria ? $producto->categoria->nombre : 'Sin categoría',
                'precio_costo' => $producto->precio_costo,
                'precio_venta' => $producto->precio_venta,
                'stock_total' => $stockTotal,
                'stock_minimo' => $producto->stock_minimo,
                'estado_stock' => $stockTotal <= $producto->stock_minimo ? 'Bajo' : 'Normal',
                'activo' => $producto->activo ? 'Sí' : 'No'
            ];
        })->toArray();
    }

    /**
     * Generar datos de inventario
     */
    private function generarDatosInventario(int $empresaId, array $filtros): array
    {
        $query = ProductoStock::where('empresa_id', $empresaId)
            ->with(['producto', 'sucursal']);
        
        // Aplicar filtros
        if (isset($filtros['sucursal_id'])) {
            $query->where('sucursal_id', $filtros['sucursal_id']);
        }
        
        if (isset($filtros['solo_stock_bajo'])) {
            $query->whereColumn('cantidad_actual', '<=', 'stock_minimo');
        }
        
        return $query->orderBy('cantidad_actual')->get()->map(function($stock) {
            return [
                'producto_codigo' => $stock->producto->codigo,
                'producto_nombre' => $stock->producto->nombre,
                'sucursal' => $stock->sucursal->nombre,
                'stock_actual' => $stock->cantidad_actual,
                'stock_minimo' => $stock->stock_minimo,
                'stock_maximo' => $stock->stock_maximo,
                'diferencia' => $stock->cantidad_actual - $stock->stock_minimo,
                'estado' => $stock->cantidad_actual <= $stock->stock_minimo ? 'CRÍTICO' : 'NORMAL'
            ];
        })->toArray();
    }

    /**
     * Generar datos de caja
     */
    private function generarDatosCaja(int $empresaId, array $filtros): array
    {
        $query = CajaSesion::where('empresa_id', $empresaId)
            ->with(['caja', 'usuario', 'sucursal']);
        
        // Aplicar filtros
        if (isset($filtros['fecha_desde'])) {
            $query->whereDate('fecha_apertura', '>=', $filtros['fecha_desde']);
        }
        
        if (isset($filtros['fecha_hasta'])) {
            $query->whereDate('fecha_apertura', '<=', $filtros['fecha_hasta']);
        }
        
        if (isset($filtros['sucursal_id'])) {
            $query->where('sucursal_id', $filtros['sucursal_id']);
        }
        
        return $query->orderBy('fecha_apertura', 'desc')->get()->map(function($sesion) {
            return [
                'id' => $sesion->id,
                'caja' => $sesion->caja->nombre,
                'sucursal' => $sesion->sucursal->nombre,
                'usuario' => $sesion->usuario->name,
                'fecha_apertura' => $sesion->fecha_apertura->format('d/m/Y H:i'),
                'fecha_cierre' => $sesion->fecha_cierre ? $sesion->fecha_cierre->format('d/m/Y H:i') : 'Abierta',
                'monto_apertura' => $sesion->monto_apertura,
                'monto_cierre' => $sesion->monto_cierre,
                'diferencia' => $sesion->monto_cierre ? ($sesion->monto_cierre - $sesion->monto_apertura) : 0,
                'estado' => $sesion->estado
            ];
        })->toArray();
    }

    /**
     * Generar datos de compras
     */
    private function generarDatosCompras(int $empresaId, array $filtros): array
    {
        $query = Compra::where('empresa_id', $empresaId)
            ->with(['proveedor', 'usuario', 'sucursal']);
        
        // Aplicar filtros
        if (isset($filtros['fecha_desde'])) {
            $query->whereDate('fecha', '>=', $filtros['fecha_desde']);
        }
        
        if (isset($filtros['fecha_hasta'])) {
            $query->whereDate('fecha', '<=', $filtros['fecha_hasta']);
        }
        
        if (isset($filtros['proveedor_id'])) {
            $query->where('proveedor_id', $filtros['proveedor_id']);
        }
        
        return $query->orderBy('fecha', 'desc')->get()->map(function($compra) {
            return [
                'id' => $compra->id,
                'numero' => $compra->numero_compra,
                'fecha' => $compra->fecha->format('d/m/Y'),
                'proveedor' => $compra->proveedor->nombre,
                'sucursal' => $compra->sucursal->nombre,
                'usuario' => $compra->usuario->name,
                'subtotal' => $compra->subtotal,
                'igv' => $compra->igv,
                'total' => $compra->total,
                'estado' => $compra->estado
            ];
        })->toArray();
    }

    /**
     * Generar datos de lotes
     */
    private function generarDatosLotes(int $empresaId, array $filtros): array
    {
        $query = Lote::where('empresa_id', $empresaId)
            ->with(['producto', 'proveedor']);
        
        // Aplicar filtros
        if (isset($filtros['estado'])) {
            $query->where('estado_lote', $filtros['estado']);
        }
        
        if (isset($filtros['vencimiento_desde'])) {
            $query->whereDate('fecha_vencimiento', '>=', $filtros['vencimiento_desde']);
        }
        
        if (isset($filtros['vencimiento_hasta'])) {
            $query->whereDate('fecha_vencimiento', '<=', $filtros['vencimiento_hasta']);
        }
        
        if (isset($filtros['proximos_vencer'])) {
            $query->proximosAVencer($filtros['proximos_vencer']);
        }
        
        return $query->orderBy('fecha_vencimiento')->get()->map(function($lote) {
            $stockActual = $lote->getStockActual();
            
            return [
                'id' => $lote->id,
                'codigo_lote' => $lote->codigo_lote,
                'producto' => $lote->producto->nombre,
                'proveedor' => $lote->proveedor ? $lote->proveedor->nombre : 'Sin proveedor',
                'fecha_vencimiento' => $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m/Y') : 'Sin vencimiento',
                'dias_restantes' => $lote->diasHastaVencimiento(),
                'stock_actual' => $stockActual,
                'estado' => $lote->estado_lote,
                'criticidad' => $lote->estaVencido() ? 'VENCIDO' : ($lote->estaProximoAVencer(7) ? 'CRÍTICO' : 'NORMAL')
            ];
        })->toArray();
    }

    /**
     * Generar datos de analytics
     */
    private function generarDatosAnalytics(int $empresaId, array $filtros): array
    {
        $desde = isset($filtros['fecha_desde']) ? Carbon::parse($filtros['fecha_desde']) : Carbon::now()->subDays(30);
        $hasta = isset($filtros['fecha_hasta']) ? Carbon::parse($filtros['fecha_hasta']) : Carbon::now();
        
        return [
            'estadisticas_ventas' => AnalyticsEvento::getEstadisticasVentas($empresaId, $desde, $hasta),
            'productos_agotados' => AnalyticsEvento::getProductosMasAgotados($empresaId, 30),
            'resumen_periodo' => [
                'fecha_desde' => $desde->format('d/m/Y'),
                'fecha_hasta' => $hasta->format('d/m/Y'),
                'dias_analizados' => $desde->diffInDays($hasta) + 1
            ]
        ];
    }

    /**
     * Generar archivo según formato
     */
    private function generarArchivo(Reporte $reporte, array $datos): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "reporte_{$reporte->id}_{$timestamp}";
        
        switch ($reporte->formato) {
            case Reporte::FORMATO_PDF:
                return $this->generarPDF($reporte, $datos, $filename);
                
            case Reporte::FORMATO_EXCEL:
                return $this->generarExcel($reporte, $datos, $filename);
                
            case Reporte::FORMATO_CSV:
                return $this->generarCSV($reporte, $datos, $filename);
                
            default: // HTML
                return $this->generarHTML($reporte, $datos, $filename);
        }
    }

    /**
     * Generar archivo HTML
     */
    private function generarHTML(Reporte $reporte, array $datos, string $filename): string
    {
        $html = view('reportes.template', [
            'reporte' => $reporte,
            'datos' => $datos
        ])->render();
        
        $path = "reportes/{$filename}.html";
        Storage::put($path, $html);
        
        return $path;
    }

    /**
     * Generar archivo PDF
     */
    private function generarPDF(Reporte $reporte, array $datos, string $filename): string
    {
        // Generar PDF usando barryvdh/laravel-dompdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.pdf-template', [
            'reporte' => $reporte,
            'datos' => $datos,
            'titulo' => $reporte->nombre,
            'generado_en' => now()->format('d/m/Y H:i:s')
        ]);
        
        $path = "reportes/{$filename}.pdf";
        
        // Configurar orientación según datos
        if (count($datos) > 0 && is_array($datos[0]) && count($datos[0]) > 5) {
            $pdf->setPaper('A4', 'landscape');
        } else {
            $pdf->setPaper('A4', 'portrait');
        }
        
        // Guardar el PDF
        Storage::put($path, $pdf->output());
        
        return $path;
    }

    /**
     * Generar archivo Excel
     */
    private function generarExcel(Reporte $reporte, array $datos, string $filename): string
    {
        // Usar nuestra clase ReporteExport personalizada
        $exporter = new \App\Exports\ReporteExport($reporte, $datos);
        $csvContent = $exporter->generarCSV();
        
        $path = "reportes/{$filename}.csv";
        
        // Agregar BOM para compatibilidad con Excel y acentos
        $csvWithBom = "\xEF\xBB\xBF" . $csvContent;
        
        Storage::put($path, $csvWithBom);
        
        return $path;
    }

    /**
     * Generar archivo CSV
     */
    private function generarCSV(Reporte $reporte, array $datos, string $filename): string
    {
        $path = "reportes/{$filename}.csv";
        
        $csv = fopen('php://temp', 'w+');
        
        // Escribir headers
        if (!empty($datos)) {
            fputcsv($csv, array_keys($datos[0]));
            
            // Escribir datos
            foreach ($datos as $fila) {
                fputcsv($csv, $fila);
            }
        }
        
        rewind($csv);
        $contenido = stream_get_contents($csv);
        fclose($csv);
        
        Storage::put($path, $contenido);
        
        return $path;
    }
}

