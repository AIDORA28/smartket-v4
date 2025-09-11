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
        Schema::table('planes', function (Blueprint $table) {
            // Campos de precios
            $table->decimal('precio_mensual', 8, 2)->default(0)->after('nombre')->comment('Precio mensual del plan');
            $table->decimal('precio_anual', 8, 2)->default(0)->after('precio_mensual')->comment('Precio anual del plan');
            
            // Campo para trial
            $table->unsignedInteger('dias_prueba')->default(0)->after('precio_anual')->comment('Días de prueba gratuita');
            
            // Campo para rubros (todos empiezan con 1)
            $table->unsignedInteger('max_rubros')->default(1)->after('max_sucursales')->comment('Máximo rubros permitidos');
            
            // Campos de configuración
            $table->text('descripcion')->nullable()->after('nombre')->comment('Descripción del plan para mostrar');
            $table->json('caracteristicas_json')->nullable()->after('limites_json')->comment('Lista de características del plan');
            $table->boolean('es_gratis')->default(false)->after('activo')->comment('Si el plan es gratuito');
            $table->boolean('es_visible')->default(true)->after('es_gratis')->comment('Si el plan es visible en pricing');
            $table->unsignedInteger('orden_display')->default(0)->after('es_visible')->comment('Orden para mostrar en UI');
            
            // Índices
            $table->index(['activo', 'es_visible']);
            $table->index('orden_display');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planes', function (Blueprint $table) {
            $table->dropIndex(['activo', 'es_visible']);
            $table->dropIndex(['orden_display']);
            
            $table->dropColumn([
                'precio_mensual',
                'precio_anual', 
                'dias_prueba',
                'max_rubros',
                'descripcion',
                'caracteristicas_json',
                'es_gratis',
                'es_visible',
                'orden_display'
            ]);
        });
    }
};

