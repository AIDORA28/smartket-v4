<?php

/**
 * Script para corregir las referencias de costo_unitario a precio_costo
 */

require_once __DIR__ . '/vendor/autoload.php';

$archivos = [
    'app/Services/AnalyticsService.php',
    'app/Services/DashboardService.php',
    'app/Livewire/Reportes/Sales.php'
];

echo "🔧 CORRIGIENDO REFERENCIAS DE COSTO\n";
echo "===================================\n\n";

foreach ($archivos as $archivo) {
    $rutaCompleta = __DIR__ . '/' . $archivo;
    
    if (!file_exists($rutaCompleta)) {
        echo "⚠️ Archivo no encontrado: $archivo\n";
        continue;
    }
    
    $contenido = file_get_contents($rutaCompleta);
    $contenidoOriginal = $contenido;
    
    $reemplazos = [
        'costo_unitario' => 'precio_costo',
        'p.costo_unitario' => 'p.precio_costo',
        'productos.costo_unitario' => 'productos.precio_costo'
    ];
    
    foreach ($reemplazos as $buscar => $reemplazar) {
        $numReemplazos = 0;
        $contenido = str_replace($buscar, $reemplazar, $contenido, $numReemplazos);
        if ($numReemplazos > 0) {
            echo "✅ $archivo: '$buscar' -> '$reemplazar' ($numReemplazos veces)\n";
        }
    }
    
    if ($contenido !== $contenidoOriginal) {
        file_put_contents($rutaCompleta, $contenido);
        echo "💾 Guardado: $archivo\n";
    } else {
        echo "ℹ️ Sin cambios: $archivo\n";
    }
    echo "\n";
}

echo "🎉 ¡Corrección de costo completada!\n\n";

?>
