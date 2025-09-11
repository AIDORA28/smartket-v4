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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nombre', 160)->comment('Nombre completo o razón social');
            $table->string('documento_tipo', 10)->nullable()->comment('DNI/RUC/CE');
            $table->string('documento_numero', 20)->nullable()->comment('Número del documento');
            $table->string('telefono', 20)->nullable()->comment('Teléfono principal');
            $table->string('email', 120)->nullable()->comment('Email principal');
            $table->string('direccion', 200)->nullable()->comment('Dirección completa');
            $table->json('contacto_json')->nullable()->comment('Información adicional de contacto');
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id'], 'idx_proveedores_empresa');
            $table->index(['empresa_id', 'nombre'], 'idx_proveedores_empresa_nombre');
            $table->index(['empresa_id', 'documento_numero'], 'idx_proveedores_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};

