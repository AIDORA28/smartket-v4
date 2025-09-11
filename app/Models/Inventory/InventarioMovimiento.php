<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\EmpresaScope;
use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use App\Models\Core\User;
use Illuminate\Support\Facades\Auth;

class InventarioMovimiento extends Model
{
    use HasFactory, SoftDeletes;

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

    // ========================= CONSTANTES =========================
    
    const TIPO_ENTRADA = 'ENTRADA';
    const TIPO_SALIDA = 'SALIDA';
    const TIPO_AJUSTE = 'AJUSTE';
    const TIPO_TRANSFER_IN = 'TRANSFER_IN';
    const TIPO_TRANSFER_OUT = 'TRANSFER_OUT';

    const TIPOS_MOVIMIENTO = [
        self::TIPO_ENTRADA => 'Entrada',
        self::TIPO_SALIDA => 'Salida',
        self::TIPO_AJUSTE => 'Ajuste',
        self::TIPO_TRANSFER_IN => 'Transferencia Entrada',
        self::TIPO_TRANSFER_OUT => 'Transferencia Salida',
    ];

    const REFERENCIA_COMPRA = 'COMPRA';
    const REFERENCIA_VENTA = 'VENTA';
    const REFERENCIA_AJUSTE = 'AJUSTE';
    const REFERENCIA_TRANSFERENCIA = 'TRANSFERENCIA';
    const REFERENCIA_DEVOLUCION = 'DEVOLUCION';

    /**
     * Boot del modelo - Auto-scope por empresa
     */
    protected static function booted()
    {
        static::addGlobalScope(new EmpresaScope);
    }

    // ========================= RELACIONES =========================

    /**
     * Empresa del movimiento
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Producto del movimiento
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Sucursal del movimiento
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Usuario que registra el movimiento
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ========================= SCOPES =========================

    /**
     * Scope: Por sucursal
     */
    public function scopeSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    /**
     * Scope: Por producto
     */
    public function scopeProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope: Movimientos de entrada
     */
    public function scopeEntradas($query)
    {
        return $query->whereIn('tipo_movimiento', [self::TIPO_ENTRADA, self::TIPO_TRANSFER_IN]);
    }

    /**
     * Scope: Movimientos de salida
     */
    public function scopeSalidas($query)
    {
        return $query->whereIn('tipo_movimiento', [self::TIPO_SALIDA, self::TIPO_TRANSFER_OUT]);
    }

    /**
     * Scope: Entre fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope: Por tipo de movimiento
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    /**
     * Scope: Ordenado por fecha (más recientes primero)
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('fecha_movimiento', 'desc');
    }

    // ========================= MÉTODOS DE VERIFICACIÓN =========================

    /**
     * Verificar si es movimiento de entrada
     */
    public function esEntrada(): bool
    {
        return in_array($this->tipo_movimiento, [self::TIPO_ENTRADA, self::TIPO_TRANSFER_IN]);
    }

    /**
     * Verificar si es movimiento de salida
     */
    public function esSalida(): bool
    {
        return in_array($this->tipo_movimiento, [self::TIPO_SALIDA, self::TIPO_TRANSFER_OUT]);
    }

    /**
     * Verificar si es ajuste
     */
    public function esAjuste(): bool
    {
        return $this->tipo_movimiento === self::TIPO_AJUSTE;
    }

    // ========================= MÉTODOS ESTÁTICOS =========================

    /**
     * Crear movimiento de entrada
     */
    public static function crearEntrada(array $datos): self
    {
        return static::create([
            'empresa_id' => Auth::user()->empresa_actual_id,
            'producto_id' => $datos['producto_id'],
            'sucursal_id' => $datos['sucursal_id'],
            'usuario_id' => Auth::id(),
            'tipo_movimiento' => self::TIPO_ENTRADA,
            'referencia_tipo' => $datos['referencia_tipo'] ?? self::REFERENCIA_AJUSTE,
            'referencia_id' => $datos['referencia_id'] ?? null,
            'cantidad' => $datos['cantidad'],
            'costo_unitario' => $datos['costo_unitario'] ?? 0,
            'stock_anterior' => $datos['stock_anterior'],
            'stock_posterior' => $datos['stock_posterior'],
            'observaciones' => $datos['observaciones'] ?? null,
            'fecha_movimiento' => $datos['fecha_movimiento'] ?? now()
        ]);
    }

    /**
     * Crear movimiento de salida
     */
    public static function crearSalida(array $datos): self
    {
        return static::create([
            'empresa_id' => Auth::user()->empresa_actual_id,
            'producto_id' => $datos['producto_id'],
            'sucursal_id' => $datos['sucursal_id'],
            'usuario_id' => Auth::id(),
            'tipo_movimiento' => self::TIPO_SALIDA,
            'referencia_tipo' => $datos['referencia_tipo'] ?? self::REFERENCIA_VENTA,
            'referencia_id' => $datos['referencia_id'] ?? null,
            'cantidad' => abs($datos['cantidad']) * -1, // Siempre negativo para salidas
            'costo_unitario' => $datos['costo_unitario'] ?? 0,
            'stock_anterior' => $datos['stock_anterior'],
            'stock_posterior' => $datos['stock_posterior'],
            'observaciones' => $datos['observaciones'] ?? null,
            'fecha_movimiento' => $datos['fecha_movimiento'] ?? now()
        ]);
    }

    // ========================= ACCESSORS =========================

    /**
     * Icono según tipo de movimiento
     */
    public function getIconoAttribute(): string
    {
        return match($this->tipo_movimiento) {
            self::TIPO_ENTRADA => '📈',
            self::TIPO_SALIDA => '📉',
            self::TIPO_AJUSTE => '⚖️',
            self::TIPO_TRANSFER_IN => '📥',
            self::TIPO_TRANSFER_OUT => '📤',
            default => '📋'
        };
    }

    /**
     * Color según tipo de movimiento
     */
    public function getColorAttribute(): string
    {
        return match($this->tipo_movimiento) {
            self::TIPO_ENTRADA, self::TIPO_TRANSFER_IN => 'green',
            self::TIPO_SALIDA, self::TIPO_TRANSFER_OUT => 'red',
            self::TIPO_AJUSTE => 'blue',
            default => 'gray'
        };
    }

    /**
     * Texto descriptivo del tipo de movimiento
     */
    public function getTipoMovimientoTexto(): string
    {
        return self::TIPOS_MOVIMIENTO[$this->tipo_movimiento] ?? $this->tipo_movimiento;
    }

    /**
     * Verificar si es movimiento de entrada
     */
    public function getEsEntradaAttribute(): bool
    {
        return $this->esEntrada();
    }
}
