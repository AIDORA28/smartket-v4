<?php

namespace App\Exports;

use App\Models\Reporte;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * Export de reportes para Excel usando PhpSpreadsheet
 * Como no pudimos instalar maatwebsite/excel, usamos una implementación manual
 */
class ReporteExport
{
    protected Reporte $reporte;
    protected array $datos;

    public function __construct(Reporte $reporte, array $datos)
    {
        $this->reporte = $reporte;
        $this->datos = $datos;
    }

    /**
     * Generar CSV como alternativa a Excel
     */
    public function generarCSV(): string
    {
        $csv = [];
        
        // Headers basados en el reporte
        $headers = $this->extraerHeaders();
        $csv[] = $headers;
        
        // Datos
        foreach ($this->datos as $fila) {
            $csv[] = $this->extraerFila($fila, $headers);
        }
        
        return $this->arrayToCSV($csv);
    }

    /**
     * Extraer headers del primer elemento de datos
     */
    private function extraerHeaders(): array
    {
        if (empty($this->datos)) {
            return ['Sin datos'];
        }

        $primer_elemento = $this->datos[0];
        
        if (is_array($primer_elemento)) {
            return array_keys($primer_elemento);
        }
        
        if (is_object($primer_elemento)) {
            return array_keys((array) $primer_elemento);
        }
        
        return ['Valor'];
    }

    /**
     * Extraer fila de datos
     */
    private function extraerFila($elemento, array $headers): array
    {
        $fila = [];
        
        foreach ($headers as $header) {
            if (is_array($elemento)) {
                $fila[] = $elemento[$header] ?? '';
            } elseif (is_object($elemento)) {
                $fila[] = $elemento->$header ?? '';
            } else {
                $fila[] = (string) $elemento;
            }
        }
        
        return $fila;
    }

    /**
     * Convertir array a CSV string
     */
    private function arrayToCSV(array $data): string
    {
        $output = '';
        
        foreach ($data as $row) {
            $output .= implode(',', array_map(function($field) {
                // Escapar campos que contengan comas o comillas
                if (strpos($field, ',') !== false || strpos($field, '"') !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row)) . "\n";
        }
        
        return $output;
    }

    /**
     * Obtener metadatos del reporte
     */
    public function getMetadata(): array
    {
        return [
            'nombre' => $this->reporte->nombre,
            'descripcion' => $this->reporte->descripcion,
            'generado_en' => now(),
            'total_filas' => count($this->datos)
        ];
    }
}
