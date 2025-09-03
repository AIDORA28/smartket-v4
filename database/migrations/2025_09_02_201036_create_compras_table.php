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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('sucursal_destino_id')->constrained('sucursales')->comment('Sucursal donde llega la mercadería');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que registra la compra');
            $table->datetime('fecha')->comment('Fecha de la compra');
            $table->string('numero_doc', 40)->nullable()->comment('Número de documento del proveedor');
            $table->string('tipo_doc', 15)->default('factura')->comment('factura/boleta/recibo/nota');
            $table->string('estado', 15)->default('borrador')->comment('borrador/confirmada/recibida/anulada');
            $table->decimal('total', 14, 2)->default(0.00)->comment('Total de la compra');
            $table->char('moneda', 3)->default('PEN')->comment('Moneda de la compra');
            $table->text('observaciones')->nullable()->comment('Observaciones de la compra');
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id', 'fecha'], 'idx_compras_empresa_fecha');
            $table->index(['empresa_id', 'estado'], 'idx_compras_empresa_estado');
            $table->index(['proveedor_id'], 'idx_compras_proveedor');
            $table->index(['sucursal_destino_id'], 'idx_compras_sucursal');
            $table->index(['user_id'], 'idx_compras_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
