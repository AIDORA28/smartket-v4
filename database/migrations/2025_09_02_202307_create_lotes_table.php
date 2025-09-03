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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->comment('Proveedor del lote');
            $table->string('codigo_lote', 40)->comment('Código del lote, ej: YYYYMMDD-001');
            $table->date('fecha_produccion')->nullable()->comment('Fecha de producción');
            $table->date('fecha_vencimiento')->nullable()->comment('NULL = sin vencimiento');
            $table->string('estado_lote', 20)->default('activo')->comment('activo/vencido/bloqueado/agotado');
            $table->json('atributos_json')->nullable()->comment('Atributos específicos del lote');
            $table->timestamps();
            
            // Índices y restricciones
            $table->unique(['empresa_id', 'producto_id', 'codigo_lote'], 'idx_unique_lote_empresa_producto');
            $table->index(['empresa_id', 'fecha_vencimiento'], 'idx_lotes_empresa_vencimiento');
            $table->index(['producto_id'], 'idx_lotes_producto');
            $table->index(['estado_lote'], 'idx_lotes_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
