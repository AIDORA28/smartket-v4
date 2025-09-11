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
        Schema::create('sucursal_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade')
                  ->comment('Relación con sucursal');
            
            // Configuraciones operativas
            $table->json('horarios_atencion')->nullable()
                  ->comment('Horarios de atención por día de semana');
            $table->json('configuracion_pos')->nullable()
                  ->comment('Configuración del punto de venta');
            $table->json('configuracion_inventario')->nullable()
                  ->comment('Alertas stock específicas de sucursal');
            
            // Configuraciones de personal
            $table->unsignedInteger('limite_usuarios')->default(5)
                  ->comment('Límite de usuarios para esta sucursal');
            $table->json('roles_permitidos')->nullable()
                  ->comment('Roles que pueden operar en esta sucursal');
            
            // Configuraciones de ventas
            $table->boolean('permite_ventas_online')->default(false)
                  ->comment('Si permite ventas online');
            $table->boolean('permite_delivery')->default(false)
                  ->comment('Si ofrece servicio delivery');
            $table->decimal('radio_delivery_km', 5, 2)->nullable()
                  ->comment('Radio de delivery en kilómetros');
            $table->decimal('costo_delivery', 8, 2)->nullable()
                  ->comment('Costo del delivery');
            
            // Configuraciones de reportes
            $table->json('configuracion_reportes')->nullable()
                  ->comment('Configuración reportes específicos');
            $table->boolean('reporte_diario_automatico')->default(true)
                  ->comment('Enviar reporte diario automático');
            
            // Configuraciones de seguridad
            $table->json('configuracion_cajas')->nullable()
                  ->comment('Configuración de cajas registradoras');
            $table->boolean('requiere_supervisor_descuentos')->default(true)
                  ->comment('Requiere supervisor para descuentos');
            
            // Configuraciones de notificaciones
            $table->json('configuracion_notificaciones')->nullable()
                  ->comment('Notificaciones específicas sucursal');
            
            $table->timestamps();
            
            // Índices
            $table->unique('sucursal_id', 'sucursal_settings_sucursal_unique');
            $table->index('permite_ventas_online');
            $table->index('permite_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursal_settings');
    }
};
