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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120)->comment('Nombre comercial de la empresa');
            $table->string('ruc', 15)->nullable()->comment('RUC peruano (11 dígitos)');
            $table->tinyInteger('tiene_ruc')->default(0)->comment('Cache: si RUC está validado');
            $table->foreignId('plan_id')->constrained('planes')->comment('Plan contratado');
            $table->json('features_json')->nullable()->comment('Cache de features habilitadas (TTL 5min)');
            $table->tinyInteger('sucursales_enabled')->default(0)->comment('Si multi-sucursal está activo');
            $table->unsignedInteger('sucursales_count')->default(1)->comment('Cache: número de sucursales activas');
            $table->tinyInteger('allow_negative_stock')->default(0)->comment('Permitir stock negativo');
            $table->tinyInteger('precio_incluye_igv')->default(1)->comment('Si precios incluyen IGV por defecto');
            $table->string('timezone', 40)->default('America/Lima')->comment('Zona horaria de la empresa');
            $table->string('connection_name', 40)->nullable()->comment('Futuro: nombre de BD dedicada');
            $table->timestamps();
            
            $table->index('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};

