<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'nombre',
        'codigo_interno',
        'codigo_barra',
        'descripcion',
        'precio_costo',
        'precio_venta',
        'margen_ganancia',
        'incluye_igv',
        'unidad_medida',
        'permite_decimales',
        'maneja_stock',
        'stock_minimo',
        'stock_maximo',
        'activo',
        'imagen_url',
        'extras_json'
    ];

    protected $casts = [
        'precio_costo' => 'decimal:4',
        'precio_venta' => 'decimal:4',
        'margen_ganancia' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'stock_maximo' => 'decimal:2',
        'incluye_igv' => 'boolean',
        'permite_decimales' => 'boolean',
        'maneja_stock' => 'boolean',
        'activo' => 'boolean',
        'extras_json' => 'array',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductoStock::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class);
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->activo;
    }

    public function manejaStock(): bool
    {
        return $this->maneja_stock;
    }

    public function getStockTotal(): float
    {
        return $this->stocks()->sum('cantidad_actual');
    }

    public function getStockSucursal(int $sucursalId): float
    {
        return $this->stocks()->where('sucursal_id', $sucursalId)->value('cantidad_actual') ?? 0;
    }

    public function calcularPrecioVenta(): void
    {
        if ($this->margen_ganancia > 0) {
            $precioBase = $this->precio_costo * (1 + ($this->margen_ganancia / 100));
            $this->precio_venta = $precioBase;
        }
    }

    public function getPrecioFinal(bool $conIgv = true): float
    {
        $precio = $this->precio_venta;
        
        if ($this->incluye_igv && !$conIgv) {
            // El precio incluye IGV pero se solicita sin IGV
            return $precio / 1.18;
        } elseif (!$this->incluye_igv && $conIgv) {
            // El precio no incluye IGV pero se solicita con IGV
            return $precio * 1.18;
        }
        
        return $precio;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeWithStock($query)
    {
        return $query->where('maneja_stock', true);
    }

    public function scopeConStockBajo($query)
    {
        return $query->whereHas('stocks', function ($q) {
            $q->whereRaw('cantidad_actual <= stock_minimo');
        });
    }
}
