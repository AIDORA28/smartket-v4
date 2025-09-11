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
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60)->comment('FREE_BASIC/STANDARD/PRO/ENTERPRISE');
            $table->unsignedInteger('max_usuarios')->default(5)->comment('Máximo usuarios permitidos');
            $table->unsignedInteger('max_sucursales')->default(1)->comment('Máximo sucursales permitidas');
            $table->unsignedInteger('max_productos')->default(1000)->comment('Máximo productos permitidos');
            $table->json('limites_json')->nullable()->comment('Otros límites: ventas_diarias, storage, etc');
            $table->tinyInteger('activo')->default(1)->comment('Si el plan está disponible');
            $table->unsignedInteger('grace_percent')->default(10)->comment('Porcentaje de gracia antes de bloqueo');
            $table->timestamps();
            
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};

