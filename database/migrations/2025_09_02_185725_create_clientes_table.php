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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: clientes por empresa');
            $table->string('tipo_documento', 20)->default('DNI')->comment('DNI/RUC/CE/PASAPORTE');
            $table->string('numero_documento', 20)->comment('Número de documento de identidad');
            $table->string('nombre', 150)->comment('Nombre completo o razón social');
            $table->string('email', 150)->nullable()->comment('Correo electrónico');
            $table->string('telefono', 20)->nullable()->comment('Teléfono de contacto');
            $table->text('direccion')->nullable()->comment('Dirección completa');
            $table->date('fecha_nacimiento')->nullable()->comment('Fecha de nacimiento');
            $table->enum('genero', ['M', 'F', 'O'])->nullable()->comment('Género: Masculino/Femenino/Otro');
            $table->tinyInteger('es_empresa')->default(0)->comment('Si es persona jurídica (RUC)');
            $table->decimal('limite_credito', 10, 2)->default(0)->comment('Límite de crédito autorizado');
            $table->decimal('credito_usado', 10, 2)->default(0)->comment('Crédito actualmente usado');
            $table->tinyInteger('permite_credito')->default(0)->comment('Si se permite venta a crédito');
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->comment('Descuento por defecto (%)');
            $table->tinyInteger('activo')->default(1)->comment('Si el cliente está activo');
            $table->json('extras_json')->nullable()->comment('Datos adicionales: preferencias, notas, etc');
            $table->timestamps();
            
            $table->unique(['empresa_id', 'numero_documento']);
            $table->index(['empresa_id', 'activo']);
            $table->index(['empresa_id', 'tipo_documento']);
            $table->index(['empresa_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

