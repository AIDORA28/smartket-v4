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
        Schema::create('empresa_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')
                  ->comment('Relación con empresa');
            
            // Métricas financieras
            $table->decimal('total_ventas_mes', 12, 2)->default(0)
                  ->comment('Total ventas del mes actual');
            $table->decimal('total_ventas_año', 15, 2)->default(0)
                  ->comment('Total ventas del año actual');
            $table->decimal('margen_promedio', 5, 2)->default(0)
                  ->comment('Margen de ganancia promedio');
            $table->decimal('crecimiento_mensual', 5, 2)->default(0)
                  ->comment('Porcentaje crecimiento vs mes anterior');
            
            // Métricas operativas
            $table->unsignedInteger('total_clientes')->default(0)
                  ->comment('Total clientes registrados');
            $table->unsignedInteger('clientes_activos_mes')->default(0)
                  ->comment('Clientes que compraron este mes');
            $table->unsignedInteger('total_productos')->default(0)
                  ->comment('Total productos en inventario');
            $table->unsignedInteger('productos_vendidos_mes')->default(0)
                  ->comment('Productos vendidos este mes');
            
            // Métricas de sucursales
            $table->unsignedInteger('total_sucursales')->default(0)
                  ->comment('Total sucursales activas');
            $table->foreignId('sucursal_mas_vendedora_id')->nullable()
                  ->constrained('sucursales')->onDelete('set null')
                  ->comment('Sucursal con más ventas del mes');
            
            // Productos y categorías top
            $table->json('productos_mas_vendidos')->nullable()
                  ->comment('Top 10 productos más vendidos');
            $table->json('categorias_mas_vendidas')->nullable()
                  ->comment('Top 10 categorías más vendidas');
            
            // Métricas de satisfacción
            $table->decimal('rating_promedio', 3, 2)->nullable()
                  ->comment('Rating promedio de clientes');
            $table->unsignedInteger('total_reviews')->default(0)
                  ->comment('Total reviews recibidas');
            
            // Alertas y configuración
            $table->json('alertas_configuradas')->nullable()
                  ->comment('Alertas automáticas configuradas');
            $table->json('reportes_automaticos')->nullable()
                  ->comment('Configuración reportes automáticos');
            
            // Fecha de última actualización
            $table->timestamp('ultima_actualizacion')->nullable()
                  ->comment('Última vez que se calcularon las métricas');
            
            $table->timestamps();
            
            // Índices
            $table->unique('empresa_id', 'empresa_analytics_empresa_unique');
            $table->index('total_ventas_mes');
            $table->index('crecimiento_mensual');
            $table->index('ultima_actualizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_analytics');
    }
};
