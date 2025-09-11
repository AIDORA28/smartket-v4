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
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade')->comment('Venta a la que pertenece');
            $table->foreignId('producto_id')->constrained('productos')->comment('Producto vendido');
            $table->decimal('cantidad', 10, 3)->comment('Cantidad vendida (permite decimales)');
            $table->decimal('precio_unitario', 10, 4)->comment('Precio unitario al momento de la venta');
            $table->decimal('precio_costo', 10, 4)->comment('Costo unitario al momento de la venta');
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->comment('Descuento aplicado al item (%)');
            $table->decimal('descuento_monto', 10, 2)->default(0)->comment('Monto de descuento del item');
            $table->decimal('subtotal', 10, 2)->comment('Subtotal del item (cantidad * precio - descuento)');
            $table->decimal('impuesto_monto', 10, 2)->default(0)->comment('IGV del item');
            $table->decimal('total', 10, 2)->comment('Total del item con impuestos');
            $table->text('observaciones')->nullable()->comment('Observaciones del item');
            $table->timestamps();
            
            $table->index(['venta_id']);
            $table->index(['producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};

