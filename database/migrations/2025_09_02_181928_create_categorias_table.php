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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: categorías por empresa');
            $table->string('nombre', 120)->comment('Nombre de la categoría');
            $table->string('codigo', 20)->nullable()->comment('Código interno opcional');
            $table->text('descripcion')->nullable()->comment('Descripción detallada');
            $table->string('color', 7)->default('#6B7280')->comment('Color hex para UI');
            $table->string('icono', 50)->default('📦')->comment('Emoji o clase de icono');
            $table->tinyInteger('activa')->default(1)->comment('Si la categoría está activa');
            $table->unsignedInteger('productos_count')->default(0)->comment('Cache: cantidad de productos');
            $table->timestamps();
            
            $table->unique(['empresa_id', 'nombre']);
            $table->unique(['empresa_id', 'codigo']);
            $table->index(['empresa_id', 'activa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};

