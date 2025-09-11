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
        Schema::create('sucursal_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_transfer_id')->constrained('sucursal_transfers')
                  ->onDelete('cascade')->comment('Relación con transferencia');
            $table->foreignId('producto_id')->constrained('productos')
                  ->comment('Producto transferido');
            
            // Información del producto en transferencia
            $table->string('codigo_producto', 50)
                  ->comment('Código del producto al momento transferencia');
            $table->string('nombre_producto', 150)
                  ->comment('Nombre del producto al momento transferencia');
            $table->string('unidad_medida', 20)->nullable()
                  ->comment('Unidad de medida del producto');
            
            // Cantidades
            $table->decimal('cantidad_solicitada', 10, 3)
                  ->comment('Cantidad solicitada');
            $table->decimal('cantidad_enviada', 10, 3)->default(0)
                  ->comment('Cantidad realmente enviada');
            $table->decimal('cantidad_recibida', 10, 3)->default(0)
                  ->comment('Cantidad recibida y verificada');
            
            // Stock información
            $table->decimal('stock_origen_antes', 10, 3)->nullable()
                  ->comment('Stock en origen antes de transferencia');
            $table->decimal('stock_destino_antes', 10, 3)->nullable()
                  ->comment('Stock en destino antes de transferencia');
            
            // Precios y valores
            $table->decimal('precio_unitario', 10, 4)
                  ->comment('Precio unitario del producto');
            $table->decimal('valor_linea', 12, 2)
                  ->comment('Valor total de la línea');
            
            // Lotes y vencimientos (si aplica)
            $table->string('lote_numero', 50)->nullable()
                  ->comment('Número de lote si producto lo maneja');
            $table->date('fecha_vencimiento')->nullable()
                  ->comment('Fecha vencimiento si producto lo maneja');
            
            // Estado del ítem
            $table->enum('estado_item', ['pendiente', 'parcial', 'completo', 'faltante'])
                  ->default('pendiente')->comment('Estado específico del ítem');
            $table->text('observaciones_item')->nullable()
                  ->comment('Observaciones específicas del ítem');
            
            // Información de discrepancias
            $table->decimal('cantidad_diferencia', 10, 3)->default(0)
                  ->comment('Diferencia entre enviado y recibido');
            $table->text('motivo_diferencia')->nullable()
                  ->comment('Motivo de la diferencia si existe');
            
            $table->timestamps();
            
            // Índices
            $table->index(['sucursal_transfer_id', 'producto_id']);
            $table->index('codigo_producto');
            $table->index('estado_item');
            $table->index('lote_numero');
            $table->index('fecha_vencimiento');
            
            // Unique constraint para evitar duplicados
            $table->unique(['sucursal_transfer_id', 'producto_id', 'lote_numero'], 
                          'transfer_product_lote_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursal_transfer_items');
    }
};
