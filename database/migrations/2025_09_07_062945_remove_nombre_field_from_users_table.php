<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar campo redundante 'nombre'
            // Laravel usa 'name' como estándar
            $table->dropColumn('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurar campo 'nombre' si es necesario hacer rollback
            // Nullable inicialmente para evitar constraint violation
            $table->string('nombre', 120)->nullable()->after('email')->comment('Nombre completo del usuario');
        });
        
        // Actualizar registros existentes copiando desde 'name'
        DB::statement("UPDATE users SET nombre = name WHERE nombre IS NULL");
        
        // Hacer el campo NOT NULL
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombre', 120)->nullable(false)->change();
        });
    }
};
