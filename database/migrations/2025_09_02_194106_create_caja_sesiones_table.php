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
        Schema::create('caja_sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->foreignId('caja_id')->constrained('cajas')->onDelete('cascade');
            $table->foreignId('user_apertura_id')->constrained('users')->onDelete('cascade')
                  ->comment('Usuario que abre la sesión');
            $table->foreignId('user_cierre_id')->nullable()->constrained('users')->onDelete('set null')
                  ->comment('Usuario que cierra la sesión');
            $table->string('codigo', 20)->nullable()->comment('Código de sesión: CX-YYYYMMDD-n');
            $table->dateTime('apertura_at')->comment('Fecha y hora de apertura');
            $table->dateTime('cierre_at')->nullable()->comment('Fecha y hora de cierre');
            $table->decimal('monto_inicial', 14, 2)->default(0.00)->comment('Monto inicial en caja');
            $table->decimal('monto_ingresos', 14, 2)->default(0.00)->comment('Ingresos adicionales');
            $table->decimal('monto_retiros', 14, 2)->default(0.00)->comment('Retiros de caja');
            $table->decimal('monto_ventas_efectivo', 14, 2)->default(0.00)->comment('Total ventas en efectivo');
            $table->decimal('monto_declarado_cierre', 14, 2)->nullable()->comment('Monto declarado al cierre');
            $table->decimal('diferencia', 14, 2)->nullable()->comment('Diferencia: declarado - calculado');
            $table->enum('estado', ['abierta', 'cerrada', 'anulada'])->default('abierta')->comment('Estado de la sesión');
            $table->text('observaciones')->nullable()->comment('Observaciones de la sesión');
            $table->timestamps();
            
            $table->index(['empresa_id', 'estado'], 'idx_caja_sesiones_empresa_estado');
            $table->index('caja_id', 'idx_caja_sesiones_caja');
            $table->index('apertura_at', 'idx_caja_sesiones_apertura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja_sesiones');
    }
};

