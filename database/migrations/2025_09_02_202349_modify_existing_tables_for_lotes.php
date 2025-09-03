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
        // Agregar foreign key constraint a compra_items que ya tiene lote_id
        Schema::table('compra_items', function (Blueprint $table) {
            $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('set null');
            $table->index(['lote_id'], 'idx_compra_items_lote');
        });

        // Agregar lote_id a inventario_movimientos si no existe
        if (Schema::hasTable('inventario_movimientos')) {
            Schema::table('inventario_movimientos', function (Blueprint $table) {
                if (!Schema::hasColumn('inventario_movimientos', 'lote_id')) {
                    $table->foreignId('lote_id')->nullable()->after('producto_id')->constrained('lotes')->comment('Lote específico del movimiento');
                }
            });
        }

        // Agregar lote_id a venta_detalles para trazabilidad de salidas
        if (Schema::hasTable('venta_detalles')) {
            Schema::table('venta_detalles', function (Blueprint $table) {
                if (!Schema::hasColumn('venta_detalles', 'lote_id')) {
                    $table->foreignId('lote_id')->nullable()->after('producto_id')->constrained('lotes')->comment('Lote utilizado en la venta');
                    $table->index(['lote_id'], 'idx_venta_detalles_lote');
                }
            });
        }

        // Agregar campos relacionados con lotes a producto_stocks
        if (Schema::hasTable('producto_stocks')) {
            Schema::table('producto_stocks', function (Blueprint $table) {
                if (!Schema::hasColumn('producto_stocks', 'alerta_vencimiento_dias')) {
                    $table->integer('alerta_vencimiento_dias')->default(30)->after('ultimo_movimiento')->comment('Días antes del vencimiento para alertar');
                }
                if (!Schema::hasColumn('producto_stocks', 'maneja_lotes')) {
                    $table->boolean('maneja_lotes')->default(false)->after('alerta_vencimiento_dias')->comment('Si el producto maneja control por lotes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar campos agregados en orden inverso
        if (Schema::hasTable('producto_stocks')) {
            Schema::table('producto_stocks', function (Blueprint $table) {
                if (Schema::hasColumn('producto_stocks', 'maneja_lotes')) {
                    $table->dropColumn('maneja_lotes');
                }
                if (Schema::hasColumn('producto_stocks', 'alerta_vencimiento_dias')) {
                    $table->dropColumn('alerta_vencimiento_dias');
                }
            });
        }

        if (Schema::hasTable('venta_detalles')) {
            Schema::table('venta_detalles', function (Blueprint $table) {
                if (Schema::hasColumn('venta_detalles', 'lote_id')) {
                    $table->dropForeign(['lote_id']);
                    $table->dropIndex('idx_venta_detalles_lote');
                    $table->dropColumn('lote_id');
                }
            });
        }

        if (Schema::hasTable('inventario_movimientos')) {
            Schema::table('inventario_movimientos', function (Blueprint $table) {
                if (Schema::hasColumn('inventario_movimientos', 'lote_id')) {
                    $table->dropForeign(['lote_id']);
                    $table->dropColumn('lote_id');
                }
            });
        }

        Schema::table('compra_items', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropIndex('idx_compra_items_lote');
        });
    }
};
