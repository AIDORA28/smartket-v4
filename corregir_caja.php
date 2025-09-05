<?php

/**
 * Script para corregir las referencias de campos de caja_sesiones
 */

require_once __DIR__ . '/vendor/autoload.php';

$archivos = [
    'app/Services/DashboardService.php',
    'app/Services/AnalyticsService.php'
];

echo "🔧 CORRIGIENDO REFERENCIAS DE CAJA_SESIONES\n";
echo "==========================================\n\n";

foreach ($archivos as $archivo) {
    $rutaCompleta = __DIR__ . '/' . $archivo;
    
    if (!file_exists($rutaCompleta)) {
        echo "⚠️ Archivo no encontrado: $archivo\n";
        continue;
    }
    
    $contenido = file_get_contents($rutaCompleta);
    $contenidoOriginal = $contenido;
    
    $reemplazos = [
        'fecha_cierre' => 'cierre_at',
        'fecha_apertura' => 'apertura_at',
        'monto_cierre' => 'monto_declarado_cierre'
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

echo "🎉 ¡Corrección de caja_sesiones completada!\n\n";

?>
