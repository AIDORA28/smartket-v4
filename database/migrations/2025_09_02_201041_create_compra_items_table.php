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
        Schema::create('compra_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Denormalizado para queries');
            $table->foreignId('compra_id')->constrained('compras');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('lote_id')->nullable()->comment('Lote asignado al item - será implementado en Módulo 6');
            $table->decimal('cantidad', 14, 3)->default(0)->comment('Cantidad comprada');
            $table->decimal('costo_unitario', 14, 4)->default(0.0000)->comment('Costo unitario de compra');
            $table->decimal('descuento_pct', 5, 2)->default(0.00)->comment('Descuento en porcentaje');
            $table->decimal('subtotal', 14, 2)->default(0.00)->comment('cantidad * costo_unitario - descuento');
            $table->timestamps();
            
            // Índices
            $table->index(['compra_id'], 'idx_compra_items_compra');
            $table->index(['producto_id'], 'idx_compra_items_producto');
            $table->index(['empresa_id', 'producto_id'], 'idx_compra_items_empresa_producto');
            // $table->index(['lote_id'], 'idx_compra_items_lote'); // Se agregará en Módulo 6
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compra_items');
    }
};
