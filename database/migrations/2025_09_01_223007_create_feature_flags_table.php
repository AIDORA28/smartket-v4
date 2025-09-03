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
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('feature_key', 60)->comment('pos/multi_sucursal/lotes/caja/facturacion_electronica/variantes/smart_insights');
            $table->tinyInteger('enabled')->default(0)->comment('Si la feature está habilitada');
            $table->timestamps();
            
            $table->unique(['empresa_id', 'feature_key']);
            $table->index(['empresa_id', 'enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
    }
};
