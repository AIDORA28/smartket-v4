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
        Schema::table('productos', function (Blueprint $table) {
            // Agregar FK para marca (nullable)
            $table->foreignId('marca_id')->nullable()->after('categoria_id')->constrained('marcas')->comment('Marca del producto (opcional)');
            
            // Agregar FK para unidad de medida (nullable, mantenemos string por compatibilidad)
            $table->foreignId('unidad_medida_id')->nullable()->after('unidad_medida')->constrained('unidades_medida')->comment('Unidad de medida del producto (opcional)');
            
            // Índices para optimizar consultas
            $table->index(['empresa_id', 'marca_id']);
            $table->index(['empresa_id', 'unidad_medida_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['marca_id']);
            $table->dropForeign(['unidad_medida_id']);
            $table->dropIndex(['empresa_id', 'marca_id']);
            $table->dropIndex(['empresa_id', 'unidad_medida_id']);
            $table->dropColumn(['marca_id', 'unidad_medida_id']);
        });
    }
};
