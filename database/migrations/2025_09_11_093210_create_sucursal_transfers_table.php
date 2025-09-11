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
        Schema::create('sucursal_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('numero_transferencia', 20)->unique()
                  ->comment('Número único de transferencia');
            
            // Sucursales involucradas
            $table->foreignId('sucursal_origen_id')->constrained('sucursales')
                  ->comment('Sucursal que envía productos');
            $table->foreignId('sucursal_destino_id')->constrained('sucursales')
                  ->comment('Sucursal que recibe productos');
            
            // Información de la transferencia
            $table->enum('estado', ['pendiente', 'en_transito', 'recibida', 'cancelada'])
                  ->default('pendiente')->comment('Estado de la transferencia');
            $table->enum('tipo_transferencia', ['reposicion', 'equilibrio', 'devolucion', 'manual'])
                  ->default('manual')->comment('Tipo de transferencia');
            
            // Fechas importantes
            $table->timestamp('fecha_solicitud')
                  ->comment('Fecha de solicitud transferencia');
            $table->timestamp('fecha_envio')->nullable()
                  ->comment('Fecha de envío');
            $table->timestamp('fecha_recepcion')->nullable()
                  ->comment('Fecha de recepción');
            
            // Personal involucrado
            $table->foreignId('usuario_solicita_id')->constrained('users')
                  ->comment('Usuario que solicita transferencia');
            $table->foreignId('usuario_envia_id')->nullable()->constrained('users')
                  ->comment('Usuario que envía transferencia');
            $table->foreignId('usuario_recibe_id')->nullable()->constrained('users')
                  ->comment('Usuario que recibe transferencia');
            
            // Información adicional
            $table->text('motivo')->nullable()
                  ->comment('Motivo de la transferencia');
            $table->text('observaciones')->nullable()
                  ->comment('Observaciones adicionales');
            $table->json('documentos_adjuntos')->nullable()
                  ->comment('URLs de documentos adjuntos');
            
            // Totales
            $table->unsignedInteger('total_items')->default(0)
                  ->comment('Total de ítems en transferencia');
            $table->decimal('valor_total', 12, 2)->default(0)
                  ->comment('Valor total de la transferencia');
            
            // Control de versiones
            $table->boolean('requiere_aprobacion')->default(false)
                  ->comment('Si requiere aprobación especial');
            $table->foreignId('usuario_aprueba_id')->nullable()->constrained('users')
                  ->comment('Usuario que aprueba la transferencia');
            $table->timestamp('fecha_aprobacion')->nullable()
                  ->comment('Fecha de aprobación');
            
            $table->timestamps();
            
            // Índices
            $table->index(['sucursal_origen_id', 'estado']);
            $table->index(['sucursal_destino_id', 'estado']);
            $table->index('fecha_solicitud');
            $table->index('tipo_transferencia');
            $table->index('numero_transferencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursal_transfers');
    }
};
