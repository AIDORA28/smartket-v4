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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Multi-tenant: productos por empresa');
            $table->foreignId('categoria_id')->constrained('categorias')->comment('Categoría del producto');
            $table->string('nombre', 150)->comment('Nombre del producto');
            $table->string('codigo_interno', 30)->nullable()->comment('SKU o código interno');
            $table->string('codigo_barra', 50)->nullable()->comment('Código de barras EAN/UPC');
            $table->text('descripcion')->nullable()->comment('Descripción detallada del producto');
            $table->decimal('precio_costo', 10, 4)->default(0)->comment('Precio de costo sin IGV');
            $table->decimal('precio_venta', 10, 4)->default(0)->comment('Precio de venta con/sin IGV según config empresa');
            $table->decimal('margen_ganancia', 5, 2)->default(0)->comment('Porcentaje de margen sobre costo');
            $table->tinyInteger('incluye_igv')->default(1)->comment('Si el precio de venta incluye IGV');
            $table->string('unidad_medida', 20)->default('UNIDAD')->comment('UNIDAD/KG/LITRO/METRO/etc');
            $table->tinyInteger('permite_decimales')->default(0)->comment('Si permite venta en decimales');
            $table->tinyInteger('maneja_stock')->default(1)->comment('Si maneja control de inventario');
            $table->decimal('stock_minimo', 10, 2)->default(0)->comment('Stock mínimo para alertas');
            $table->decimal('stock_maximo', 10, 2)->default(0)->comment('Stock máximo recomendado');
            $table->tinyInteger('activo')->default(1)->comment('Si el producto está activo');
            $table->string('imagen_url', 255)->nullable()->comment('URL de imagen del producto');
            $table->json('extras_json')->nullable()->comment('Datos adicionales: dimensiones, peso, etc');
            $table->timestamps();
            
            $table->unique(['empresa_id', 'codigo_interno']);
            $table->unique(['empresa_id', 'codigo_barra']);
            $table->index(['empresa_id', 'categoria_id']);
            $table->index(['empresa_id', 'activo']);
            $table->index(['empresa_id', 'maneja_stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
