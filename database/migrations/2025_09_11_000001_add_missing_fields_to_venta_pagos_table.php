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
        Schema::table('venta_pagos', function (Blueprint $table) {
            // Agregar campos faltantes para alinear con el modelo VentaPago
            $table->enum('estado', ['pendiente', 'confirmado', 'anulado', 'devuelto'])
                  ->default('pendiente')
                  ->after('fecha_pago')
                  ->comment('Estado del pago');
                  
            $table->decimal('comision_porcentaje', 5, 2)
                  ->default(0)
                  ->after('estado')
                  ->comment('Porcentaje de comisión del método de pago');
                  
            $table->decimal('comision_monto', 10, 2)
                  ->default(0)
                  ->after('comision_porcentaje')
                  ->comment('Monto de comisión calculado');
                  
            $table->decimal('monto_neto', 10, 2)
                  ->default(0)
                  ->after('comision_monto')
                  ->comment('Monto neto después de comisiones');
                  
            $table->json('extras_json')
                  ->nullable()
                  ->after('observaciones')
                  ->comment('Datos adicionales del pago en JSON');

            // Agregar índices para el nuevo campo estado
            $table->index(['venta_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venta_pagos', function (Blueprint $table) {
            $table->dropIndex(['venta_id', 'estado']);
            $table->dropColumn([
                'estado',
                'comision_porcentaje', 
                'comision_monto',
                'monto_neto',
                'extras_json'
            ]);
        });
    }
};
