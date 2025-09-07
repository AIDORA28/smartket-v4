<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require_once __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DETALLADA DE LA TABLA USERS ===\n\n";

// Verificar si la tabla existe
if (Schema::hasTable('users')) {
    echo "✅ La tabla 'users' SÍ EXISTE\n\n";
    
    // Obtener todas las columnas de la tabla users
    echo "📋 COLUMNAS DE LA TABLA USERS:\n";
    echo str_repeat('=', 50) . "\n";
    
    $columns = Schema::getColumnListing('users');
    foreach ($columns as $index => $column) {
        echo ($index + 1) . ". $column\n";
    }
    
    echo "\n📊 INFORMACIÓN DETALLADA DE CADA COLUMNA:\n";
    echo str_repeat('=', 50) . "\n";
    
    // Usar consulta SQL directa para obtener información detallada
    $columnInfo = DB::select("
        SELECT 
            column_name,
            data_type,
            character_maximum_length,
            is_nullable,
            column_default
        FROM information_schema.columns 
        WHERE table_name = 'users' 
        ORDER BY ordinal_position
    ");
    
    foreach ($columnInfo as $column) {
        $name = $column->column_name;
        $type = $column->data_type;
        $length = $column->character_maximum_length;
        $nullable = $column->is_nullable === 'YES' ? 'NULLABLE' : 'NOT NULL';
        $default = $column->column_default;
        
        echo "- $name: $type" . ($length ? "($length)" : '') . " | $nullable" . ($default ? " | DEFAULT: $default" : '') . "\n";
    }
    
    // Contar registros
    $userCount = DB::table('users')->count();
    echo "\n👥 TOTAL DE USUARIOS: $userCount\n\n";
    
    if ($userCount > 0) {
        echo "📝 DATOS DE USUARIOS:\n";
        echo str_repeat('-', 50) . "\n";
        $users = DB::table('users')->select('id', 'name', 'email', 'created_at')->get();
        foreach ($users as $user) {
            echo "ID: $user->id | Name: $user->name | Email: $user->email | Created: $user->created_at\n";
        }
    }
    
    // Verificar índices
    echo "\n🔑 ÍNDICES DE LA TABLA USERS:\n";
    echo str_repeat('=', 50) . "\n";
    $indexes = DB::select("
        SELECT 
            indexname,
            indexdef
        FROM pg_indexes 
        WHERE tablename = 'users'
    ");
    
    foreach ($indexes as $index) {
        echo "- {$index->indexname}: {$index->indexdef}\n";
    }
    
} else {
    echo "❌ La tabla 'users' NO EXISTE\n";
    
    // Listar todas las tablas disponibles
    echo "\n📋 TABLAS DISPONIBLES EN LA BASE DE DATOS:\n";
    $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    foreach ($tables as $table) {
        echo "- {$table->table_name}\n";
    }
}

echo "\n=== FIN DE LA VERIFICACIÓN ===\n";
