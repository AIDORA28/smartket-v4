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
        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: unidades por empresa');
            $table->string('nombre', 80)->comment('Nombre de la unidad (Kilogramo, Litro, etc)');
            $table->string('abreviacion', 10)->comment('Abreviación (Kg, L, Und, etc)');
            $table->string('tipo', 20)->default('GENERAL')->comment('PESO/VOLUMEN/LONGITUD/CONTABLE/GENERAL');
            $table->string('icono', 50)->default('⚖️')->comment('Emoji o clase de icono');
            $table->tinyInteger('activa')->default(1)->comment('Si la unidad está activa');
            $table->unsignedInteger('productos_count')->default(0)->comment('Cache: cantidad de productos');
            $table->timestamps();
            
            $table->unique(['empresa_id', 'nombre']);
            $table->unique(['empresa_id', 'abreviacion']);
            $table->index(['empresa_id', 'activa']);
            $table->index(['empresa_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades_medida');
    }
};

