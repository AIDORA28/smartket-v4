<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar columna activa a tabla empresas
        Schema::table('empresas', function (Blueprint $table) {
            $table->boolean('activa')->default(true)->after('connection_name')->comment('Si la empresa está activa');
        });

        // Agregar columna activa a tabla sucursales si no existe
        if (Schema::hasColumn('sucursales', 'activa') === false) {
            Schema::table('sucursales', function (Blueprint $table) {
                $table->boolean('activa')->default(true)->after('configuracion_json')->comment('Si la sucursal está activa');
            });
        }

        // Actualizar todas las empresas existentes como activas
        DB::table('empresas')->update(['activa' => true]);
        
        // Actualizar todas las sucursales existentes como activas
        DB::table('sucursales')->update(['activa' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('activa');
        });

        if (Schema::hasColumn('sucursales', 'activa')) {
            Schema::table('sucursales', function (Blueprint $table) {
                $table->dropColumn('activa');
            });
        }
    }
};
