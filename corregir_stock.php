<?php

/**
 * Script para corregir las referencias de stock_actual a cantidad_actual
 */

require_once __DIR__ . '/vendor/autoload.php';

$archivos = [
    'app/Services/AnalyticsService.php',
    'app/Services/DashboardService.php',
    'app/Livewire/Reportes/Sales.php'
];

echo "🔧 CORRIGIENDO REFERENCIAS DE STOCK\n";
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
        'stock_actual' => 'cantidad_actual',
        'ps.stock_actual' => 'ps.cantidad_actual',
        'producto_stocks.stock_actual' => 'producto_stocks.cantidad_actual'
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

echo "🎉 ¡Corrección de stock completada!\n\n";

?>
