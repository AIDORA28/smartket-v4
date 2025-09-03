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
        Schema::create('producto_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: stock por empresa');
            $table->foreignId('producto_id')->constrained('productos')->comment('Producto al que pertenece el stock');
            $table->foreignId('sucursal_id')->constrained('sucursales')->comment('Sucursal donde está el stock');
            $table->decimal('cantidad_actual', 10, 2)->default(0)->comment('Cantidad actual en stock');
            $table->decimal('cantidad_reservada', 10, 2)->default(0)->comment('Cantidad reservada (pedidos pendientes)');
            $table->decimal('cantidad_disponible', 10, 2)->default(0)->comment('Cache: actual - reservada');
            $table->decimal('costo_promedio', 10, 4)->default(0)->comment('Costo promedio ponderado');
            $table->datetime('ultimo_movimiento')->nullable()->comment('Fecha del último movimiento de stock');
            $table->timestamps();
            
            $table->unique(['producto_id', 'sucursal_id']);
            $table->index(['empresa_id', 'sucursal_id']);
            $table->index(['producto_id', 'cantidad_actual']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_stocks');
    }
};
