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
        Schema::create('sucursal_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade')
                  ->comment('Relación con sucursal');
            
            // Métricas de ventas
            $table->decimal('ventas_dia', 12, 2)->default(0)
                  ->comment('Ventas del día actual');
            $table->decimal('ventas_mes', 12, 2)->default(0)
                  ->comment('Ventas del mes actual');
            $table->decimal('ventas_año', 15, 2)->default(0)
                  ->comment('Ventas del año actual');
            
            // Métricas de transacciones
            $table->unsignedInteger('transacciones_dia')->default(0)
                  ->comment('Número de transacciones del día');
            $table->unsignedInteger('transacciones_mes')->default(0)
                  ->comment('Número de transacciones del mes');
            $table->decimal('ticket_promedio', 10, 2)->default(0)
                  ->comment('Ticket promedio de venta');
            
            // Métricas de productos
            $table->unsignedInteger('productos_vendidos_dia')->default(0)
                  ->comment('Productos vendidos hoy');
            $table->unsignedInteger('productos_vendidos_mes')->default(0)
                  ->comment('Productos vendidos este mes');
            $table->json('productos_top_dia')->nullable()
                  ->comment('Top productos vendidos del día');
            
            // Métricas de clientes
            $table->unsignedInteger('clientes_atendidos_dia')->default(0)
                  ->comment('Clientes únicos atendidos hoy');
            $table->unsignedInteger('clientes_nuevos_mes')->default(0)
                  ->comment('Clientes nuevos este mes');
            
            // Métricas de inventario
            $table->unsignedInteger('productos_bajo_stock')->default(0)
                  ->comment('Productos con stock bajo');
            $table->unsignedInteger('productos_sin_stock')->default(0)
                  ->comment('Productos sin stock');
            $table->decimal('valor_inventario', 15, 2)->default(0)
                  ->comment('Valor total del inventario');
            
            // Métricas de personal
            $table->unsignedInteger('empleados_activos')->default(0)
                  ->comment('Empleados activos en sucursal');
            $table->decimal('ventas_por_empleado', 10, 2)->default(0)
                  ->comment('Ventas promedio por empleado');
            
            // Comparativas y rankings
            $table->unsignedInteger('ranking_empresa')->nullable()
                  ->comment('Posición en ranking de sucursales');
            $table->decimal('crecimiento_vs_mes_anterior', 5, 2)->default(0)
                  ->comment('% crecimiento vs mes anterior');
            $table->decimal('participacion_ventas_empresa', 5, 2)->default(0)
                  ->comment('% participación en ventas totales');
            
            // Fecha de última actualización
            $table->date('fecha_metricas')
                  ->comment('Fecha de las métricas registradas');
            $table->timestamp('ultima_actualizacion')->nullable()
                  ->comment('Última actualización de métricas');
            
            $table->timestamps();
            
            // Índices
            $table->unique(['sucursal_id', 'fecha_metricas'], 'sucursal_performance_unique');
            $table->index('ventas_mes');
            $table->index('ranking_empresa');
            $table->index('fecha_metricas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursal_performance');
    }
};
