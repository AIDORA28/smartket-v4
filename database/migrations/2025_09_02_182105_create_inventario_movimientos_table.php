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
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: movimientos por empresa');
            $table->foreignId('producto_id')->constrained('productos')->comment('Producto del movimiento');
            $table->foreignId('sucursal_id')->constrained('sucursales')->comment('Sucursal donde ocurre el movimiento');
            $table->foreignId('usuario_id')->constrained('users')->comment('Usuario que registra el movimiento');
            $table->string('tipo_movimiento', 20)->comment('ENTRADA/SALIDA/AJUSTE/TRANSFER_IN/TRANSFER_OUT');
            $table->string('referencia_tipo', 30)->nullable()->comment('COMPRA/VENTA/AJUSTE/TRANSFERENCIA/DEVOLUCION');
            $table->string('referencia_id', 50)->nullable()->comment('ID del documento que genera el movimiento');
            $table->decimal('cantidad', 10, 2)->comment('Cantidad del movimiento (+ entrada, - salida)');
            $table->decimal('costo_unitario', 10, 4)->default(0)->comment('Costo unitario del producto en este movimiento');
            $table->decimal('stock_anterior', 10, 2)->comment('Stock antes del movimiento');
            $table->decimal('stock_posterior', 10, 2)->comment('Stock después del movimiento');
            $table->text('observaciones')->nullable()->comment('Observaciones del movimiento');
            $table->datetime('fecha_movimiento')->comment('Fecha y hora del movimiento');
            $table->timestamps();
            
            $table->index(['empresa_id', 'producto_id', 'fecha_movimiento']);
            $table->index(['sucursal_id', 'fecha_movimiento']);
            $table->index(['tipo_movimiento', 'fecha_movimiento']);
            $table->index(['referencia_tipo', 'referencia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};
