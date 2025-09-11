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
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: métodos por empresa');
            $table->string('nombre', 50)->comment('Nombre del método de pago');
            $table->string('codigo', 20)->comment('Código interno del método');
            $table->enum('tipo', ['efectivo', 'tarjeta', 'transferencia', 'credito', 'digital'])->comment('Tipo de método de pago');
            $table->tinyInteger('requiere_referencia')->default(0)->comment('Si requiere número de referencia');
            $table->tinyInteger('afecta_caja')->default(1)->comment('Si afecta el flujo de caja');
            $table->decimal('comision_porcentaje', 5, 2)->default(0)->comment('Comisión que cobra (%)');
            $table->decimal('comision_fija', 8, 2)->default(0)->comment('Comisión fija que cobra');
            $table->tinyInteger('activo')->default(1)->comment('Si el método está activo');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
            $table->string('icono', 50)->nullable()->comment('Icono o emoji para mostrar');
            $table->timestamps();
            
            $table->unique(['empresa_id', 'codigo']);
            $table->index(['empresa_id', 'activo']);
            $table->index(['empresa_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodos_pago');
    }
};

