<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioMovimiento extends Model
{
    protected $table = 'inventario_movimientos';
    
    protected $fillable = [
        'empresa_id',
        'producto_id',
        'sucursal_id',
        'usuario_id',
        'tipo_movimiento',
        'referencia_tipo',
        'referencia_id',
        'cantidad',
        'costo_unitario',
        'stock_anterior',
        'stock_posterior',
        'observaciones',
        'fecha_movimiento'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario' => 'decimal:4',
        'stock_anterior' => 'decimal:2',
        'stock_posterior' => 'decimal:2',
        'fecha_movimiento' => 'datetime',
    ];

    // Constants
    const TIPO_ENTRADA = 'ENTRADA';
    const TIPO_SALIDA = 'SALIDA';
    const TIPO_AJUSTE = 'AJUSTE';
    const TIPO_TRANSFER_IN = 'TRANSFER_IN';
    const TIPO_TRANSFER_OUT = 'TRANSFER_OUT';

    const REFERENCIA_COMPRA = 'COMPRA';
    const REFERENCIA_VENTA = 'VENTA';
    const REFERENCIA_AJUSTE = 'AJUSTE';
    const REFERENCIA_TRANSFERENCIA = 'TRANSFERENCIA';
    const REFERENCIA_DEVOLUCION = 'DEVOLUCION';

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function esEntrada(): bool
    {
        return in_array($this->tipo_movimiento, [
            self::TIPO_ENTRADA,
            self::TIPO_TRANSFER_IN
        ]);
    }

    public function esSalida(): bool
    {
        return in_array($this->tipo_movimiento, [
            self::TIPO_SALIDA,
            self::TIPO_TRANSFER_OUT
        ]);
    }

    public function esAjuste(): bool
    {
        return $this->tipo_movimiento === self::TIPO_AJUSTE;
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

    public function scopeEntradas($query)
    {
        return $query->whereIn('tipo_movimiento', [self::TIPO_ENTRADA, self::TIPO_TRANSFER_IN]);
    }

    public function scopeSalidas($query)
    {
        return $query->whereIn('tipo_movimiento', [self::TIPO_SALIDA, self::TIPO_TRANSFER_OUT]);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
    }
}
