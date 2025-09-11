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
        Schema::create('empresa_rubros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('rubro_id')->constrained('rubros');
            $table->tinyInteger('es_principal')->default(0)->comment('Si es el rubro principal de la empresa');
            $table->json('configuracion_json')->nullable()->comment('Configuración específica para este rubro');
            $table->timestamps();
            
            $table->unique(['empresa_id', 'rubro_id']);
            $table->index('empresa_id');
            $table->index('rubro_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_rubros');
    }
};

