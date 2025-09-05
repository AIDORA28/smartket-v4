<?php

/**
 * Script para corregir las referencias de 'fecha' a 'fecha_venta' en los servicios
 */

require_once __DIR__ . '/vendor/autoload.php';

// Archivos y correcciones a realizar
$correcciones = [
    'app/Services/AnalyticsService.php' => [
        "->whereBetween('fecha'" => "->whereBetween('fecha_venta'",
        "DATE(fecha)" => "DATE(fecha_venta)",
    ],
    'app/Services/DashboardService.php' => [
        "->whereBetween('fecha'" => "->whereBetween('fecha_venta'",
    ],
    'app/Services/ReporteVentasService.php' => [
        "->whereBetween('fecha'" => "->whereBetween('fecha_venta'",
        "DATE(fecha)" => "DATE(fecha_venta)",
        "'fecha'" => "'fecha_venta'",
    ],
    'app/Livewire/Reportes/Sales.php' => [
        "'fecha'" => "'fecha_venta'",
        "DATE(fecha)" => "DATE(fecha_venta)",
    ]
];

echo "🔧 CORRIGIENDO REFERENCIAS DE COLUMNA FECHA\n";
echo "==========================================\n\n";

foreach ($correcciones as $archivo => $reemplazos) {
    $rutaCompleta = __DIR__ . '/' . $archivo;
    
    if (!file_exists($rutaCompleta)) {
        echo "⚠️ Archivo no encontrado: $archivo\n";
        continue;
    }
    
    $contenido = file_get_contents($rutaCompleta);
    $contenidoOriginal = $contenido;
    
    foreach ($reemplazos as $buscar => $reemplazar) {
        $numReemplazos = 0;
        $contenido = str_replace($buscar, $reemplazar, $contenido, $numReemplazos);
        if ($numReemplazos > 0) {
            echo "✅ $archivo: Reemplazado '$buscar' -> '$reemplazar' ($numReemplazos veces)\n";
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

echo "🎉 ¡Corrección completada!\n\n";

// Verificar que el modelo Venta use los campos correctos
echo "🔍 VERIFICANDO MODELO VENTA...\n";
$modeloVenta = __DIR__ . '/app/Models/Venta.php';
if (file_exists($modeloVenta)) {
    $contenidoModelo = file_get_contents($modeloVenta);
    if (strpos($contenidoModelo, "protected \$dates = ['fecha_venta'];") === false && 
        strpos($contenidoModelo, "protected \$casts = [") !== false) {
        echo "⚠️ Considera agregar 'fecha_venta' a los casts del modelo Venta como 'datetime'\n";
    } else {
        echo "✅ Modelo Venta parece estar configurado correctamente\n";
    }
} else {
    echo "❌ No se encontró el modelo Venta\n";
}

echo "\n✨ Corrección finalizada. Ahora puedes ejecutar las pruebas nuevamente.\n";

?>
