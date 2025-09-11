<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\MetodoPago;
use App\Models\Inventory\Producto;
use App\Models\Sales\Venta;
use App\Models\Sales\VentaDetalle;
use App\Models\Sales\VentaPago;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaService
{
    protected InventarioService $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
    }

    /**
     * Crear nueva venta
     */
    public function crearVenta(array $datos): Venta
    {
        return DB::transaction(function () use ($datos) {
            // Validar cliente si existe
            if (isset($datos['cliente_id'])) {
                $cliente = Cliente::findOrFail($datos['cliente_id']);
                
                // Verificar límite de crédito si es venta a crédito
                if (isset($datos['es_credito']) && $datos['es_credito']) {
                    $this->validarLimiteCredito($cliente, $datos['total'] ?? 0);
                }
            }

            // Crear venta
            $venta = Venta::create([
                'empresa_id' => $datos['empresa_id'],
                'sucursal_id' => $datos['sucursal_id'],
                'usuario_id' => $datos['usuario_id'],
                'cliente_id' => $datos['cliente_id'] ?? null,
                'numero_venta' => Venta::generarNumeroVenta($datos['empresa_id'], $datos['sucursal_id']),
                'tipo_comprobante' => $datos['tipo_comprobante'] ?? Venta::TIPO_TICKET,
                'estado' => Venta::ESTADO_PENDIENTE,
                'fecha_venta' => now(),
                'descuento_porcentaje' => $datos['descuento_porcentaje'] ?? 0,
                'impuesto_porcentaje' => $datos['impuesto_porcentaje'] ?? 0,
                'observaciones' => $datos['observaciones'] ?? null,
                'requiere_facturacion' => $datos['requiere_facturacion'] ?? false,
            ]);

            Log::info("Venta creada: {$venta->numero_venta}", ['venta_id' => $venta->id]);

            return $venta;
        });
    }

    /**
     * Agregar productos a la venta
     */
    public function agregarProductos(Venta $venta, array $productos): void
    {
        DB::transaction(function () use ($venta, $productos) {
            foreach ($productos as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                
                // Validar stock disponible
                if ($producto->maneja_stock) {
                    $stockDisponible = $this->inventarioService->obtenerStock(
                        $producto->id, 
                        $venta->sucursal_id
                    );
                    
                    if ($stockDisponible < $item['cantidad']) {
                        throw new \Exception(
                            "Stock insuficiente para {$producto->nombre}. Disponible: {$stockDisponible}"
                        );
                    }
                }

                // Crear detalle de venta
                $venta->agregarDetalle(
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio_unitario'] ?? $producto->precio_venta,
                    $item['descuento_porcentaje'] ?? 0
                );

                // Registrar movimiento de inventario
                if ($producto->maneja_stock) {
                    $this->inventarioService->registrarMovimiento(
                        $producto->id,
                        $venta->sucursal_id,
                        'salida',
                        $item['cantidad'],
                        $producto->precio_costo,
                        "Venta #{$venta->numero_venta}"
                    );
                }
            }

            // Recalcular totales
            $venta->calcularTotales();
        });
    }

    /**
     * Procesar pagos de la venta
     */
    public function procesarPagos(Venta $venta, array $pagos): bool
    {
        return DB::transaction(function () use ($venta, $pagos) {
            $totalPagos = 0;

            foreach ($pagos as $pago) {
                $metodoPago = MetodoPago::findOrFail($pago['metodo_pago_id']);
                
                // Validar método de pago activo
                if (!$metodoPago->activo) {
                    throw new \Exception("El método de pago {$metodoPago->nombre} no está activo");
                }

                // Crear el pago
                $ventaPago = $venta->agregarPago(
                    $pago['metodo_pago_id'],
                    $pago['monto'],
                    $pago['referencia'] ?? null
                );

                // Calcular comisión
                $ventaPago->calcularComision();

                // Procesar el pago según su tipo
                if (!$ventaPago->procesar()) {
                    throw new \Exception("Error al procesar pago con {$metodoPago->nombre}");
                }

                $totalPagos += $pago['monto'];
            }

            // Actualizar total pagado
            $venta->actualizarTotalPagado();

            // Calcular vuelto si es efectivo y hay exceso
            if ($totalPagos > $venta->total) {
                $venta->update(['vuelto' => $totalPagos - $venta->total]);
            }

            // Marcar como pagada si está completamente pagada
            if ($venta->esta_pagada) {
                $venta->marcarComoPagada();
            }

            return true;
        });
    }

    /**
     * Completar venta (agregar productos y procesar pagos)
     */
    public function completarVenta(array $datos): Venta
    {
        return DB::transaction(function () use ($datos) {
            // Crear venta
            $venta = $this->crearVenta($datos);

            // Agregar productos
            if (!empty($datos['productos'])) {
                $this->agregarProductos($venta, $datos['productos']);
            }

            // Procesar pagos
            if (!empty($datos['pagos'])) {
                $this->procesarPagos($venta, $datos['pagos']);
            }

            // Actualizar información del cliente si es venta a crédito
            if ($venta->cliente_id && $venta->es_a_credito) {
                $this->actualizarEstadoCliente($venta->cliente);
            }

            Log::info("Venta completada: {$venta->numero_venta}", [
                'venta_id' => $venta->id,
                'total' => $venta->total,
                'estado' => $venta->estado
            ]);

            return $venta->fresh(['detalles', 'pagos', 'cliente']);
        });
    }

    /**
     * Anular venta
     */
    public function anularVenta(Venta $venta, string $motivo): bool
    {
        return DB::transaction(function () use ($venta, $motivo) {
            if ($venta->estado === Venta::ESTADO_ANULADA) {
                throw new \Exception('La venta ya está anulada');
            }

            // Anular la venta (esto revierte el stock automáticamente)
            $venta->anular($motivo);

            // Anular pagos
            foreach ($venta->pagos as $pago) {
                $pago->anular('Venta anulada');
            }

            // Actualizar estado del cliente si es necesario
            if ($venta->cliente_id) {
                $this->actualizarEstadoCliente($venta->cliente);
            }

            Log::info("Venta anulada: {$venta->numero_venta}", [
                'venta_id' => $venta->id,
                'motivo' => $motivo
            ]);

            return true;
        });
    }

    /**
     * Obtener ventas del día
     */
    public function ventasDelDia(int $empresaId, int $sucursalId = null): Collection
    {
        $query = Venta::forEmpresa($empresaId)
            ->hoy()
            ->with(['cliente', 'detalles.producto', 'pagos.metodoPago'])
            ->orderBy('created_at', 'desc');

        if ($sucursalId) {
            $query->forSucursal($sucursalId);
        }

        return $query->get();
    }

    /**
     * Obtener resumen de ventas
     */
    public function resumenVentas(int $empresaId, string $fechaInicio, string $fechaFin = null, int $sucursalId = null): array
    {
        $query = Venta::forEmpresa($empresaId)
            ->byFecha($fechaInicio, $fechaFin)
            ->where('estado', '!=', Venta::ESTADO_ANULADA);

        if ($sucursalId) {
            $query->forSucursal($sucursalId);
        }

        $ventas = $query->get();

        return [
            'total_ventas' => $ventas->count(),
            'monto_total' => $ventas->sum('total'),
            'monto_pagado' => $ventas->sum('total_pagado'),
            'ventas_pendientes' => $ventas->where('estado', Venta::ESTADO_PENDIENTE)->count(),
            'ventas_pagadas' => $ventas->where('estado', Venta::ESTADO_PAGADA)->count(),
            'promedio_venta' => $ventas->count() > 0 ? $ventas->avg('total') : 0,
            'venta_mayor' => $ventas->max('total') ?? 0,
            'venta_menor' => $ventas->min('total') ?? 0,
        ];
    }

    /**
     * Obtener productos más vendidos
     */
    public function productosMasVendidos(int $empresaId, int $limite = 10, string $fechaInicio = null, string $fechaFin = null): Collection
    {
        $query = VentaDetalle::select('producto_id')
            ->selectRaw('SUM(cantidad) as total_vendido')
            ->selectRaw('SUM(total) as total_facturado')
            ->selectRaw('COUNT(*) as veces_vendido')
            ->with('producto')
            ->whereHas('venta', function ($q) use ($empresaId, $fechaInicio, $fechaFin) {
                $q->forEmpresa($empresaId)
                  ->where('estado', '!=', Venta::ESTADO_ANULADA);
                
                if ($fechaInicio) {
                    $q->byFecha($fechaInicio, $fechaFin);
                }
            })
            ->groupBy('producto_id')
            ->orderBy('total_vendido', 'desc')
            ->limit($limite);

        return $query->get();
    }

    /**
     * Validar límite de crédito del cliente
     */
    private function validarLimiteCredito(Cliente $cliente, float $montoNuevo): void
    {
        $saldoPendiente = $cliente->saldo_pendiente + $montoNuevo;
        
        if ($saldoPendiente > $cliente->limite_credito) {
            throw new \Exception(
                "El cliente excede su límite de crédito. Límite: {$cliente->limite_credito}, Saldo pendiente: {$saldoPendiente}"
            );
        }
    }

    /**
     * Actualizar estado crediticio del cliente
     */
    private function actualizarEstadoCliente(Cliente $cliente): void
    {
        $cliente->actualizarSaldoPendiente();
        $cliente->actualizarEstadoCredito();
    }
}

