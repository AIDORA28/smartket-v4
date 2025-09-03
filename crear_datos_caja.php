<?php

// Script para crear datos de prueba del módulo caja

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Caja;

echo "=== CREANDO DATOS DE PRUEBA MÓDULO CAJA ===\n\n";

// Obtener primera empresa y sucursal
$empresa = Empresa::first();
$sucursal = Sucursal::first();

if (!$empresa || !$sucursal) {
    echo "❌ Error: No hay empresa o sucursal disponible\n";
    exit(1);
}

echo "✅ Empresa encontrada: {$empresa->nombre}\n";
echo "✅ Sucursal encontrada: {$sucursal->nombre}\n\n";

// Crear cajas de prueba
$cajas = [
    [
        'nombre' => 'Caja Principal',
        'codigo' => 'CJ-001',
        'tipo' => 'principal'
    ],
    [
        'nombre' => 'Caja Secundaria',
        'codigo' => 'CJ-002', 
        'tipo' => 'secundaria'
    ]
];

foreach ($cajas as $cajaData) {
    $caja = Caja::create([
        'empresa_id' => $empresa->id,
        'sucursal_id' => $sucursal->id,
        'nombre' => $cajaData['nombre'],
        'codigo' => $cajaData['codigo'],
        'tipo' => $cajaData['tipo'],
        'activa' => true
    ]);
    
    echo "✅ Caja creada: {$caja->nombre} (ID: {$caja->id})\n";
}

echo "\n=== DATOS DE PRUEBA CREADOS EXITOSAMENTE ===\n";
echo "Total cajas: " . Caja::count() . "\n";
