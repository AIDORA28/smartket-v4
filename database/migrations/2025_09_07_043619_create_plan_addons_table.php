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
        Schema::create('plan_addons', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->comment('Tipo de addon: usuario, rubro, sucursal');
            $table->string('nombre')->comment('Nombre descriptivo del addon');
            $table->text('descripcion')->nullable()->comment('Descripción del addon');
            $table->decimal('precio_mensual', 8, 2)->default(0)->comment('Precio mensual del addon');
            $table->decimal('precio_anual', 8, 2)->default(0)->comment('Precio anual del addon');
            $table->unsignedInteger('cantidad')->default(1)->comment('Cantidad que proporciona el addon');
            $table->boolean('activo')->default(true)->comment('Si el addon está activo');
            $table->json('restricciones_json')->nullable()->comment('Restricciones específicas del addon');
            $table->timestamps();
            
            // Índices
            $table->index(['tipo', 'activo']);
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_addons');
    }
};
