<?php

namespace App\Models\Sales;

use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use App\Models\Core\User;
use App\Models\CRM\Cliente;
use App\Models\Inventory\Producto;
use App\Models\Core\MetodoPago;
use App\Services\InventarioService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\EmpresaScope;

class Venta extends Model
{
    use SoftDeletes;
    
    protected static function booted(): void
    {
        static::addGlobalScope(new EmpresaScope);
    }

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'usuario_id',
        'cliente_id',
        'numero_venta',
        'tipo_comprobante',
        'serie_comprobante',
        'numero_comprobante',
        'estado',
        'fecha_venta',
        'subtotal',
        'descuento_porcentaje',
        'descuento_monto',
        'impuesto_porcentaje',
        'impuesto_monto',
        'total',
        'total_pagado',
        'vuelto',
        'observaciones',
        'requiere_facturacion',
        'fecha_anulacion',
        'motivo_anulacion',
        'extras_json',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'fecha_anulacion' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'impuesto_porcentaje' => 'decimal:2',
        'impuesto_monto' => 'decimal:2',
        'total' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'vuelto' => 'decimal:2',
        'requiere_facturacion' => 'boolean',
        'extras_json' => 'array',
    ];

    // Constantes
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_PAGADA = 'pagada';
    const ESTADO_ANULADA = 'anulada';
    const ESTADO_DEVUELTA = 'devuelta';

    const ESTADOS = [
        self::ESTADO_PENDIENTE => 'Pendiente',
        self::ESTADO_PAGADA => 'Pagada',
        self::ESTADO_ANULADA => 'Anulada',
        self::ESTADO_DEVUELTA => 'Devuelta',
    ];

    const TIPO_BOLETA = 'boleta';
    const TIPO_FACTURA = 'factura';
    const TIPO_NOTA_CREDITO = 'nota_credito';
    const TIPO_NOTA_DEBITO = 'nota_debito';
    const TIPO_TICKET = 'ticket';

    const TIPOS_COMPROBANTE = [
        self::TIPO_TICKET => 'Ticket',
        self::TIPO_BOLETA => 'Boleta',
        self::TIPO_FACTURA => 'Factura',
        self::TIPO_NOTA_CREDITO => 'Nota de Crédito',
        self::TIPO_NOTA_DEBITO => 'Nota de Débito',
    ];

    // Relaciones
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(VentaPago::class);
    }

    // Scopes
    public function scopeForSucursal(Builder $query, int $sucursalId): Builder
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeByEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }

    public function scopeByFecha(Builder $query, $fechaInicio, $fechaFin = null): Builder
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_venta', $fechaInicio);
    }

    public function scopeHoy(Builder $query): Builder
    {
        return $query->whereDate('fecha_venta', today());
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopePagadas(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PAGADA);
    }

    public function scopeAnuladas(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_ANULADA);
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->whereNotIn('estado', [self::ESTADO_ANULADA]);
    }

    public function scopeConCliente(Builder $query): Builder
    {
        return $query->whereNotNull('cliente_id');
    }

    public function scopeAnónimas(Builder $query): Builder
    {
        return $query->whereNull('cliente_id');
    }

    // Accessors
    public function getSaldoPendienteAttribute(): float
    {
        return $this->total - $this->total_pagado;
    }

    public function getEstaPagadaAttribute(): bool
    {
        return $this->saldo_pendiente <= 0.01; // Tolerancia de 1 centavo
    }

    public function getEsACreditoAttribute(): bool
    {
        return $this->saldo_pendiente > 0.01 && $this->estado !== self::ESTADO_ANULADA;
    }

    public function getComprobanteCompletoAttribute(): string
    {
        if ($this->serie_comprobante && $this->numero_comprobante) {
            return "{$this->serie_comprobante}-{$this->numero_comprobante}";
        }
        return $this->numero_venta;
    }

    public function getEstadoFormateadoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getTipoComprobanteFormateadoAttribute(): string
    {
        return self::TIPOS_COMPROBANTE[$this->tipo_comprobante] ?? $this->tipo_comprobante;
    }

    public function getCantidadItemsAttribute(): int
    {
        return $this->detalles()->count();
    }

    public function getCantidadTotalProductosAttribute(): float
    {
        return $this->detalles()->sum('cantidad');
    }

    public function getMargenTotalAttribute(): float
    {
        return $this->detalles()->sum(DB::raw('(precio_unitario - precio_costo) * cantidad'));
    }

    public function getEsRentableAttribute(): bool
    {
        return $this->margen_total > 0;
    }

    // Métodos de negocio
    public function calcularTotales(): self
    {
        $this->load('detalles');
        
        $subtotal = $this->detalles->sum('subtotal');
        $descuentoMonto = ($subtotal * $this->descuento_porcentaje) / 100;
        $baseImponible = $subtotal - $descuentoMonto;
        $impuestoMonto = ($baseImponible * $this->impuesto_porcentaje) / 100;
        $total = $baseImponible + $impuestoMonto;

        $this->update([
            'subtotal' => $subtotal,
            'descuento_monto' => $descuentoMonto,
            'impuesto_monto' => $impuestoMonto,
            'total' => $total,
        ]);

        return $this;
    }

    public function agregarDetalle(int $productoId, float $cantidad, float $precioUnitario, float $descuentoPorcentaje = 0, string $observaciones = null): VentaDetalle
    {
        $producto = Producto::findOrFail($productoId);
        
        $subtotal = $cantidad * $precioUnitario;
        $descuentoMonto = ($subtotal * $descuentoPorcentaje) / 100;
        $subtotalConDescuento = $subtotal - $descuentoMonto;
        $impuestoMonto = ($subtotalConDescuento * $this->impuesto_porcentaje) / 100;
        $total = $subtotalConDescuento + $impuestoMonto;

        return $this->detalles()->create([
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'precio_costo' => $producto->precio_costo ?? 0,
            'descuento_porcentaje' => $descuentoPorcentaje,
            'descuento_monto' => $descuentoMonto,
            'subtotal' => $subtotalConDescuento,
            'impuesto_monto' => $impuestoMonto,
            'total' => $total,
            'observaciones' => $observaciones,
        ]);
    }

    public function agregarPago(int $metodoPagoId, float $monto, string $referencia = null, string $observaciones = null): VentaPago
    {
        return $this->pagos()->create([
            'metodo_pago_id' => $metodoPagoId,
            'monto' => $monto,
            'referencia' => $referencia,
            'observaciones' => $observaciones,
            'fecha_pago' => now(),
        ]);
    }

    public function actualizarTotalPagado(): self
    {
        $totalPagado = $this->pagos()->sum('monto');
        $this->update(['total_pagado' => $totalPagado]);
        
        // Auto-marcar como pagada si se completó el pago
        if ($this->esta_pagada && $this->estado === self::ESTADO_PENDIENTE) {
            $this->marcarComoPagada();
        }

        return $this;
    }

    public function marcarComoPagada(): self
    {
        $this->update([
            'estado' => self::ESTADO_PAGADA,
            'total_pagado' => $this->total,
        ]);

        return $this;
    }

    public function anular(string $motivo): self
    {
        DB::transaction(function () use ($motivo) {
            // Revertir stock si es necesario
            foreach ($this->detalles as $detalle) {
                if ($detalle->producto->maneja_stock) {
                    app(InventarioService::class)->registrarMovimiento(
                        $detalle->producto_id,
                        $this->sucursal_id,
                        'entrada',
                        $detalle->cantidad,
                        $detalle->precio_costo,
                        "Anulación de venta #{$this->numero_venta}"
                    );
                }
            }

            $this->update([
                'estado' => self::ESTADO_ANULADA,
                'fecha_anulacion' => now(),
                'motivo_anulacion' => $motivo,
            ]);
        });

        return $this;
    }

    public function procesar(): bool
    {
        try {
            DB::transaction(function () {
                // Validar stock y procesar inventario
                foreach ($this->detalles as $detalle) {
                    if ($detalle->producto->maneja_stock) {
                        app(InventarioService::class)->registrarMovimiento(
                            $detalle->producto_id,
                            $this->sucursal_id,
                            'salida',
                            $detalle->cantidad,
                            $detalle->precio_costo,
                            "Venta #{$this->numero_venta}"
                        );
                    }
                }

                // Procesar pagos
                foreach ($this->pagos as $pago) {
                    $pago->procesar();
                }

                // Actualizar estado si está completamente pagada
                if ($this->esta_pagada) {
                    $this->marcarComoPagada();
                }
            });

            return true;
        } catch (\Exception $e) {
            $this->registrarError($e->getMessage());
            return false;
        }
    }

    public function duplicar(): self
    {
        $nuevaVenta = $this->replicate([
            'numero_venta',
            'fecha_venta',
            'estado',
            'total_pagado',
            'vuelto',
            'fecha_anulacion',
            'motivo_anulacion'
        ]);

        $nuevaVenta->numero_venta = self::generarNumeroVenta($this->empresa_id, $this->sucursal_id);
        $nuevaVenta->fecha_venta = now();
        $nuevaVenta->estado = self::ESTADO_PENDIENTE;
        $nuevaVenta->save();

        // Duplicar detalles
        foreach ($this->detalles as $detalle) {
            $nuevoDetalle = $detalle->replicate();
            $nuevoDetalle->venta_id = $nuevaVenta->id;
            $nuevoDetalle->save();
        }

        return $nuevaVenta;
    }

    private function registrarError(string $mensaje): void
    {
        $extras = $this->extras_json ?? [];
        $extras['errores'] = $extras['errores'] ?? [];
        $extras['errores'][] = [
            'mensaje' => $mensaje,
            'fecha' => now()->toISOString(),
        ];

        $this->update(['extras_json' => $extras]);
    }

    public static function generarNumeroVenta(int $empresaId, int $sucursalId): string
    {
        $ultimo = self::forEmpresa($empresaId)
            ->forSucursal($sucursalId)
            ->whereDate('fecha_venta', today())
            ->max('numero_venta');

        if ($ultimo) {
            // Extraer el número secuencial del formato YYYYMMDD-NNNNNN
            $parts = explode('-', $ultimo);
            $numero = count($parts) > 1 ? intval($parts[1]) + 1 : 1;
        } else {
            $numero = 1;
        }

        return now()->format('Ymd') . '-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    // Métodos estáticos de utilidad
    public static function ventasDelDia(int $empresaId, int $sucursalId = null): Builder
    {
        $query = self::forEmpresa($empresaId)->hoy()->activas();
        
        if ($sucursalId) {
            $query->forSucursal($sucursalId);
        }

        return $query;
    }

    public static function ventasDelMes(int $empresaId, int $sucursalId = null): Builder
    {
        $query = self::forEmpresa($empresaId)
            ->whereMonth('fecha_venta', now()->month)
            ->whereYear('fecha_venta', now()->year)
            ->activas();
        
        if ($sucursalId) {
            $query->forSucursal($sucursalId);
        }

        return $query;
    }

    public static function topProductos(int $empresaId, int $limit = 10, int $sucursalId = null): array
    {
        $query = VentaDetalle::query()
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.estado', '!=', self::ESTADO_ANULADA);

        if ($sucursalId) {
            $query->where('ventas.sucursal_id', $sucursalId);
        }

        return $query
            ->select(
                'productos.nombre',
                'productos.codigo',
                DB::raw('SUM(venta_detalles.cantidad) as cantidad_vendida'),
                DB::raw('SUM(venta_detalles.total) as total_vendido'),
                DB::raw('COUNT(DISTINCT ventas.id) as numero_ventas')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
            ->orderByDesc('cantidad_vendida')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
