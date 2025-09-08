<?php
/**
 * Script de análisis completo de la base de datos
 * Uso: php scripts/database/analyze_all_tables.php
 */

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

function showTableStructure($tableName)
{
    echo "\n=== TABLA: {$tableName} ===\n";
    
    try {
        // Verificar si la tabla existe
        if (!Schema::hasTable($tableName)) {
            echo "❌ La tabla '{$tableName}' no existe\n";
            return;
        }
        
        // Obtener columnas
        $columns = Schema::getColumnListing($tableName);
        echo "📋 Columnas (" . count($columns) . "):\n";
        foreach ($columns as $column) {
            echo "  - {$column}\n";
        }
        
        // Contar registros
        $count = DB::table($tableName)->count();
        echo "\n📊 Total registros: {$count}\n";
        
        // Mostrar algunos datos de ejemplo si hay registros
        if ($count > 0 && $count <= 5) {
            echo "\n🔍 Datos de ejemplo:\n";
            $samples = DB::table($tableName)->limit(3)->get();
            foreach ($samples as $sample) {
                echo "  • " . json_encode($sample, JSON_UNESCAPED_UNICODE) . "\n";
            }
        } elseif ($count > 5) {
            echo "\n🔍 Primeros 2 registros de ejemplo:\n";
            $samples = DB::table($tableName)->limit(2)->get();
            foreach ($samples as $sample) {
                echo "  • " . json_encode($sample, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

// Main execution
echo "=== ANÁLISIS COMPLETO BASE DE DATOS - SMARTKET V4 ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";

// Lista completa de tablas importantes
$tables = [
    // Core
    'users',
    'empresas', 
    'planes',
    'sucursales',
    
    // Productos e Inventario
    'productos',
    'categorias', 
    'producto_stocks',
    'inventario_movimientos',
    'lotes',
    
    // Ventas
    'ventas',
    'venta_detalles',
    'venta_pagos',
    'metodos_pago',
    
    // Clientes y Proveedores
    'clientes',
    'proveedores',
    
    // Compras
    'compras',
    'compra_items',
    
    // Caja
    'cajas',
    'caja_sesiones',
    'caja_movimientos',
    
    // Rubros
    'rubros',
    'empresa_rubros',
    
    // Features y Analytics
    'feature_flags',
    'analytics_eventos'
];

foreach ($tables as $table) {
    showTableStructure($table);
}

echo "\n✅ Análisis completo de base de datos finalizado.\n";
