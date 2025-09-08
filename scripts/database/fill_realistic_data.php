<?php
/**
 * Script para llenar la base de datos con datos de prueba realistas
 * Uso: php scripts/database/fill_realistic_data.php
 */

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== LLENANDO BASE DE DATOS CON DATOS REALISTAS ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

try {
    DB::beginTransaction();

    // 1. LLENAR PRODUCTO_STOCKS CON STOCK INICIAL
    echo "1. 📦 Agregando stock inicial a productos...\n";
    
    $productos = DB::table('productos')->where('empresa_id', 1)->get();
    foreach ($productos as $producto) {
        // Stock aleatorio realista
        $stockActual = rand(20, 150);
        $stockDisponible = $stockActual - rand(0, 5); // Algunas reservas
        
        DB::table('producto_stocks')->insertOrIgnore([
            'empresa_id' => 1,
            'producto_id' => $producto->id,
            'sucursal_id' => 1,
            'cantidad_actual' => $stockActual,
            'cantidad_reservada' => $stockActual - $stockDisponible,
            'cantidad_disponible' => $stockDisponible,
            'costo_promedio' => $producto->precio_costo,
            'ultimo_movimiento' => Carbon::now()->subDays(rand(1, 30)),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'alerta_vencimiento_dias' => 30,
            'maneja_lotes' => 0
        ]);
    }
    echo "   ✅ Stock agregado para " . count($productos) . " productos\n";

    // 2. AGREGAR MÉTODOS DE PAGO ADICIONALES
    echo "2. 💳 Agregando métodos de pago...\n";
    
    $metodosPago = [
        ['nombre' => 'Tarjeta de Débito', 'codigo' => 'TD', 'tipo' => 'tarjeta'],
        ['nombre' => 'Tarjeta de Crédito', 'codigo' => 'TC', 'tipo' => 'tarjeta'],
        ['nombre' => 'Yape', 'codigo' => 'YAPE', 'tipo' => 'digital'],
        ['nombre' => 'Plin', 'codigo' => 'PLIN', 'tipo' => 'digital'],
        ['nombre' => 'Transferencia', 'codigo' => 'TRANS', 'tipo' => 'transferencia']
    ];

    foreach ($metodosPago as $index => $metodo) {
        DB::table('metodos_pago')->insertOrIgnore([
            'empresa_id' => 1,
            'nombre' => $metodo['nombre'],
            'codigo' => $metodo['codigo'],
            'tipo' => $metodo['tipo'],
            'requiere_referencia' => in_array($metodo['codigo'], ['YAPE', 'PLIN', 'TRANS']) ? 1 : 0,
            'afecta_caja' => $metodo['codigo'] === 'EFE' ? 1 : 0,
            'comision_porcentaje' => $metodo['tipo'] === 'tarjeta' ? 3.5 : 0,
            'comision_fija' => 0,
            'activo' => 1,
            'orden' => $index + 2,
            'icono' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
    echo "   ✅ " . count($metodosPago) . " métodos de pago agregados\n";

    // 3. CREAR VENTAS REALISTAS DE LOS ÚLTIMOS 30 DÍAS
    echo "3. 💰 Creando ventas realistas...\n";
    
    $clientes = DB::table('clientes')->where('empresa_id', 1)->pluck('id')->toArray();
    $metodosPagoIds = DB::table('metodos_pago')->where('empresa_id', 1)->pluck('id')->toArray();
    $productosConStock = DB::table('productos')
        ->join('producto_stocks', 'productos.id', '=', 'producto_stocks.producto_id')
        ->where('productos.empresa_id', 1)
        ->select('productos.id', 'productos.nombre', 'productos.precio_venta', 'productos.precio_costo', 'producto_stocks.cantidad_actual')
        ->get();

    $ventasCreadas = 0;
    
    // Crear ventas de los últimos 30 días
    for ($dia = 30; $dia >= 0; $dia--) {
        $fecha = Carbon::now()->subDays($dia);
        
        // Entre 2-8 ventas por día (más los fines de semana)
        $ventasPorDia = $fecha->isWeekend() ? rand(5, 12) : rand(2, 8);
        
        for ($v = 0; $v < $ventasPorDia; $v++) {
            $clienteId = $clientes[array_rand($clientes)];
            $horaVenta = $fecha->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59));
            
            // Crear la venta
            $ventaId = DB::table('ventas')->insertGetId([
                'empresa_id' => 1,
                'sucursal_id' => 1,
                'usuario_id' => 1, // José Pérez
                'cliente_id' => $clienteId,
                'numero_venta' => $horaVenta->format('Ymd') . '-' . str_pad($v + 1, 4, '0', STR_PAD_LEFT),
                'tipo_comprobante' => 'boleta',
                'serie_comprobante' => 'B001',
                'numero_comprobante' => str_pad($ventasCreadas + 1, 8, '0', STR_PAD_LEFT),
                'estado' => 'pagada',
                'fecha_venta' => $horaVenta,
                'subtotal' => 0, // Se calculará después
                'descuento_porcentaje' => 0,
                'descuento_monto' => 0,
                'impuesto_porcentaje' => 18,
                'impuesto_monto' => 0, // Se calculará después
                'total' => 0, // Se calculará después
                'total_pagado' => 0, // Se calculará después
                'vuelto' => 0,
                'observaciones' => null,
                'requiere_facturacion' => 0,
                'fecha_anulacion' => null,
                'motivo_anulacion' => null,
                'extras_json' => null,
                'created_at' => $horaVenta,
                'updated_at' => $horaVenta
            ]);

            // Crear detalles de venta (1-5 productos por venta)
            $productosEnVenta = rand(1, 5);
            $subtotalVenta = 0;
            
            for ($p = 0; $p < $productosEnVenta; $p++) {
                $producto = $productosConStock[array_rand($productosConStock->toArray())];
                $cantidad = rand(1, 5);
                $precioUnitario = $producto->precio_venta;
                $subtotalItem = $cantidad * $precioUnitario;
                $subtotalVenta += $subtotalItem;

                DB::table('venta_detalles')->insert([
                    'venta_id' => $ventaId,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'precio_costo' => $producto->precio_costo,
                    'descuento_porcentaje' => 0,
                    'descuento_monto' => 0,
                    'subtotal' => $subtotalItem,
                    'impuesto_monto' => $subtotalItem * 0.18,
                    'total' => $subtotalItem * 1.18,
                    'observaciones' => null,
                    'created_at' => $horaVenta,
                    'updated_at' => $horaVenta,
                    'lote_id' => null
                ]);

                // Crear movimiento de inventario
                $stockAnterior = $producto->cantidad_actual;
                $stockPosterior = $stockAnterior - $cantidad;

                DB::table('inventario_movimientos')->insert([
                    'empresa_id' => 1,
                    'producto_id' => $producto->id,
                    'sucursal_id' => 1,
                    'usuario_id' => 1,
                    'tipo_movimiento' => 'salida',
                    'referencia_tipo' => 'venta',
                    'referencia_id' => $ventaId,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $producto->precio_costo,
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $stockPosterior,
                    'observaciones' => "Venta #{$horaVenta->format('Ymd')}-" . str_pad($v + 1, 4, '0', STR_PAD_LEFT),
                    'fecha_movimiento' => $horaVenta,
                    'created_at' => $horaVenta,
                    'updated_at' => $horaVenta,
                    'lote_id' => null
                ]);

                // Actualizar stock del producto
                DB::table('producto_stocks')
                    ->where('producto_id', $producto->id)
                    ->where('sucursal_id', 1)
                    ->decrement('cantidad_actual', $cantidad);
                    
                DB::table('producto_stocks')
                    ->where('producto_id', $producto->id)
                    ->where('sucursal_id', 1)
                    ->decrement('cantidad_disponible', $cantidad);
            }

            // Calcular totales de la venta
            $impuestoMonto = $subtotalVenta * 0.18;
            $total = $subtotalVenta + $impuestoMonto;

            // Crear pago (aleatorio entre efectivo y otros métodos)
            $metodoPagoId = $metodosPagoIds[array_rand($metodosPagoIds)];
            
            DB::table('venta_pagos')->insert([
                'venta_id' => $ventaId,
                'metodo_pago_id' => $metodoPagoId,
                'monto' => $total,
                'referencia' => $metodoPagoId != 3 ? 'REF-' . rand(100000, 999999) : null, // 3 = Efectivo
                'observaciones' => null,
                'fecha_pago' => $horaVenta,
                'created_at' => $horaVenta,
                'updated_at' => $horaVenta
            ]);

            // Actualizar totales de la venta
            DB::table('ventas')->where('id', $ventaId)->update([
                'subtotal' => $subtotalVenta,
                'impuesto_monto' => $impuestoMonto,
                'total' => $total,
                'total_pagado' => $total
            ]);

            $ventasCreadas++;
        }
    }
    
    echo "   ✅ {$ventasCreadas} ventas creadas\n";

    // 4. CREAR ALGUNOS EVENTOS DE ANALYTICS
    echo "4. 📊 Creando eventos de analytics...\n";
    
    $eventosAnalytics = [
        ['evento' => 'login', 'categoria' => 'auth', 'valor_texto' => 'successful_login'],
        ['evento' => 'dashboard_view', 'categoria' => 'navigation', 'valor_texto' => 'dashboard_accessed'],
        ['evento' => 'product_view', 'categoria' => 'inventory', 'valor_texto' => 'product_list_viewed'],
        ['evento' => 'sale_created', 'categoria' => 'sales', 'valor_numerico' => $total ?? 50],
        ['evento' => 'pos_accessed', 'categoria' => 'sales', 'valor_texto' => 'pos_opened']
    ];

    foreach ($eventosAnalytics as $evento) {
        DB::table('analytics_eventos')->insert([
            'empresa_id' => 1,
            'usuario_id' => 1,
            'sucursal_id' => 1,
            'evento' => $evento['evento'],
            'categoria' => $evento['categoria'],
            'entidad_tipo' => null,
            'entidad_id' => null,
            'datos_json' => json_encode(['source' => 'dashboard', 'browser' => 'chrome']),
            'metadatos_json' => json_encode(['ip' => '127.0.0.1', 'user_agent' => 'SmartKet/4.0']),
            'valor_numerico' => $evento['valor_numerico'] ?? null,
            'valor_texto' => $evento['valor_texto'] ?? null,
            'timestamp_evento' => Carbon::now(),
            'session_id' => 'session_' . uniqid(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
    
    echo "   ✅ " . count($eventosAnalytics) . " eventos de analytics creados\n";

    // 5. CREAR CAJA Y SESIÓN ACTIVA
    echo "5. 💼 Creando caja y sesión activa...\n";
    
    // Crear caja principal
    $cajaId = DB::table('cajas')->insertGetId([
        'empresa_id' => 1,
        'sucursal_id' => 1,
        'nombre' => 'Caja Principal',
        'codigo' => 'CAJA01',
        'tipo' => 'principal',
        'activa' => 1,
        'configuracion_json' => json_encode(['permite_vuelto' => true, 'requiere_cierre' => true]),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ]);

    // Crear sesión de caja del día
    $sesionId = DB::table('caja_sesiones')->insertGetId([
        'empresa_id' => 1,
        'caja_id' => $cajaId,
        'user_apertura_id' => 1,
        'user_cierre_id' => null,
        'codigo' => 'SES-' . Carbon::now()->format('Ymd') . '-001',
        'apertura_at' => Carbon::now()->setHour(8)->setMinute(0),
        'cierre_at' => null,
        'monto_inicial' => 200.00,
        'monto_ingresos' => 0,
        'monto_retiros' => 0,
        'monto_ventas_efectivo' => DB::table('ventas')
            ->join('venta_pagos', 'ventas.id', '=', 'venta_pagos.venta_id')
            ->join('metodos_pago', 'venta_pagos.metodo_pago_id', '=', 'metodos_pago.id')
            ->where('ventas.empresa_id', 1)
            ->where('metodos_pago.codigo', 'EFE')
            ->whereDate('ventas.fecha_venta', Carbon::today())
            ->sum('venta_pagos.monto'),
        'monto_declarado_cierre' => null,
        'diferencia' => null,
        'estado' => 'abierta',
        'observaciones' => 'Apertura automática del día',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ]);

    echo "   ✅ Caja y sesión creadas\n";

    DB::commit();
    
    echo "\n=== ✅ BASE DE DATOS LLENADA EXITOSAMENTE ===\n";
    echo "📊 Resumen:\n";
    echo "   - Productos con stock: " . DB::table('producto_stocks')->count() . "\n";
    echo "   - Métodos de pago: " . DB::table('metodos_pago')->where('empresa_id', 1)->count() . "\n";
    echo "   - Ventas creadas: " . DB::table('ventas')->where('empresa_id', 1)->count() . "\n";
    echo "   - Movimientos de inventario: " . DB::table('inventario_movimientos')->where('empresa_id', 1)->count() . "\n";
    echo "   - Eventos de analytics: " . DB::table('analytics_eventos')->where('empresa_id', 1)->count() . "\n";
    echo "   - Caja activa: Sí\n";
    
    // Estadísticas adicionales
    $ventasHoy = DB::table('ventas')->where('empresa_id', 1)->whereDate('fecha_venta', Carbon::today())->sum('total');
    $ventasMes = DB::table('ventas')->where('empresa_id', 1)->whereMonth('fecha_venta', Carbon::now()->month)->sum('total');
    $stockBajo = DB::table('productos')
        ->join('producto_stocks', 'productos.id', '=', 'producto_stocks.producto_id')
        ->where('productos.empresa_id', 1)
        ->whereRaw('producto_stocks.cantidad_actual <= productos.stock_minimo')
        ->count();
    
    echo "\n📈 KPIs actuales:\n";
    echo "   - Ventas hoy: S/ " . number_format($ventasHoy, 2) . "\n";
    echo "   - Ventas del mes: S/ " . number_format($ventasMes, 2) . "\n";
    echo "   - Productos con stock bajo: {$stockBajo}\n";
    echo "   - Clientes activos: " . DB::table('clientes')->where('empresa_id', 1)->count() . "\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
}

echo "\n🎯 ¡Listo para mejorar el dashboard con datos reales!\n";
