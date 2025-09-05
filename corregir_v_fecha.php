<?php

/**
 * Script para corregir TODAS las referencias restantes de v.fecha por v.fecha_venta
 */

require_once __DIR__ . '/vendor/autoload.php';

$archivos = [
    'app/Livewire/Reportes/Sales.php',
    'app/Services/DashboardService.php'
];

echo "🔧 CORRIGIENDO REFERENCIAS RESTANTES DE v.fecha\n";
echo "===============================================\n\n";

foreach ($archivos as $archivo) {
    $rutaCompleta = __DIR__ . '/' . $archivo;
    
    if (!file_exists($rutaCompleta)) {
        echo "⚠️ Archivo no encontrado: $archivo\n";
        continue;
    }
    
    $contenido = file_get_contents($rutaCompleta);
    $contenidoOriginal = $contenido;
    
    // Reemplazar v.fecha por v.fecha_venta
    $numReemplazos = 0;
    $contenido = str_replace('v.fecha', 'v.fecha_venta', $contenido, $numReemplazos);
    
    if ($numReemplazos > 0) {
        echo "✅ $archivo: Reemplazado 'v.fecha' por 'v.fecha_venta' ($numReemplazos veces)\n";
        file_put_contents($rutaCompleta, $contenido);
        echo "💾 Guardado: $archivo\n";
    } else {
        echo "ℹ️ Sin cambios: $archivo\n";
    }
    echo "\n";
}

echo "🎉 ¡Corrección completada!\n\n";

?>
