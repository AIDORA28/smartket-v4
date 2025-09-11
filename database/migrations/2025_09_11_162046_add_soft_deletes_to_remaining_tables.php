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
        // TABLAS ADICIONALES QUE NECESITAN SOFT DELETES
        
        // INVENTORY MODULE - Tablas que faltaron
        Schema::table('categorias', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de categorías');
        });

        Schema::table('marcas', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de marcas');
        });

        Schema::table('unidades_medida', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de unidades');
        });

        Schema::table('producto_stocks', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de stock');
        });

        // CORE MODULE - Tablas críticas adicionales
        Schema::table('metodos_pago', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de métodos de pago');
        });

        // WAREHOUSE MODULE (si existe)
        if (Schema::hasTable('lotes')) {
            Schema::table('lotes', function (Blueprint $table) {
                $table->softDeletes()->comment('Soft delete para auditoría de lotes');
            });
        }

        // REPORTS MODULE (si existen)
        if (Schema::hasTable('reportes')) {
            Schema::table('reportes', function (Blueprint $table) {
                $table->softDeletes()->comment('Soft delete para auditoría de reportes');
            });
        }

        if (Schema::hasTable('reporte_templates')) {
            Schema::table('reporte_templates', function (Blueprint $table) {
                $table->softDeletes()->comment('Soft delete para auditoría de plantillas');
            });
        }

        if (Schema::hasTable('analytics_eventos')) {
            Schema::table('analytics_eventos', function (Blueprint $table) {
                $table->softDeletes()->comment('Soft delete para auditoría de analytics');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('marcas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('unidades_medida', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('producto_stocks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('metodos_pago', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        if (Schema::hasTable('lotes')) {
            Schema::table('lotes', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('reportes')) {
            Schema::table('reportes', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('reporte_templates')) {
            Schema::table('reporte_templates', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('analytics_eventos')) {
            Schema::table('analytics_eventos', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
