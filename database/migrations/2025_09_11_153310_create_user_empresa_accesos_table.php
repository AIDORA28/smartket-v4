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
        Schema::create('user_empresa_accesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('rol_en_empresa')->default('empleado')->comment('admin/gerente/empleado/vendedor');
            $table->boolean('activo')->default(true)->comment('Si el acceso está activo');
            $table->timestamps();
            
            // Índices
            $table->unique(['user_id', 'empresa_id'], 'unique_user_empresa');
            $table->index(['user_id'], 'idx_user_empresa_user');
            $table->index(['empresa_id'], 'idx_user_empresa_empresa');
            $table->index(['activo'], 'idx_user_empresa_activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_empresa_accesos');
    }
};
