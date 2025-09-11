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
        Schema::create('analytics_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: eventos por empresa');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->comment('Usuario que trigger el evento');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->comment('Sucursal donde ocurre el evento');
            $table->string('evento', 100)->comment('Nombre del evento: venta_completada, producto_agotado, etc.');
            $table->string('categoria', 50)->comment('VENTAS/INVENTARIO/CAJA/USUARIOS/SISTEMA');
            $table->string('entidad_tipo', 50)->nullable()->comment('Tipo de entidad: venta, producto, usuario, etc.');
            $table->string('entidad_id', 50)->nullable()->comment('ID de la entidad relacionada');
            $table->json('datos_json')->nullable()->comment('Datos adicionales del evento');
            $table->json('metadatos_json')->nullable()->comment('Metadatos: IP, user agent, etc.');
            $table->decimal('valor_numerico', 15, 4)->nullable()->comment('Valor numérico del evento (monto, cantidad, etc.)');
            $table->string('valor_texto', 255)->nullable()->comment('Valor textual del evento');
            $table->timestamp('timestamp_evento')->comment('Momento exacto del evento');
            $table->string('session_id', 100)->nullable()->comment('ID de sesión');
            $table->timestamps();
            
            // Índices para queries de analytics
            $table->index(['empresa_id', 'evento', 'timestamp_evento'], 'idx_analytics_empresa_evento');
            $table->index(['categoria', 'timestamp_evento'], 'idx_analytics_categoria');
            $table->index(['sucursal_id', 'timestamp_evento'], 'idx_analytics_sucursal');
            $table->index(['entidad_tipo', 'entidad_id'], 'idx_analytics_entidad');
            $table->index(['timestamp_evento'], 'idx_analytics_tiempo');
            
            // Índice para limpieza de datos antiguos
            $table->index(['created_at'], 'idx_analytics_limpieza');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_eventos');
    }
};

