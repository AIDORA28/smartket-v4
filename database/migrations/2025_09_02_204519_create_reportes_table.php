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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: reportes por empresa');
            $table->foreignId('usuario_id')->constrained('users')->comment('Usuario que genera el reporte');
            $table->string('nombre', 100)->comment('Nombre descriptivo del reporte');
            $table->string('tipo', 50)->comment('VENTAS/PRODUCTOS/INVENTARIO/CAJA/COMPRAS/LOTES/ANALYTICS');
            $table->string('subtipo', 50)->nullable()->comment('Subtipo específico del reporte');
            $table->json('filtros_json')->nullable()->comment('Filtros aplicados al reporte');
            $table->json('configuracion_json')->nullable()->comment('Configuración de columnas, orden, etc.');
            $table->string('formato', 20)->default('HTML')->comment('HTML/PDF/EXCEL/CSV');
            $table->string('estado', 20)->default('GENERANDO')->comment('GENERANDO/COMPLETADO/ERROR');
            $table->text('archivo_path')->nullable()->comment('Ruta del archivo generado');
            $table->integer('total_registros')->default(0)->comment('Total de registros en el reporte');
            $table->timestamp('fecha_generacion')->nullable()->comment('Timestamp de generación');
            $table->timestamp('fecha_expiracion')->nullable()->comment('Fecha de expiración del archivo');
            $table->text('error_mensaje')->nullable()->comment('Mensaje de error si falla');
            $table->boolean('es_favorito')->default(false)->comment('Marcado como favorito por el usuario');
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id', 'tipo', 'created_at'], 'idx_reportes_empresa_tipo');
            $table->index(['usuario_id', 'created_at'], 'idx_reportes_usuario');
            $table->index(['estado', 'fecha_expiracion'], 'idx_reportes_limpieza');
            $table->index(['es_favorito'], 'idx_reportes_favoritos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
