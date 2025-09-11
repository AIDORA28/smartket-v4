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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nombre', 120)->comment('Nombre de la sucursal');
            $table->string('codigo_interno', 20)->nullable()->comment('Código interno opcional');
            $table->string('direccion', 180)->nullable()->comment('Dirección física');
            $table->tinyInteger('activa')->default(1)->comment('Si la sucursal está activa');
            $table->timestamps();
            
            $table->index('empresa_id');
            $table->index(['empresa_id', 'activa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};

