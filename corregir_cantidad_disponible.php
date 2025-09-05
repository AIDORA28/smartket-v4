<?php
/**
 * Script para corregir referencias a 'cantidad_disponible' en DashboardService
 * Remueve las condiciones where que no existen en el esquema real de lotes
 */

$archivo = 'd:\VS Code\SmartKet-v4\app\Services\DashboardService.php';
$contenido = file_get_contents($archivo);

// Array de correcciones: eliminar todas las condiciones ->where('cantidad_disponible', '>', 0)
$correcciones = [
    // En obtenerAlertas - lotes vencidos
    [
        'buscar' => '        $lotesVencidos = Lote::where(\'empresa_id\', $empresaId)
            ->when($sucursalId, fn($q) => $q->where(\'sucursal_id\', $sucursalId))
            ->where(\'fecha_vencimiento\', \'<\', Carbon::now())
            ->where(\'cantidad_disponible\', \'>\', 0)
            ->with(\'producto\')
            ->get();',
        'reemplazar' => '        $lotesVencidos = Lote::where(\'empresa_id\', $empresaId)
            ->when($sucursalId, fn($q) => $q->where(\'sucursal_id\', $sucursalId))
            ->where(\'fecha_vencimiento\', \'<\', Carbon::now())
            ->with(\'producto\')
            ->get();'
    ],
    // En obtenerAlertas - próximos a vencer
    [
        'buscar' => '        $proximosVencer = Lote::where(\'empresa_id\', $empresaId)
            ->when($sucursalId, fn($q) => $q->where(\'sucursal_id\', $sucursalId))
            ->whereBetween(\'fecha_vencimiento\', [Carbon::now(), Carbon::now()->addDays(7)])
            ->where(\'cantidad_disponible\', \'>\', 0)
            ->with(\'producto\')
            ->get();',
        'reemplazar' => '        $proximosVencer = Lote::where(\'empresa_id\', $empresaId)
            ->when($sucursalId, fn($q) => $q->where(\'sucursal_id\', $sucursalId))
            ->whereBetween(\'fecha_vencimiento\', [Carbon::now(), Carbon::now()->addDays(7)])
            ->with(\'producto\')
            ->get();'
    ],
    // En contarLotesProximosVencer
    [
        'buscar' => '        return Lote::where(\'empresa_id\', $empresaId)
            ->when($sucursalId, fn($q) => $q->where(\'sucursal_id\', $sucursalId))
            ->whereBetween(\'fecha_vencimiento\', [Carbon::now(), Carbon::now()->addDays(30)])
            ->where(\'cantidad_disponible\', \'>\', 0)
            ->count();',
        'reemplazar' => '        return Lote::where(\'empresa_id\', $empresaId)
            ->when($sucursalId, fn($q) => $q->where(\'sucursal_id\', $sucursalId))
            ->whereBetween(\'fecha_vencimiento\', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();'
    ],
    // En contarLotesVencidos
    [
        'buscar' => '        return Lote::where(\'empresa_id\', $empresaId)
            ->when($sucursalId, fn($q) => $q->where(\'sucursal_id\', $sucursalId))
            ->where(\'fecha_vencimiento\', \'<\', Carbon::now())
            ->where(\'cantidad_disponible\', \'>\', 0)
            ->count();',
        'reemplazar' => '        return Lote::where(\'empresa_id\', $empresaId)
            ->when($sucursalId, fn($q) => $q->where(\'sucursal_id\', $sucursalId))
            ->where(\'fecha_vencimiento\', \'<\', Carbon::now())
            ->count();'
    ]
];

$cambios_realizados = 0;

foreach ($correcciones as $correccion) {
    if (strpos($contenido, $correccion['buscar']) !== false) {
        $contenido = str_replace($correccion['buscar'], $correccion['reemplazar'], $contenido);
        $cambios_realizados++;
        echo "✓ Corregido: " . substr($correccion['buscar'], 0, 50) . "...\n";
    } else {
        echo "⚠ No encontrado: " . substr($correccion['buscar'], 0, 50) . "...\n";
    }
}

if ($cambios_realizados > 0) {
    file_put_contents($archivo, $contenido);
    echo "\n🎉 DashboardService actualizado exitosamente!\n";
    echo "Cambios realizados: $cambios_realizados\n";
    echo "Se removieron todas las referencias a 'cantidad_disponible' en consultas de Lotes\n";
} else {
    echo "\n❌ No se realizaron cambios en DashboardService\n";
}
?>
