<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\EmpresaScope;
use App\Models\Core\Empresa;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';
    
    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'marca_id',
        'unidad_medida_id',
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

    /**
     * Boot del modelo - Auto-scope por empresa
     */
    protected static function booted()
    {
        static::addGlobalScope(new EmpresaScope);
    }

    // ========================= RELACIONES =========================

    /**
     * Empresa a la que pertenece el producto
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Categoría del producto
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Marca del producto (opcional)
     */
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    /**
     * Unidad de medida del producto
     */
    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    /**
     * Stocks por sucursal
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductoStock::class, 'producto_id');
    }

    /**
     * Movimientos de inventario
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class, 'producto_id');
    }

    // ========================= SCOPES =========================

    /**
     * Scope: Solo productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope: Productos que manejan stock
     */
    public function scopeConStock($query)
    {
        return $query->where('maneja_stock', true);
    }

    /**
     * Scope: Buscar productos
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'ILIKE', "%{$termino}%")
              ->orWhere('codigo_interno', 'ILIKE', "%{$termino}%")
              ->orWhere('codigo_barra', 'ILIKE', "%{$termino}%")
              ->orWhere('descripcion', 'ILIKE', "%{$termino}%");
        });
    }

    /**
     * Scope: Por categoría
     */
    public function scopeCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    /**
     * Scope: Por marca
     */
    public function scopeMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }

    /**
     * Scope: Con stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereHas('stocks', function($q) {
            $q->whereRaw('cantidad_actual <= stock_minimo');
        });
    }

    // ========================= ACCESSORS =========================

    /**
     * Stock total en todas las sucursales
     */
    public function getStockTotalAttribute(): float
    {
        return $this->stocks()->sum('cantidad_actual') ?: 0;
    }

    /**
     * Stock disponible total
     */
    public function getStockDisponibleAttribute(): float
    {
        return $this->stocks()->sum('cantidad_disponible') ?: 0;
    }

    /**
     * Precio final calculado
     */
    public function getPrecioFinalAttribute(): float
    {
        return $this->getPrecioConIgv();
    }

    /**
     * Estado como texto
     */
    public function getEstadoTextoAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    // ========================= MÉTODOS DE CÁLCULO =========================

    /**
     * Obtener precio con/sin IGV
     */
    public function getPrecioConIgv(bool $conIgv = true): float
    {
        $precio = $this->precio_venta;
        
        if ($this->incluye_igv && !$conIgv) {
            // Precio incluye IGV, se solicita sin IGV
            return round($precio / 1.18, 4);
        } elseif (!$this->incluye_igv && $conIgv) {
            // Precio no incluye IGV, se solicita con IGV  
            return round($precio * 1.18, 4);
        }
        
        return $precio;
    }

    /**
     * Calcular margen basado en costo y venta
     */
    public function calcularMargen(): void
    {
        if ($this->precio_costo > 0) {
            $margen = (($this->precio_venta - $this->precio_costo) / $this->precio_costo) * 100;
            $this->margen_ganancia = round($margen, 2);
        }
    }

    /**
     * Calcular precio de venta basado en margen
     */
    public function calcularPrecioVenta(): void
    {
        if ($this->precio_costo > 0 && $this->margen_ganancia > 0) {
            $precio = $this->precio_costo * (1 + ($this->margen_ganancia / 100));
            $this->precio_venta = round($precio, 4);
        }
    }

    // ========================= MÉTODOS DE STOCK =========================

    /**
     * Obtener stock de una sucursal específica
     */
    public function getStockSucursal(int $sucursalId): float
    {
        return $this->stocks()->where('sucursal_id', $sucursalId)->value('cantidad_actual') ?: 0;
    }

    /**
     * Verificar si tiene stock bajo
     */
    public function tieneStockBajo(): bool
    {
        if (!$this->maneja_stock) return false;
        
        return $this->stocks()->where('cantidad_actual', '<=', $this->stock_minimo)->exists();
    }

    /**
     * Verificar si tiene stock suficiente
     */
    public function tieneSuficienteStock(float $cantidad, int $sucursalId = null): bool
    {
        if (!$this->maneja_stock) return true;
        
        if ($sucursalId) {
            $stock = $this->getStockSucursal($sucursalId);
            return $stock >= $cantidad;
        }
        
        return $this->stock_total >= $cantidad;
    }

    // ========================= MÉTODOS ESTÁTICOS =========================

    /**
     * Buscar producto por código
     */
    public static function porCodigo(string $codigo): ?self
    {
        return static::where('codigo_interno', $codigo)
                    ->orWhere('codigo_barra', $codigo)
                    ->first();
    }

    // ========================= MÉTODOS DE INSTANCIA =========================

    /**
     * Array para select/options
     */
    public function toSelectOption(): array
    {
        return [
            'value' => $this->id,
            'label' => $this->nombre,
            'codigo' => $this->codigo_interno,
            'precio' => $this->precio_venta,
            'stock' => $this->stock_total,
            'categoria' => $this->categoria?->nombre,
            'marca' => $this->marca?->nombre,
            'unidad' => $this->unidadMedida?->abreviacion
        ];
    }

    /**
     * Activar/desactivar producto
     */
    public function toggleActivo(): bool
    {
        $this->activo = !$this->activo;
        return $this->save();
    }
}
