<?php

namespace App\Models;

use App\Services\InventarioService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Venta extends Model
{
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

    const TIPO_BOLETA = 'boleta';
    const TIPO_FACTURA = 'factura';
    const TIPO_NOTA_CREDITO = 'nota_credito';
    const TIPO_NOTA_DEBITO = 'nota_debito';
    const TIPO_TICKET = 'ticket';

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
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeForSucursal($query, int $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeByEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeByFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_venta', $fechaInicio);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_venta', today());
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

    // Métodos de negocio
    public function calcularTotales(): void
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
    }

    public function agregarDetalle(int $productoId, float $cantidad, float $precioUnitario, float $descuentoPorcentaje = 0): VentaDetalle
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
            'precio_costo' => $producto->precio_costo,
            'descuento_porcentaje' => $descuentoPorcentaje,
            'descuento_monto' => $descuentoMonto,
            'subtotal' => $subtotalConDescuento,
            'impuesto_monto' => $impuestoMonto,
            'total' => $total,
        ]);
    }

    public function agregarPago(int $metodoPagoId, float $monto, string $referencia = null): VentaPago
    {
        return $this->pagos()->create([
            'metodo_pago_id' => $metodoPagoId,
            'monto' => $monto,
            'referencia' => $referencia,
            'fecha_pago' => now(),
        ]);
    }

    public function actualizarTotalPagado(): void
    {
        $totalPagado = $this->pagos()->sum('monto');
        $this->update(['total_pagado' => $totalPagado]);
    }

    public function marcarComoPagada(): void
    {
        $this->update([
            'estado' => self::ESTADO_PAGADA,
            'total_pagado' => $this->total,
        ]);
    }

    public function anular(string $motivo): void
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
    }

    public static function generarNumeroVenta(int $empresaId, int $sucursalId): string
    {
        $ultimo = self::forEmpresa($empresaId)
            ->forSucursal($sucursalId)
            ->whereDate('fecha_venta', today())
            ->max('numero_venta');

        if ($ultimo) {
            $numero = intval(substr($ultimo, -6)) + 1;
        } else {
            $numero = 1;
        }

        return now()->format('Ymd') . '-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
