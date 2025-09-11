<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportService
{
    /**
     * Exportar datos a CSV
     */
    public function exportarCSV(Collection $datos, array $headers, string $nombreArchivo = null): string
    {
        $nombreArchivo = $nombreArchivo ?? 'export_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        $contenido = $this->generarCSV($datos, $headers);
        $rutaArchivo = 'exports/' . $nombreArchivo;
        
        Storage::put($rutaArchivo, $contenido);
        
        return $rutaArchivo;
    }

    /**
     * Exportar datos a JSON
     */
    public function exportarJSON(Collection $datos, string $nombreArchivo = null): string
    {
        $nombreArchivo = $nombreArchivo ?? 'export_' . Carbon::now()->format('Y-m-d_H-i-s') . '.json';
        
        $contenido = json_encode($datos->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $rutaArchivo = 'exports/' . $nombreArchivo;
        
        Storage::put($rutaArchivo, $contenido);
        
        return $rutaArchivo;
    }

    /**
     * Exportar datos a Excel (básico usando CSV)
     */
    public function exportarExcel(Collection $datos, array $headers, string $nombreArchivo = null): string
    {
        $nombreArchivo = $nombreArchivo ?? 'export_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Por ahora usamos CSV hasta que se instale una librería de Excel
        $contenido = $this->generarCSV($datos, $headers);
        $rutaArchivo = 'exports/' . str_replace('.xlsx', '.csv', $nombreArchivo);
        
        Storage::put($rutaArchivo, $contenido);
        
        return $rutaArchivo;
    }

    /**
     * Exportar reporte de ventas
     */
    public function exportarReporteVentas(Collection $ventas, string $formato = 'csv'): string
    {
        $headers = [
            'Fecha',
            'Cliente',
            'Número',
            'Subtotal',
            'IGV',
            'Total',
            'Estado',
            'Usuario',
            'Sucursal'
        ];

        $datos = $ventas->map(function ($venta) {
            return [
                $venta->fecha,
                $venta->cliente?->nombre ?? 'Sin cliente',
                $venta->numero,
                $venta->subtotal,
                $venta->igv,
                $venta->total,
                $venta->estado,
                $venta->user?->name ?? '',
                $venta->sucursal?->nombre ?? ''
            ];
        });

        $nombreArchivo = 'reporte_ventas_' . Carbon::now()->format('Y-m-d_H-i-s');

        switch ($formato) {
            case 'json':
                return $this->exportarJSON($datos, $nombreArchivo . '.json');
            case 'excel':
                return $this->exportarExcel($datos, $headers, $nombreArchivo . '.xlsx');
            default:
                return $this->exportarCSV($datos, $headers, $nombreArchivo . '.csv');
        }
    }

    /**
     * Exportar reporte de productos
     */
    public function exportarReporteProductos(Collection $productos, string $formato = 'csv'): string
    {
        $headers = [
            'Código',
            'Nombre',
            'Categoría',
            'Precio Venta',
            'Costo Unitario',
            'Stock Actual',
            'Stock Mínimo',
            'Estado',
            'Fecha Creación'
        ];

        $datos = $productos->map(function ($producto) {
            return [
                $producto->codigo,
                $producto->nombre,
                $producto->categoria?->nombre ?? '',
                $producto->precio_venta,
                $producto->costo_unitario,
                $producto->stocks->sum('stock_actual') ?? 0,
                $producto->stock_minimo,
                $producto->activo ? 'Activo' : 'Inactivo',
                $producto->created_at?->format('Y-m-d H:i:s')
            ];
        });

        $nombreArchivo = 'reporte_productos_' . Carbon::now()->format('Y-m-d_H-i-s');

        switch ($formato) {
            case 'json':
                return $this->exportarJSON($datos, $nombreArchivo . '.json');
            case 'excel':
                return $this->exportarExcel($datos, $headers, $nombreArchivo . '.xlsx');
            default:
                return $this->exportarCSV($datos, $headers, $nombreArchivo . '.csv');
        }
    }

    /**
     * Exportar reporte de inventario
     */
    public function exportarReporteInventario(Collection $stocks, string $formato = 'csv'): string
    {
        $headers = [
            'Producto',
            'Código',
            'Sucursal',
            'Stock Actual',
            'Stock Mínimo',
            'Valor Unitario',
            'Valor Total',
            'Estado Stock'
        ];

        $datos = $stocks->map(function ($stock) {
            $estadoStock = 'Normal';
            if ($stock->stock_actual == 0) {
                $estadoStock = 'Sin Stock';
            } elseif ($stock->stock_actual <= $stock->producto->stock_minimo) {
                $estadoStock = 'Stock Bajo';
            }

            return [
                $stock->producto->nombre,
                $stock->producto->codigo,
                $stock->sucursal?->nombre ?? '',
                $stock->stock_actual,
                $stock->producto->stock_minimo,
                $stock->producto->costo_unitario,
                $stock->stock_actual * $stock->producto->costo_unitario,
                $estadoStock
            ];
        });

        $nombreArchivo = 'reporte_inventario_' . Carbon::now()->format('Y-m-d_H-i-s');

        switch ($formato) {
            case 'json':
                return $this->exportarJSON($datos, $nombreArchivo . '.json');
            case 'excel':
                return $this->exportarExcel($datos, $headers, $nombreArchivo . '.xlsx');
            default:
                return $this->exportarCSV($datos, $headers, $nombreArchivo . '.csv');
        }
    }

    /**
     * Exportar reporte de caja
     */
    public function exportarReporteCaja(Collection $sesiones, string $formato = 'csv'): string
    {
        $headers = [
            'Fecha Apertura',
            'Fecha Cierre',
            'Usuario',
            'Sucursal',
            'Monto Inicial',
            'Monto Cierre',
            'Diferencia',
            'Estado'
        ];

        $datos = $sesiones->map(function ($sesion) {
            return [
                $sesion->fecha_apertura,
                $sesion->fecha_cierre ?? 'Abierta',
                $sesion->user?->name ?? '',
                $sesion->sucursal?->nombre ?? '',
                $sesion->monto_inicial,
                $sesion->monto_cierre ?? 0,
                ($sesion->monto_cierre ?? 0) - $sesion->monto_inicial,
                $sesion->fecha_cierre ? 'Cerrada' : 'Abierta'
            ];
        });

        $nombreArchivo = 'reporte_caja_' . Carbon::now()->format('Y-m-d_H-i-s');

        switch ($formato) {
            case 'json':
                return $this->exportarJSON($datos, $nombreArchivo . '.json');
            case 'excel':
                return $this->exportarExcel($datos, $headers, $nombreArchivo . '.xlsx');
            default:
                return $this->exportarCSV($datos, $headers, $nombreArchivo . '.csv');
        }
    }

    /**
     * Exportar reporte de compras
     */
    public function exportarReporteCompras(Collection $compras, string $formato = 'csv'): string
    {
        $headers = [
            'Fecha',
            'Proveedor',
            'Número',
            'Subtotal',
            'IGV',
            'Total',
            'Estado',
            'Usuario',
            'Sucursal'
        ];

        $datos = $compras->map(function ($compra) {
            return [
                $compra->fecha,
                $compra->proveedor?->nombre ?? '',
                $compra->numero,
                $compra->subtotal,
                $compra->igv,
                $compra->total,
                $compra->estado,
                $compra->user?->name ?? '',
                $compra->sucursal?->nombre ?? ''
            ];
        });

        $nombreArchivo = 'reporte_compras_' . Carbon::now()->format('Y-m-d_H-i-s');

        switch ($formato) {
            case 'json':
                return $this->exportarJSON($datos, $nombreArchivo . '.json');
            case 'excel':
                return $this->exportarExcel($datos, $headers, $nombreArchivo . '.xlsx');
            default:
                return $this->exportarCSV($datos, $headers, $nombreArchivo . '.csv');
        }
    }

    /**
     * Exportar reporte de lotes
     */
    public function exportarReporteLotes(Collection $lotes, string $formato = 'csv'): string
    {
        $headers = [
            'Producto',
            'Código Lote',
            'Fecha Producción',
            'Fecha Vencimiento',
            'Cantidad Inicial',
            'Cantidad Disponible',
            'Sucursal',
            'Estado'
        ];

        $datos = $lotes->map(function ($lote) {
            $estado = 'Activo';
            if ($lote->fecha_vencimiento < Carbon::now()) {
                $estado = 'Vencido';
            } elseif ($lote->fecha_vencimiento <= Carbon::now()->addDays(7)) {
                $estado = 'Por Vencer';
            } elseif ($lote->cantidad_disponible == 0) {
                $estado = 'Agotado';
            }

            return [
                $lote->producto?->nombre ?? '',
                $lote->codigo_lote,
                $lote->fecha_produccion,
                $lote->fecha_vencimiento,
                $lote->cantidad_inicial,
                $lote->cantidad_disponible,
                $lote->sucursal?->nombre ?? '',
                $estado
            ];
        });

        $nombreArchivo = 'reporte_lotes_' . Carbon::now()->format('Y-m-d_H-i-s');

        switch ($formato) {
            case 'json':
                return $this->exportarJSON($datos, $nombreArchivo . '.json');
            case 'excel':
                return $this->exportarExcel($datos, $headers, $nombreArchivo . '.xlsx');
            default:
                return $this->exportarCSV($datos, $headers, $nombreArchivo . '.csv');
        }
    }

    /**
     * Exportar reporte de analytics
     */
    public function exportarReporteAnalytics(Collection $eventos, string $formato = 'csv'): string
    {
        $headers = [
            'Fecha/Hora',
            'Tipo Evento',
            'Objeto',
            'ID Objeto',
            'Usuario',
            'Sucursal',
            'IP Address',
            'User Agent',
            'Datos'
        ];

        $datos = $eventos->map(function ($evento) {
            return [
                $evento->created_at?->format('Y-m-d H:i:s'),
                $evento->evento_tipo,
                $evento->objeto,
                $evento->objeto_id,
                $evento->user?->name ?? '',
                $evento->sucursal?->nombre ?? '',
                $evento->ip_address,
                $evento->user_agent,
                $evento->datos
            ];
        });

        $nombreArchivo = 'reporte_analytics_' . Carbon::now()->format('Y-m-d_H-i-s');

        switch ($formato) {
            case 'json':
                return $this->exportarJSON($datos, $nombreArchivo . '.json');
            case 'excel':
                return $this->exportarExcel($datos, $headers, $nombreArchivo . '.xlsx');
            default:
                return $this->exportarCSV($datos, $headers, $nombreArchivo . '.csv');
        }
    }

    /**
     * Generar contenido CSV
     */
    private function generarCSV(Collection $datos, array $headers): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Escribir headers
        fputcsv($output, $headers);
        
        // Escribir datos
        foreach ($datos as $fila) {
            fputcsv($output, is_array($fila) ? $fila : $fila->toArray());
        }
        
        rewind($output);
        $contenido = stream_get_contents($output);
        fclose($output);
        
        return $contenido;
    }

    /**
     * Obtener URL de descarga de archivo exportado
     */
    public function obtenerUrlDescarga(string $rutaArchivo): string
    {
        return Storage::url($rutaArchivo);
    }

    /**
     * Eliminar archivo exportado
     */
    public function eliminarArchivo(string $rutaArchivo): bool
    {
        return Storage::delete($rutaArchivo);
    }

    /**
     * Listar archivos exportados
     */
    public function listarArchivosExportados(): array
    {
        $archivos = Storage::files('exports');
        
        return collect($archivos)->map(function ($archivo) {
            return [
                'nombre' => basename($archivo),
                'ruta' => $archivo,
                'tamaño' => Storage::size($archivo),
                'fecha_modificacion' => Storage::lastModified($archivo),
                'url_descarga' => $this->obtenerUrlDescarga($archivo)
            ];
        })->sortByDesc('fecha_modificacion')->values()->toArray();
    }

    /**
     * Limpiar archivos exportados antiguos (más de 7 días)
     */
    public function limpiarArchivosAntiguos(): int
    {
        $archivos = Storage::files('exports');
        $eliminados = 0;
        $fechaLimite = Carbon::now()->subDays(7)->timestamp;
        
        foreach ($archivos as $archivo) {
            if (Storage::lastModified($archivo) < $fechaLimite) {
                Storage::delete($archivo);
                $eliminados++;
            }
        }
        
        return $eliminados;
    }

    /**
     * Generar reporte personalizado
     */
    public function generarReportePersonalizado(
        Collection $datos,
        array $columnas,
        string $titulo,
        string $formato = 'csv'
    ): string {
        $headers = array_values($columnas);
        $datosFormateados = $datos->map(function ($item) use ($columnas) {
            $fila = [];
            foreach (array_keys($columnas) as $campo) {
                $fila[] = data_get($item, $campo, '');
            }
            return $fila;
        });

        $nombreArchivo = 'reporte_personalizado_' . Carbon::now()->format('Y-m-d_H-i-s');

        switch ($formato) {
            case 'json':
                return $this->exportarJSON($datosFormateados, $nombreArchivo . '.json');
            case 'excel':
                return $this->exportarExcel($datosFormateados, $headers, $nombreArchivo . '.xlsx');
            default:
                return $this->exportarCSV($datosFormateados, $headers, $nombreArchivo . '.csv');
        }
    }
}

