<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Lote extends Model
{
    protected $fillable = [
        'empresa_id',
        'producto_id',
        'proveedor_id',
        'codigo_lote',
        'fecha_produccion',
        'fecha_vencimiento',
        'estado_lote',
        'atributos_json'
    ];

    protected $casts = [
        'fecha_produccion' => 'date',
        'fecha_vencimiento' => 'date',
        'atributos_json' => 'array'
    ];

    // =====================================================================
    // RELACIONES
    // =====================================================================

    /**
     * Lote pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Lote pertenece a un producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Lote pertenece a un proveedor (opcional)
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Lote tiene muchos items de compra
     */
    public function compraItems(): HasMany
    {
        return $this->hasMany(CompraItem::class);
    }

    /**
     * Lote tiene muchos movimientos de inventario
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class);
    }

    // =====================================================================
    // SCOPES
    // =====================================================================

    /**
     * Scope por empresa
     */
    public function scopeEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope por producto
     */
    public function scopeProducto(Builder $query, int $productoId): Builder
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope por estado
     */
    public function scopeEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado_lote', $estado);
    }

    /**
     * Scope lotes activos
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado_lote', 'activo');
    }

    /**
     * Scope lotes próximos a vencer
     */
    public function scopeProximosAVencer(Builder $query, int $dias = 30): Builder
    {
        $fechaLimite = Carbon::now()->addDays($dias);
        return $query->where('fecha_vencimiento', '<=', $fechaLimite)
                    ->where('fecha_vencimiento', '>', Carbon::now())
                    ->where('estado_lote', 'activo');
    }

    /**
     * Scope lotes vencidos
     */
    public function scopeVencidos(Builder $query): Builder
    {
        return $query->where('fecha_vencimiento', '<', Carbon::now())
                    ->whereNotNull('fecha_vencimiento');
    }

    /**
     * Scope ordenar por FIFO (First In, First Out)
     */
    public function scopeFIFO(Builder $query): Builder
    {
        return $query->orderBy('fecha_vencimiento', 'asc')
                    ->orderBy('fecha_produccion', 'asc')
                    ->orderBy('created_at', 'asc');
    }

    // =====================================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================================

    /**
     * Verificar si el lote está vencido
     */
    public function estaVencido(): bool
    {
        if (!$this->fecha_vencimiento) {
            return false;
        }
        return $this->fecha_vencimiento < Carbon::now();
    }

    /**
     * Obtener días hasta el vencimiento
     */
    public function diasHastaVencimiento(): ?int
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }
        return Carbon::now()->diffInDays($this->fecha_vencimiento, false);
    }

    /**
     * Verificar si está próximo a vencer
     */
    public function estaProximoAVencer(int $dias = 30): bool
    {
        $diasRestantes = $this->diasHastaVencimiento();
        return $diasRestantes !== null && $diasRestantes <= $dias && $diasRestantes > 0;
    }

    /**
     * Marcar como vencido
     */
    public function marcarVencido(): bool
    {
        $this->estado_lote = 'vencido';
        return $this->save();
    }

    /**
     * Marcar como agotado
     */
    public function marcarAgotado(): bool
    {
        $this->estado_lote = 'agotado';
        return $this->save();
    }

    /**
     * Bloquear lote
     */
    public function bloquear(): bool
    {
        $this->estado_lote = 'bloqueado';
        return $this->save();
    }

    /**
     * Activar lote
     */
    public function activar(): bool
    {
        $this->estado_lote = 'activo';
        return $this->save();
    }

    /**
     * Obtener stock actual del lote
     */
    public function getStockActual(): float
    {
        $entradas = $this->movimientos()->where('tipo_movimiento', 'ENTRADA')->sum('cantidad');
        $salidas = $this->movimientos()->whereIn('tipo_movimiento', ['SALIDA', 'AJUSTE'])->sum('cantidad');
        return $entradas - abs($salidas);
    }

    /**
     * Verificar si puede ser utilizado
     */
    public function puedeUtilizarse(): bool
    {
        return $this->estado_lote === 'activo' && !$this->estaVencido();
    }

    /**
     * Generar código de lote automático
     */
    public static function generarCodigo(int $empresaId, int $productoId, ?Carbon $fecha = null): string
    {
        $fecha = $fecha ?: Carbon::now();
        $fechaStr = $fecha->format('Ymd');
        
        // Buscar el último número del día
        $ultimo = static::where('empresa_id', $empresaId)
            ->where('producto_id', $productoId)
            ->where('codigo_lote', 'like', $fechaStr . '-%')
            ->orderBy('codigo_lote', 'desc')
            ->first();
            
        $numero = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo->codigo_lote);
            if (count($partes) >= 2) {
                $numero = intval(end($partes)) + 1;
            }
        }
        
        return $fechaStr . '-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
}
