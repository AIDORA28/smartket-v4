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
        Schema::create('venta_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade')->comment('Venta a la que pertenece el pago');
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago')->comment('Método de pago utilizado');
            $table->decimal('monto', 10, 2)->comment('Monto pagado con este método');
            $table->string('referencia', 100)->nullable()->comment('Número de referencia (voucher, transferencia, etc)');
            $table->text('observaciones')->nullable()->comment('Observaciones del pago');
            $table->datetime('fecha_pago')->comment('Fecha y hora del pago');
            $table->timestamps();
            
            $table->index(['venta_id']);
            $table->index(['metodo_pago_id']);
            $table->index(['fecha_pago']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_pagos');
    }
};

