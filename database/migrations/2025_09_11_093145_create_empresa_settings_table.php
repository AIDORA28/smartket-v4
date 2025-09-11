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
        Schema::create('empresa_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')
                  ->comment('Relación con empresa');
            
            // Configuraciones de notificaciones
            $table->json('configuracion_notificaciones')->nullable()
                  ->comment('Config emails, SMS, push notifications');
            
            // Configuraciones de facturación
            $table->json('configuracion_facturacion')->nullable()
                  ->comment('Numeración, formato, plantillas facturación');
            
            // Configuraciones de inventario
            $table->json('configuracion_inventario')->nullable()
                  ->comment('Alertas stock, políticas inventario');
            
            // Configuraciones de ventas
            $table->json('configuracion_ventas')->nullable()
                  ->comment('Descuentos automáticos, promociones');
            
            // Configuraciones de backup
            $table->json('configuracion_backup')->nullable()
                  ->comment('Frecuencia y retención de backups');
            
            // Configuraciones regionales
            $table->string('zona_horaria_predeterminada', 50)->default('America/Lima')
                  ->comment('Zona horaria por defecto');
            $table->string('idioma_predeterminado', 5)->default('es')
                  ->comment('Idioma por defecto (es, en, etc)');
            $table->string('moneda_predeterminada', 5)->default('PEN')
                  ->comment('Moneda por defecto (PEN, USD, etc)');
            
            // Configuraciones de UI/UX
            $table->json('configuracion_ui')->nullable()
                  ->comment('Preferencias interfaz usuario');
            
            // Configuraciones de seguridad
            $table->json('configuracion_seguridad')->nullable()
                  ->comment('Políticas seguridad, autenticación');
            
            $table->timestamps();
            
            // Índices
            $table->unique('empresa_id', 'empresa_settings_empresa_unique');
            $table->index('zona_horaria_predeterminada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_settings');
    }
};
