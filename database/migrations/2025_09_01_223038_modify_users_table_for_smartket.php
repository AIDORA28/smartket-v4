<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregar campos para SmartKet
            $table->foreignId('empresa_id')->after('id')->constrained('empresas');
            $table->foreignId('sucursal_id')->after('empresa_id')->nullable()->constrained('sucursales')->comment('Sucursal asignada para staff');
            $table->string('nombre', 120)->after('email')->comment('Nombre completo del usuario');
            $table->string('rol_principal', 30)->after('password')->default('staff')->comment('owner/admin/cajero/vendedor/almacenero');
            $table->tinyInteger('activo')->after('rol_principal')->default(1)->comment('Si el usuario está activo');
            $table->datetime('last_login_at')->after('activo')->nullable()->comment('Último login registrado');
            
            // Modificar campos existentes
            $table->string('email', 150)->change();
            $table->renameColumn('password', 'password_hash');
            
            // Agregar índices
            $table->unique(['empresa_id', 'email']);
            $table->index(['empresa_id', 'rol_principal']);
            $table->index('sucursal_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['sucursal_id']);
            $table->dropUnique(['empresa_id', 'email']);
            $table->dropIndex(['empresa_id', 'rol_principal']);
            $table->dropIndex(['sucursal_id']);
            
            $table->dropColumn([
                'empresa_id', 'sucursal_id', 'nombre', 'rol_principal', 
                'activo', 'last_login_at'
            ]);
            
            $table->renameColumn('password_hash', 'password');
            $table->string('email')->change();
        });
    }
};
