<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\EmpresaScope;
use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;

class ProductoStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'producto_stocks';
    
    protected $fillable = [
        'empresa_id',
        'producto_id',
        'sucursal_id',
        'cantidad_actual',
        'cantidad_reservada',
        'cantidad_disponible',
        'costo_promedio',
        'ultimo_movimiento'
    ];

    protected $casts = [
        'cantidad_actual' => 'decimal:2',
        'cantidad_reservada' => 'decimal:2',
        'cantidad_disponible' => 'decimal:2',
        'costo_promedio' => 'decimal:4',
        'ultimo_movimiento' => 'datetime',
    ];

    /**
     * Boot del modelo - Auto-scope por empresa
     */
    protected static function booted()
    {
        static::addGlobalScope(new EmpresaScope);
        
        // Auto-calcular cantidad disponible
        static::saving(function ($stock) {
            $stock->cantidad_disponible = $stock->cantidad_actual - $stock->cantidad_reservada;
        });
    }

    // ========================= RELACIONES =========================

    /**
     * Empresa del stock
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Producto del stock
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Sucursal del stock
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Movimientos relacionados con este stock
     */
    public function movimientos()
    {
        return $this->hasMany(InventarioMovimiento::class, 'producto_id', 'producto_id')
                    ->where('sucursal_id', $this->sucursal_id);
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
     * Scope: Con stock disponible
     */
    public function scopeConStock($query)
    {
        return $query->where('cantidad_actual', '>', 0);
    }

    /**
     * Scope: Stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereHas('producto', function($q) {
            $q->whereRaw('producto_stocks.cantidad_actual <= productos.stock_minimo');
        });
    }

    /**
     * Scope: Ordenado por producto
     */
    public function scopeOrdenado($query)
    {
        return $query->join('productos', 'producto_stocks.producto_id', '=', 'productos.id')
                    ->orderBy('productos.nombre');
    }

    // ========================= MÉTODOS DE INSTANCIA =========================

    /**
     * Verificar si tiene stock suficiente
     */
    public function tieneSuficienteStock(float $cantidad): bool
    {
        return $this->cantidad_disponible >= $cantidad;
    }

    /**
     * Reservar stock
     */
    public function reservar(float $cantidad): bool
    {
        if (!$this->tieneSuficienteStock($cantidad)) {
            return false;
        }
        
        $this->cantidad_reservada += $cantidad;
        return $this->save();
    }

    /**
     * Verificar si está en stock mínimo
     */
    public function enStockMinimo(): bool
    {
        return $this->cantidad_actual <= ($this->producto->stock_minimo ?? 0);
    }
}
