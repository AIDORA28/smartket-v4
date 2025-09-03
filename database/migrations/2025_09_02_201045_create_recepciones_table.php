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
        Schema::create('recepciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('compra_id')->constrained('compras');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que registra la recepción');
            $table->datetime('fecha_recepcion')->comment('Fecha y hora de recepción');
            $table->string('estado', 15)->default('parcial')->comment('parcial/completa/con_diferencias');
            $table->text('observaciones')->nullable()->comment('Observaciones de la recepción');
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id'], 'idx_recepciones_empresa');
            $table->index(['compra_id'], 'idx_recepciones_compra');
            $table->index(['sucursal_id'], 'idx_recepciones_sucursal');
            $table->index(['fecha_recepcion'], 'idx_recepciones_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepciones');
    }
};
