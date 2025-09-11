<?php

use Illuminate\Support\Facades\DB;

// Obtener estructura de la tabla planes
$columns = DB::select("
    SELECT column_name, data_type, is_nullable, column_default 
    FROM information_schema.columns 
    WHERE table_name = 'planes' 
    ORDER BY ordinal_position
");

echo "=== ESTRUCTURA TABLA PLANES ===\n";
foreach ($columns as $column) {
    printf("%-25s %-20s %-10s %s\n", 
        $column->column_name, 
        $column->data_type, 
        $column->is_nullable, 
        $column->column_default ?? 'NULL'
    );
}

// Verificar tabla plan_addons
$planAddonsColumns = DB::select("
    SELECT column_name, data_type, is_nullable, column_default 
    FROM information_schema.columns 
    WHERE table_name = 'plan_addons' 
    ORDER BY ordinal_position
");

echo "\n=== ESTRUCTURA TABLA PLAN_ADDONS ===\n";
foreach ($planAddonsColumns as $column) {
    printf("%-25s %-20s %-10s %s\n", 
        $column->column_name, 
        $column->data_type, 
        $column->is_nullable, 
        $column->column_default ?? 'NULL'
    );
}
