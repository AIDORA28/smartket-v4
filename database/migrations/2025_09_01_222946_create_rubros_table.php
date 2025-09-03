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
        Schema::create('rubros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60)->comment('panaderia/farmacia/minimarket/ferreteria/restaurante');
            $table->json('modulos_default_json')->nullable()->comment('Módulos por defecto para este rubro');
            $table->tinyInteger('activo')->default(1)->comment('Si el rubro está disponible');
            $table->timestamps();
            
            $table->unique('nombre');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubros');
    }
};
