<?php
/**
 * Script de diagnóstico - Información de tablas
 * Uso: php scripts/database/table_info.php
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
        
        // Información detallada de PostgreSQL
        $columnInfo = DB::select("
            SELECT 
                column_name, 
                data_type, 
                is_nullable,
                column_default,
                character_maximum_length
            FROM information_schema.columns 
            WHERE table_name = ? 
            ORDER BY ordinal_position
        ", [$tableName]);
        
        echo "\n🔍 Detalles:\n";
        foreach ($columnInfo as $info) {
            $nullable = $info->is_nullable === 'YES' ? 'NULL' : 'NOT NULL';
            $length = $info->character_maximum_length ? "({$info->character_maximum_length})" : '';
            $default = $info->column_default ? " DEFAULT: {$info->column_default}" : '';
            echo "  • {$info->column_name} {$info->data_type}{$length} {$nullable}{$default}\n";
        }
        
        // Contar registros
        $count = DB::table($tableName)->count();
        echo "\n📊 Total registros: {$count}\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

// Main execution
echo "=== DIAGNÓSTICO DE TABLAS - SMARTKET V4 ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";

$tables = [
    'users',
    'empresas', 
    'planes',
    'plan_addons',
    'empresa_addons',
    'sucursales'
];

foreach ($tables as $table) {
    showTableStructure($table);
}

echo "\n✅ Diagnóstico completado.\n";
