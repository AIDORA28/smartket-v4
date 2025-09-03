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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->string('nombre', 60)->comment('Nombre de la caja: Caja Principal, Caja 2');
            $table->string('codigo', 20)->nullable()->comment('Código único de la caja');
            $table->string('tipo', 15)->default('principal')->comment('principal/secundaria/movil');
            $table->boolean('activa')->default(true)->comment('Si la caja está activa');
            $table->json('configuracion_json')->nullable()->comment('Configuraciones específicas de la caja');
            $table->timestamps();
            
            $table->index(['empresa_id', 'sucursal_id'], 'idx_cajas_empresa_sucursal');
            $table->index('activa', 'idx_cajas_activa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
