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
        Schema::create('reporte_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->comment('NULL = template global, sino específico por empresa');
            $table->string('nombre', 100)->comment('Nombre de la plantilla');
            $table->text('descripcion')->nullable()->comment('Descripción del template');
            $table->string('tipo', 50)->comment('VENTAS/PRODUCTOS/INVENTARIO/CAJA/COMPRAS/LOTES/ANALYTICS');
            $table->string('subtipo', 50)->nullable()->comment('Subtipo específico');
            $table->json('configuracion_json')->comment('Configuración completa del reporte');
            $table->json('filtros_defecto_json')->nullable()->comment('Filtros por defecto');
            $table->json('columnas_json')->comment('Definición de columnas a mostrar');
            $table->string('formato_defecto', 20)->default('HTML')->comment('Formato por defecto');
            $table->boolean('es_publico')->default(false)->comment('Disponible para toda la empresa');
            $table->boolean('es_sistema')->default(false)->comment('Template del sistema (no editable)');
            $table->integer('orden_display')->default(0)->comment('Orden para mostrar en listados');
            $table->boolean('activo')->default(true)->comment('Template activo');
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id', 'tipo', 'activo'], 'idx_templates_empresa_tipo');
            $table->index(['es_publico', 'es_sistema'], 'idx_templates_acceso');
            $table->index(['orden_display'], 'idx_templates_orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_templates');
    }
};

