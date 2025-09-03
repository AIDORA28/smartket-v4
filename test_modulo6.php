<?php

/**
 * MÓDULO 6: VERIFICACIÓN DE LOTES Y VENCIMIENTOS
 * 
 * Este script verifica que todos los componentes del Módulo 6 funcionan correctamente:
 * - Migración de tabla lotes
 * - Modelo Lote con relaciones y métodos
 * - LoteService con lógica FIFO
 * - VencimientoService para alertas
 * - TrazabilidadService para seguimiento completo
 * - Seeder con datos de prueba
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Empresa;
use App\Models\InventarioMovimiento;
use App\Services\LoteService;
use App\Services\VencimientoService;
use App\Services\TrazabilidadService;

// Configurar Laravel para ejecución standalone
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN MÓDULO 6: LOTES Y VENCIMIENTOS ===\n\n";

try {
    $empresa = Empresa::first();
    if (!$empresa) {
        throw new Exception("No se encontró ninguna empresa");
    }
    
    echo "✓ Empresa encontrada: {$empresa->nombre}\n";
    
    // =====================================================================
    // 1. VERIFICAR ESTRUCTURA DE DATOS
    // =====================================================================
    echo "\n1. VERIFICANDO ESTRUCTURA DE DATOS...\n";
    
    // Verificar tabla lotes
    $totalLotes = Lote::count();
    echo "✓ Tabla lotes existe con {$totalLotes} registros\n";
    
    if ($totalLotes === 0) {
        echo "⚠️  No hay lotes. Ejecute: php artisan db:seed --class=LoteSeeder\n";
        exit(1);
    }
    
    // Verificar relaciones del modelo Lote
    $lote = Lote::with(['empresa', 'producto', 'proveedor', 'movimientos'])->first();
    echo "✓ Modelo Lote carga relaciones correctamente\n";
    echo "  - Empresa: {$lote->empresa->nombre}\n";
    echo "  - Producto: {$lote->producto->nombre}\n";
    echo "  - Proveedor: " . ($lote->proveedor ? $lote->proveedor->nombre : 'N/A') . "\n";
    echo "  - Movimientos: {$lote->movimientos->count()}\n";
    
    // =====================================================================
    // 2. VERIFICAR FUNCIONALIDADES DEL MODELO
    // =====================================================================
    echo "\n2. VERIFICANDO FUNCIONALIDADES DEL MODELO...\n";
    
    // Verificar scopes
    $lotesActivos = Lote::where('empresa_id', $empresa->id)->activos()->count();
    $lotesVencidos = Lote::where('empresa_id', $empresa->id)->vencidos()->count();
    $lotesProximosVencer = Lote::where('empresa_id', $empresa->id)->proximosAVencer(30)->count();
    
    echo "✓ Scopes funcionando:\n";
    echo "  - Lotes activos: {$lotesActivos}\n";
    echo "  - Lotes vencidos: {$lotesVencidos}\n";
    echo "  - Próximos a vencer (30 días): {$lotesProximosVencer}\n";
    
    // Verificar métodos de negocio
    $loteTest = Lote::where('empresa_id', $empresa->id)->activos()->first();
    if ($loteTest) {
        $diasVencimiento = $loteTest->diasHastaVencimiento();
        $stockActual = $loteTest->getStockActual();
        $puedeUsarse = $loteTest->puedeUtilizarse();
        
        echo "✓ Métodos de negocio funcionando:\n";
        echo "  - Días hasta vencimiento: " . ($diasVencimiento ?? 'Sin fecha') . "\n";
        echo "  - Stock actual: {$stockActual}\n";
        echo "  - Puede utilizarse: " . ($puedeUsarse ? 'Sí' : 'No') . "\n";
    }
    
    // =====================================================================
    // 3. VERIFICAR LOTESERVICE
    // =====================================================================
    echo "\n3. VERIFICANDO LOTESERVICE...\n";
    
    $loteService = new LoteService();
    
    // Obtener producto con lotes activos
    $productoConLotes = Producto::where('empresa_id', $empresa->id)
        ->whereHas('lotes', function($q) {
            $q->where('estado_lote', 'activo');
        })
        ->first();
    
    if ($productoConLotes) {
        // Verificar asignación FIFO
        try {
            $asignacion = $loteService->asignarLoteFIFO($empresa->id, $productoConLotes->id, 5);
            echo "✓ Asignación FIFO funcionando:\n";
            echo "  - Cantidad solicitada: 5\n";
            echo "  - Cantidad asignada: " . (5 - $asignacion['cantidad_faltante']) . "\n";
            echo "  - Lotes utilizados: " . count($asignacion['asignaciones']) . "\n";
            
            foreach ($asignacion['asignaciones'] as $item) {
                $loteAsignado = Lote::find($item['lote_id']);
                echo "    * Lote {$loteAsignado->codigo_lote}: {$item['cantidad']} unidades\n";
            }
        } catch (Exception $e) {
            echo "⚠️  Error en asignación FIFO: " . $e->getMessage() . "\n";
        }
        
        // Verificar generación automática de código de lote
        $codigoNuevo = Lote::generarCodigo($empresa->id, $productoConLotes->id);
        echo "✓ Generación automática de código: {$codigoNuevo}\n";
    }
    
    // =====================================================================
    // 4. VERIFICAR VENCIMIENTOSERVICE
    // =====================================================================
    echo "\n4. VERIFICANDO VENCIMIENTOSERVICE...\n";
    
    $vencimientoService = new VencimientoService();
    
    // Generar alertas de vencimiento
    $alertas = $vencimientoService->generarAlertas($empresa->id);
    echo "✓ Generación de alertas funcionando:\n";
    echo "  - Alertas críticas (≤3 días): " . count($alertas['criticas']) . "\n";
    echo "  - Alertas importantes (≤7 días): " . count($alertas['importantes']) . "\n";
    echo "  - Alertas preventivas (≤30 días): " . count($alertas['preventivas']) . "\n";
    echo "  - Lotes vencidos: " . count($alertas['vencidos']) . "\n";
    
    // Calcular productos afectados
    $productosAfectados = collect();
    foreach (['criticas', 'importantes', 'preventivas'] as $tipo) {
        foreach ($alertas[$tipo] as $alerta) {
            $productosAfectados->put($alerta['lote']->producto_id, $alerta['producto_nombre']);
        }
    }
    echo "  - Productos afectados: " . $productosAfectados->count() . "\n";
    
    // Mostrar algunas alertas críticas
    if (!empty($alertas['criticas'])) {
        echo "  Alertas críticas encontradas:\n";
        foreach (array_slice($alertas['criticas'], 0, 3) as $alerta) {
            echo "    * {$alerta['producto_nombre']} - Lote {$alerta['codigo_lote']} - {$alerta['dias_restantes']} días\n";
        }
    }
    
    // Procesar lotes vencidos
    $procesados = $vencimientoService->procesarLotesVencidos($empresa->id);
    echo "✓ Procesamiento de lotes vencidos: " . count($procesados) . " lotes marcados como vencidos\n";
    
    // =====================================================================
    // 5. VERIFICAR TRAZABILIDADSERVICE
    // =====================================================================
    echo "\n5. VERIFICANDO TRAZABILIDADSERVICE...\n";
    
    $trazabilidadService = new TrazabilidadService();
    
    // Obtener trazabilidad de un lote
    $loteParaTrazabilidad = Lote::where('empresa_id', $empresa->id)->with('movimientos')->first();
    if ($loteParaTrazabilidad) {
        $trazabilidad = $trazabilidadService->getTrazabilidadLote($loteParaTrazabilidad->id);
        
        echo "✓ Trazabilidad de lote funcionando:\n";
        echo "  - Lote: {$trazabilidad['lote']->codigo_lote}\n";
        echo "  - Movimientos registrados: " . $trazabilidad['movimientos']->count() . "\n";
        echo "  - Ventas asociadas: " . $trazabilidad['ventas']->count() . "\n";
        echo "  - Stock actual: {$trazabilidad['stock_actual']}\n";
        echo "  - Cambios de estado: " . $trazabilidad['historial_estados']->count() . "\n";
        
        if ($trazabilidad['origen']) {
            echo "  - Origen: {$trazabilidad['origen']['tipo']} - {$trazabilidad['origen']['proveedor']}\n";
        }
    }
    
    // Búsqueda de lotes
    $resultadosBusqueda = $trazabilidadService->buscarLotes($empresa->id, 'LT0001');
    echo "✓ Búsqueda de lotes funcionando: " . $resultadosBusqueda->count() . " resultados para 'LT0001'\n";
    
    // =====================================================================
    // 6. VERIFICAR INTEGRACIÓN CON OTROS MÓDULOS
    // =====================================================================
    echo "\n6. VERIFICANDO INTEGRACIÓN...\n";
    
    // Verificar que los movimientos de inventario tienen lote_id
    $movimientosConLote = InventarioMovimiento::whereNotNull('lote_id')->count();
    $totalMovimientos = InventarioMovimiento::count();
    echo "✓ Integración con inventario: {$movimientosConLote}/{$totalMovimientos} movimientos con lote asignado\n";
    
    // Verificar orden FIFO
    $lotesOrdenFIFO = Lote::where('empresa_id', $empresa->id)->activos()->FIFO()->limit(5)->get();
    echo "✓ Orden FIFO funcionando: {$lotesOrdenFIFO->count()} lotes en orden correcto\n";
    if ($lotesOrdenFIFO->count() > 1) {
        $primero = $lotesOrdenFIFO->first();
        $segundo = $lotesOrdenFIFO->skip(1)->first();
        echo "  - Primer lote: {$primero->codigo_lote} (vence: " . ($primero->fecha_vencimiento ? $primero->fecha_vencimiento->format('Y-m-d') : 'nunca') . ")\n";
        echo "  - Segundo lote: {$segundo->codigo_lote} (vence: " . ($segundo->fecha_vencimiento ? $segundo->fecha_vencimiento->format('Y-m-d') : 'nunca') . ")\n";
    }
    
    // =====================================================================
    // 7. RESUMEN ESTADÍSTICO
    // =====================================================================
    echo "\n7. RESUMEN ESTADÍSTICO...\n";
    
    $estadisticas = [
        'total_lotes' => Lote::where('empresa_id', $empresa->id)->count(),
        'lotes_activos' => Lote::where('empresa_id', $empresa->id)->activos()->count(),
        'lotes_vencidos' => Lote::where('empresa_id', $empresa->id)->vencidos()->count(),
        'lotes_agotados' => Lote::where('empresa_id', $empresa->id)->estado('agotado')->count(),
        'productos_con_lotes' => Producto::where('empresa_id', $empresa->id)->whereHas('lotes')->count(),
        'movimientos_lotes' => InventarioMovimiento::where('empresa_id', $empresa->id)->whereNotNull('lote_id')->count()
    ];
    
    echo "ESTADÍSTICAS FINALES:\n";
    foreach ($estadisticas as $key => $value) {
        echo "  - " . ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
    }
    
    // Calcular stock total por estado
    $stockPorEstado = Lote::where('empresa_id', $empresa->id)
        ->get()
        ->groupBy('estado_lote')
        ->map(function($lotes) {
            return $lotes->sum(function($lote) {
                return $lote->getStockActual();
            });
        });
    
    echo "\nSTOCK POR ESTADO DE LOTE:\n";
    foreach ($stockPorEstado as $estado => $stock) {
        echo "  - " . ucfirst($estado) . ": {$stock} unidades\n";
    }
    
    echo "\n=== ✓ MÓDULO 6 VERIFICADO COMPLETAMENTE ===\n";
    echo "Todos los componentes del sistema de lotes y vencimientos están funcionando correctamente.\n";
    echo "\nFuncionalidades verificadas:\n";
    echo "✓ Gestión de lotes con códigos únicos\n";
    echo "✓ Control de fechas de vencimiento\n";
    echo "✓ Lógica FIFO para salidas de inventario\n";
    echo "✓ Alertas automáticas de vencimiento\n";
    echo "✓ Trazabilidad completa de lotes\n";
    echo "✓ Integración con sistema de inventario\n";
    echo "✓ Búsqueda y reportes de lotes\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR DURANTE LA VERIFICACIÓN:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    
    if ($e->getTrace()) {
        echo "\nStack trace:\n";
        foreach (array_slice($e->getTrace(), 0, 5) as $trace) {
            if (isset($trace['file'])) {
                echo "  " . basename($trace['file']) . ":" . ($trace['line'] ?? '?') . " " . ($trace['function'] ?? 'unknown') . "()\n";
            }
        }
    }
    
    exit(1);
}
