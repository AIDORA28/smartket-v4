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
        // TABLAS CRÍTICAS PARA AUDITORÍA SUNAT
        
        // 1. CLIENTES - Registro de contribuyentes
        Schema::table('clientes', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría SUNAT');
        });

        // 2. PRODUCTOS - Inventario y precios históricos
        Schema::table('productos', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para trazabilidad de inventario');
        });

        // 3. VENTAS - Registro de transacciones (CRÍTICO)
        Schema::table('ventas', function (Blueprint $table) {
            $table->softDeletes()->comment('CRÍTICO: Soft delete para auditoría fiscal');
        });

        // 4. VENTA_DETALLES - Detalle de productos vendidos
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de productos');
        });

        // 5. VENTA_PAGOS - Registro de pagos recibidos
        Schema::table('venta_pagos', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de pagos');
        });

        // 6. COMPRAS - Registro de adquisiciones
        Schema::table('compras', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de compras');
        });

        // 7. COMPRA_ITEMS - Detalle de productos comprados
        Schema::table('compra_items', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para trazabilidad de compras');
        });

        // 8. PROVEEDORES - Registro de proveedores
        Schema::table('proveedores', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de proveedores');
        });

        // 9. INVENTARIO_MOVIMIENTOS - Auditoría de inventario (MUY CRÍTICO)
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->softDeletes()->comment('CRÍTICO: Auditoría completa de inventario');
        });

        // 10. CAJAS - Control de caja
        Schema::table('cajas', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de cajas');
        });

        // 11. CAJA_SESIONES - Sesiones de trabajo
        Schema::table('caja_sesiones', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de sesiones');
        });

        // 12. CAJA_MOVIMIENTOS - Movimientos de efectivo
        Schema::table('caja_movimientos', function (Blueprint $table) {
            $table->softDeletes()->comment('Soft delete para auditoría de efectivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // IMPORTANTE: NO eliminamos las columnas deleted_at en rollback
        // Una vez implementado soft deletes, debe mantenerse permanentemente
        
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('venta_pagos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('compra_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('caja_sesiones', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('caja_movimientos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
