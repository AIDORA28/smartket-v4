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
        Schema::create('caja_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->foreignId('caja_sesion_id')->constrained()->onDelete('cascade');
            $table->string('tipo', 15)->comment('ingreso/retiro/venta_efectivo');
            $table->decimal('monto', 14, 2)->comment('Monto del movimiento');
            $table->string('concepto', 120)->comment('Concepto del movimiento');
            $table->string('referencia_tipo', 20)->nullable()->comment('venta/gasto/reposicion');
            $table->unsignedBigInteger('referencia_id')->nullable()->comment('ID del documento relacionado');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Usuario que registra el movimiento');
            $table->dateTime('fecha')->comment('Fecha y hora del movimiento');
            $table->timestamp('created_at')->nullable();
            
            $table->index('caja_sesion_id', 'idx_caja_movimientos_sesion');
            $table->index(['empresa_id', 'fecha'], 'idx_caja_movimientos_empresa_fecha');
            $table->index('tipo', 'idx_caja_movimientos_tipo');
            $table->index('user_id', 'idx_caja_movimientos_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja_movimientos');
    }
};
