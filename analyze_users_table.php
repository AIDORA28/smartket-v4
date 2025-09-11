<?php

use Illuminate\Support\Facades\DB;

// Obtener estructura de la tabla users
$columns = DB::select("
    SELECT column_name, data_type, is_nullable, column_default 
    FROM information_schema.columns 
    WHERE table_name = 'users' 
    ORDER BY ordinal_position
");

echo "=== ESTRUCTURA TABLA USERS ===\n";
foreach ($columns as $column) {
    printf("%-25s %-15s %-10s %s\n", 
        $column->column_name, 
        $column->data_type, 
        $column->is_nullable, 
        $column->column_default ?? 'NULL'
    );
}

// Verificar foreign keys
$foreignKeys = DB::select("
    SELECT
        tc.constraint_name,
        tc.table_name,
        kcu.column_name,
        ccu.table_name AS foreign_table_name,
        ccu.column_name AS foreign_column_name
    FROM
        information_schema.table_constraints AS tc
        JOIN information_schema.key_column_usage AS kcu
          ON tc.constraint_name = kcu.constraint_name
          AND tc.table_schema = kcu.table_schema
        JOIN information_schema.constraint_column_usage AS ccu
          ON ccu.constraint_name = tc.constraint_name
          AND ccu.table_schema = tc.table_schema
    WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_name = 'users'
");

echo "\n=== FOREIGN KEYS EN USERS ===\n";
foreach ($foreignKeys as $fk) {
    printf("%-20s -> %s.%s\n", 
        $fk->column_name, 
        $fk->foreign_table_name, 
        $fk->foreign_column_name
    );
}

// Verificar índices
$indexes = DB::select("
    SELECT indexname, indexdef 
    FROM pg_indexes 
    WHERE tablename = 'users'
");

echo "\n=== ÍNDICES EN USERS ===\n";
foreach ($indexes as $index) {
    echo "{$index->indexname}: {$index->indexdef}\n";
}
