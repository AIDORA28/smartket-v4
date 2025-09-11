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
        Schema::create('empresa_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_addon_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('cantidad')->default(1)->comment('Cantidad comprada del addon');
            $table->date('fecha_inicio')->comment('Fecha de inicio del addon');
            $table->date('fecha_fin')->comment('Fecha de fin del addon');
            $table->decimal('precio_pagado', 8, 2)->comment('Precio pagado por el addon');
            $table->string('periodo')->comment('Periodo: mensual, anual');
            $table->boolean('activo')->default(true)->comment('Si el addon está activo');
            $table->json('configuracion_json')->nullable()->comment('Configuración específica del addon');
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id', 'activo']);
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->unique(['empresa_id', 'plan_addon_id'], 'empresa_addon_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_addons');
    }
};

