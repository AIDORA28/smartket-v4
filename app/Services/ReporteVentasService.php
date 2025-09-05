<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VentaPago;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteVentasService
{
    /**
     * Reporte de ventas por período
     */
    public function ventasPorPeriodo(int $empresaId, string $fechaInicio, string $fechaFin, int $sucursalId = null): array
    {
        $query = Venta::forEmpresa($empresaId)
            ->byFecha($fechaInicio, $fechaFin)
            ->where('estado', '!=', Venta::ESTADO_ANULADA);

        if ($sucursalId) {
            $query->forSucursal($sucursalId);
        }

        $ventas = $query->with(['cliente', 'detalles.producto', 'pagos.metodoPago'])->get();

        return [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'resumen' => [
                'total_ventas' => $ventas->count(),
                'monto_bruto' => $ventas->sum('subtotal'),
                'descuentos' => $ventas->sum('descuento_monto'),
                'impuestos' => $ventas->sum('impuesto_monto'),
                'monto_neto' => $ventas->sum('total'),
                'monto_pagado' => $ventas->sum('total_pagado'),
                'saldo_pendiente' => $ventas->sum(function ($venta) {
                    return $venta->saldo_pendiente;
                }),
                'promedio_venta' => $ventas->count() > 0 ? $ventas->avg('total') : 0,
                'venta_mayor' => $ventas->max('total') ?? 0,
                'venta_menor' => $ventas->min('total') ?? 0,
            ],
            'por_estado' => [
                'pagadas' => $ventas->where('estado', Venta::ESTADO_PAGADA)->count(),
                'pendientes' => $ventas->where('estado', Venta::ESTADO_PENDIENTE)->count(),
                'anuladas' => $ventas->where('estado', Venta::ESTADO_ANULADA)->count(),
            ],
            'ventas' => $ventas->map(function ($venta) {
                return [
                    'id' => $venta->id,
                    'numero_venta' => $venta->numero_venta,
                    'fecha_venta' => $venta->fecha_venta->format('Y-m-d H:i:s'),
                    'cliente' => $venta->cliente?->nombre ?? 'Cliente general',
                    'total' => $venta->total,
                    'pagado' => $venta->total_pagado,
                    'saldo' => $venta->saldo_pendiente,
                    'estado' => $venta->estado,
                ];
            })
        ];
    }

    /**
     * Reporte de productos más vendidos
     */
    public function productosMasVendidos(int $empresaId, string $fechaInicio, string $fechaFin, int $limite = 20): array
    {
        $productos = VentaDetalle::select('producto_id')
            ->selectRaw('SUM(cantidad) as total_vendido')
            ->selectRaw('SUM(total) as total_facturado')
            ->selectRaw('SUM(cantidad * precio_costo) as costo_total')
            ->selectRaw('COUNT(DISTINCT venta_id) as ventas_realizadas')
            ->selectRaw('AVG(precio_unitario) as precio_promedio')
            ->with('producto.categoria')
            ->whereHas('venta', function ($q) use ($empresaId, $fechaInicio, $fechaFin) {
                $q->forEmpresa($empresaId)
                  ->byFecha($fechaInicio, $fechaFin)
                  ->where('estado', '!=', Venta::ESTADO_ANULADA);
            })
            ->groupBy('producto_id')
            ->orderBy('total_vendido', 'desc')
            ->limit($limite)
            ->get();

        return [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'productos' => $productos->map(function ($detalle) {
                $margen = $detalle->total_facturado - $detalle->costo_total;
                $margenPorcentaje = $detalle->costo_total > 0 ? 
                    ($margen / $detalle->costo_total) * 100 : 0;

                return [
                    'producto_id' => $detalle->producto_id,
                    'nombre' => $detalle->producto->nombre,
                    'categoria' => $detalle->producto->categoria->nombre,
                    'cantidad_vendida' => $detalle->total_vendido,
                    'ventas_realizadas' => $detalle->ventas_realizadas,
                    'precio_promedio' => round($detalle->precio_promedio, 2),
                    'total_facturado' => $detalle->total_facturado,
                    'costo_total' => $detalle->costo_total,
                    'margen' => $margen,
                    'margen_porcentaje' => round($margenPorcentaje, 2),
                ];
            })
        ];
    }

    /**
     * Reporte de ventas por vendedor
     */
    public function ventasPorVendedor(int $empresaId, string $fechaInicio, string $fechaFin): array
    {
        $ventasPorVendedor = Venta::select('usuario_id')
            ->selectRaw('COUNT(*) as total_ventas')
            ->selectRaw('SUM(total) as monto_total')
            ->selectRaw('AVG(total) as promedio_venta')
            ->selectRaw('MAX(total) as venta_mayor')
            ->selectRaw('MIN(total) as venta_menor')
            ->with('usuario')
            ->forEmpresa($empresaId)
            ->byFecha($fechaInicio, $fechaFin)
            ->where('estado', '!=', Venta::ESTADO_ANULADA)
            ->groupBy('usuario_id')
            ->orderBy('monto_total', 'desc')
            ->get();

        return [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'vendedores' => $ventasPorVendedor->map(function ($venta) {
                return [
                    'vendedor_id' => $venta->usuario_id,
                    'nombre' => $venta->usuario->name,
                    'email' => $venta->usuario->email,
                    'total_ventas' => $venta->total_ventas,
                    'monto_total' => $venta->monto_total,
                    'promedio_venta' => round($venta->promedio_venta, 2),
                    'venta_mayor' => $venta->venta_mayor,
                    'venta_menor' => $venta->venta_menor,
                ];
            })
        ];
    }

    /**
     * Reporte de clientes frecuentes
     */
    public function clientesFrecuentes(int $empresaId, string $fechaInicio, string $fechaFin, int $limite = 20): array
    {
        $clientes = Cliente::select('clientes.*')
            ->selectRaw('COUNT(ventas.id) as total_compras')
            ->selectRaw('SUM(ventas.total) as monto_total')
            ->selectRaw('AVG(ventas.total) as promedio_compra')
            ->selectRaw('MAX(ventas.fecha_venta) as ultima_compra')
            ->join('ventas', 'clientes.id', '=', 'ventas.cliente_id')
            ->where('clientes.empresa_id', $empresaId)
            ->whereBetween('ventas.fecha_venta', [$fechaInicio, $fechaFin])
            ->where('ventas.estado', '!=', Venta::ESTADO_ANULADA)
            ->groupBy('clientes.id')
            ->orderBy('monto_total', 'desc')
            ->limit($limite)
            ->get();

        return [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'clientes' => $clientes->map(function ($cliente) {
                return [
                    'cliente_id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'documento' => $cliente->documento,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'total_compras' => $cliente->total_compras,
                    'monto_total' => $cliente->monto_total,
                    'promedio_compra' => round($cliente->promedio_compra, 2),
                    'ultima_compra' => $cliente->ultima_compra,
                    'saldo_pendiente' => $cliente->saldo_pendiente,
                ];
            })
        ];
    }

    /**
     * Reporte de métodos de pago
     */
    public function metodosPago(int $empresaId, string $fechaInicio, string $fechaFin): array
    {
        $pagosPorMetodo = VentaPago::select('metodo_pago_id')
            ->selectRaw('COUNT(*) as total_transacciones')
            ->selectRaw('SUM(monto) as monto_total')
            ->selectRaw('SUM(comision_monto) as comision_total')
            ->selectRaw('SUM(monto_neto) as monto_neto')
            ->selectRaw('AVG(monto) as promedio_transaccion')
            ->with('metodoPago')
            ->whereHas('venta', function ($q) use ($empresaId, $fechaInicio, $fechaFin) {
                $q->forEmpresa($empresaId)
                  ->byFecha($fechaInicio, $fechaFin)
                  ->where('estado', '!=', Venta::ESTADO_ANULADA);
            })
            ->where('estado', VentaPago::ESTADO_CONFIRMADO)
            ->groupBy('metodo_pago_id')
            ->orderBy('monto_total', 'desc')
            ->get();

        $totalGeneral = $pagosPorMetodo->sum('monto_total');

        return [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'resumen' => [
                'total_transacciones' => $pagosPorMetodo->sum('total_transacciones'),
                'monto_total' => $totalGeneral,
                'comision_total' => $pagosPorMetodo->sum('comision_total'),
                'monto_neto' => $pagosPorMetodo->sum('monto_neto'),
            ],
            'metodos' => $pagosPorMetodo->map(function ($pago) use ($totalGeneral) {
                $porcentaje = $totalGeneral > 0 ? 
                    ($pago->monto_total / $totalGeneral) * 100 : 0;

                return [
                    'metodo_id' => $pago->metodo_pago_id,
                    'nombre' => $pago->metodoPago->nombre,
                    'tipo' => $pago->metodoPago->tipo,
                    'total_transacciones' => $pago->total_transacciones,
                    'monto_total' => $pago->monto_total,
                    'comision_total' => $pago->comision_total,
                    'monto_neto' => $pago->monto_neto,
                    'promedio_transaccion' => round($pago->promedio_transaccion, 2),
                    'porcentaje_participacion' => round($porcentaje, 2),
                ];
            })
        ];
    }

    /**
     * Reporte de ventas por horas del día
     */
    public function ventasPorHoras(int $empresaId, string $fecha, int $sucursalId = null): array
    {
        $query = Venta::forEmpresa($empresaId)
            ->whereDate('fecha_venta', $fecha)
            ->where('estado', '!=', Venta::ESTADO_ANULADA);

        if ($sucursalId) {
            $query->forSucursal($sucursalId);
        }

        $ventas = $query->get();

        // Agrupar por hora
        $ventasPorHora = [];
        for ($hora = 0; $hora < 24; $hora++) {
            $ventasPorHora[$hora] = [
                'hora' => sprintf('%02d:00', $hora),
                'cantidad' => 0,
                'monto' => 0
            ];
        }

        foreach ($ventas as $venta) {
            $hora = $venta->fecha_venta->hour;
            $ventasPorHora[$hora]['cantidad']++;
            $ventasPorHora[$hora]['monto'] += $venta->total;
        }

        return [
            'fecha_venta' => $fecha,
            'resumen' => [
                'total_ventas' => $ventas->count(),
                'monto_total' => $ventas->sum('total'),
                'hora_mayor_actividad' => collect($ventasPorHora)
                    ->sortByDesc('cantidad')
                    ->first()['hora'] ?? '00:00',
            ],
            'por_hora' => array_values($ventasPorHora)
        ];
    }

    /**
     * Dashboard resumen ejecutivo
     */
    public function dashboardEjecutivo(int $empresaId, int $sucursalId = null): array
    {
        $hoy = Carbon::today();
        $ayer = Carbon::yesterday();
        $semanaActual = Carbon::now()->startOfWeek();
        $mesActual = Carbon::now()->startOfMonth();

        // Ventas de hoy
        $ventasHoy = $this->obtenerVentasResumen($empresaId, $hoy, $hoy, $sucursalId);
        $ventasAyer = $this->obtenerVentasResumen($empresaId, $ayer, $ayer, $sucursalId);
        $ventasSemana = $this->obtenerVentasResumen($empresaId, $semanaActual, $hoy, $sucursalId);
        $ventasMes = $this->obtenerVentasResumen($empresaId, $mesActual, $hoy, $sucursalId);

        return [
            'hoy' => $ventasHoy,
            'ayer' => $ventasAyer,
            'semana_actual' => $ventasSemana,
            'mes_actual' => $ventasMes,
            'comparacion' => [
                'vs_ayer' => [
                    'ventas' => $this->calcularPorcentajeCambio($ventasHoy['cantidad'], $ventasAyer['cantidad']),
                    'monto' => $this->calcularPorcentajeCambio($ventasHoy['monto'], $ventasAyer['monto']),
                ]
            ]
        ];
    }

    /**
     * Obtener resumen de ventas para un período
     */
    private function obtenerVentasResumen(int $empresaId, Carbon $inicio, Carbon $fin, int $sucursalId = null): array
    {
        $query = Venta::forEmpresa($empresaId)
            ->whereBetween('fecha_venta', [$inicio, $fin])
            ->where('estado', '!=', Venta::ESTADO_ANULADA);

        if ($sucursalId) {
            $query->forSucursal($sucursalId);
        }

        $ventas = $query->get();

        return [
            'cantidad' => $ventas->count(),
            'monto' => $ventas->sum('total'),
            'promedio' => $ventas->count() > 0 ? $ventas->avg('total') : 0,
        ];
    }

    /**
     * Calcular porcentaje de cambio
     */
    private function calcularPorcentajeCambio(float $actual, float $anterior): float
    {
        if ($anterior == 0) {
            return $actual > 0 ? 100 : 0;
        }

        return (($actual - $anterior) / $anterior) * 100;
    }
}
