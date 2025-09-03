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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: ventas por empresa');
            $table->foreignId('sucursal_id')->constrained('sucursales')->comment('Sucursal donde se realizó la venta');
            $table->foreignId('usuario_id')->constrained('users')->comment('Usuario que realizó la venta');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->comment('Cliente (opcional para venta anónima)');
            $table->string('numero_venta', 50)->comment('Número correlativo de venta');
            $table->enum('tipo_comprobante', ['boleta', 'factura', 'nota_credito', 'nota_debito', 'ticket'])->default('ticket')->comment('Tipo de comprobante');
            $table->string('serie_comprobante', 10)->nullable()->comment('Serie del comprobante');
            $table->string('numero_comprobante', 20)->nullable()->comment('Número del comprobante');
            $table->enum('estado', ['pendiente', 'pagada', 'anulada', 'devuelta'])->default('pendiente')->comment('Estado de la venta');
            $table->datetime('fecha_venta')->comment('Fecha y hora de la venta');
            $table->decimal('subtotal', 10, 2)->default(0)->comment('Subtotal sin descuentos ni impuestos');
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->comment('Descuento aplicado (%)');
            $table->decimal('descuento_monto', 10, 2)->default(0)->comment('Monto de descuento aplicado');
            $table->decimal('impuesto_porcentaje', 5, 2)->default(18)->comment('Porcentaje de IGV');
            $table->decimal('impuesto_monto', 10, 2)->default(0)->comment('Monto del IGV');
            $table->decimal('total', 10, 2)->default(0)->comment('Total final de la venta');
            $table->decimal('total_pagado', 10, 2)->default(0)->comment('Total pagado por el cliente');
            $table->decimal('vuelto', 10, 2)->default(0)->comment('Vuelto entregado');
            $table->text('observaciones')->nullable()->comment('Observaciones de la venta');
            $table->tinyInteger('requiere_facturacion')->default(0)->comment('Si requiere facturación electrónica');
            $table->datetime('fecha_anulacion')->nullable()->comment('Fecha de anulación si aplica');
            $table->text('motivo_anulacion')->nullable()->comment('Motivo de anulación');
            $table->json('extras_json')->nullable()->comment('Datos adicionales: delivery, mesa, etc');
            $table->timestamps();
            
            $table->unique(['empresa_id', 'numero_venta']);
            $table->index(['empresa_id', 'sucursal_id', 'fecha_venta']);
            $table->index(['empresa_id', 'usuario_id']);
            $table->index(['empresa_id', 'cliente_id']);
            $table->index(['empresa_id', 'estado']);
            $table->index(['empresa_id', 'tipo_comprobante']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
