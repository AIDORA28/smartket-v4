<?php

namespace App\Models\Purchases;

use App\Models\Core\Empresa;
use App\Models\Inventory\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\EmpresaScope;

class CompraItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compra_items';

    protected $fillable = [
        'empresa_id',
        'compra_id',
        'producto_id',
        'lote_id',
        'cantidad',
        'costo_unitario',
        'descuento_pct',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'costo_unitario' => 'decimal:4',
        'descuento_pct' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EmpresaScope);
        
        // Auto-calcular subtotal al guardar
        static::saving(function ($compraItem) {
            $compraItem->calcularSubtotal();
        });
    }

    // ==================== RELACIONES ====================

    /**
     * Relación con empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación con compra
     */
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Relación con producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con lote (nullable por ahora)
     */
    public function lote()
    {
        return $this->belongsTo(\App\Models\Lote::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para filtrar por compra
     */
    public function scopePorCompra($query, $compraId)
    {
        return $query->where('compra_id', $compraId);
    }

    /**
     * Scope para filtrar por producto
     */
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope para items con descuento
     */
    public function scopeConDescuento($query)
    {
        return $query->where('descuento_pct', '>', 0);
    }

    /**
     * Scope para items sin descuento
     */
    public function scopeSinDescuento($query)
    {
        return $query->where('descuento_pct', 0);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Obtener el monto del descuento
     */
    public function getMontoDescuentoAttribute()
    {
        return ($this->cantidad * $this->costo_unitario * $this->descuento_pct) / 100;
    }

    /**
     * Obtener el subtotal sin descuento
     */
    public function getSubtotalSinDescuentoAttribute()
    {
        return $this->cantidad * $this->costo_unitario;
    }

    /**
     * Obtener el costo unitario con descuento aplicado
     */
    public function getCostoUnitarioFinalAttribute()
    {
        return $this->costo_unitario * (1 - ($this->descuento_pct / 100));
    }

    /**
     * Verificar si tiene descuento
     */
    public function getTieneDescuentoAttribute()
    {
        return $this->descuento_pct > 0;
    }

    /**
     * Obtener información del producto
     */
    public function getInfoProductoAttribute()
    {
        return [
            'nombre' => $this->producto->nombre ?? 'Producto no encontrado',
            'codigo' => $this->producto->codigo ?? null,
            'unidad_medida' => $this->producto->unidadMedida->nombre ?? null,
            'categoria' => $this->producto->categoria->nombre ?? null,
        ];
    }

    // ==================== MÉTODOS DE NEGOCIO ====================

    /**
     * Calcular subtotal automáticamente
     */
    public function calcularSubtotal()
    {
        $subtotalSinDescuento = $this->cantidad * $this->costo_unitario;
        $montoDescuento = ($subtotalSinDescuento * $this->descuento_pct) / 100;
        $this->subtotal = $subtotalSinDescuento - $montoDescuento;
        
        return $this->subtotal;
    }

    /**
     * Actualizar cantidad
     */
    public function actualizarCantidad($nuevaCantidad)
    {
        if ($this->compra->estado !== Compra::ESTADO_BORRADOR) {
            throw new \Exception('Solo se puede modificar la cantidad en compras en borrador');
        }

        $this->update(['cantidad' => $nuevaCantidad]);
        $this->compra->calcularTotal();
        
        return $this;
    }

    /**
     * Actualizar costo unitario
     */
    public function actualizarCosto($nuevoCosto)
    {
        if ($this->compra->estado !== Compra::ESTADO_BORRADOR) {
            throw new \Exception('Solo se puede modificar el costo en compras en borrador');
        }

        $this->update(['costo_unitario' => $nuevoCosto]);
        $this->compra->calcularTotal();
        
        return $this;
    }

    /**
     * Aplicar descuento
     */
    public function aplicarDescuento($porcentaje)
    {
        if ($this->compra->estado !== Compra::ESTADO_BORRADOR) {
            throw new \Exception('Solo se puede aplicar descuento en compras en borrador');
        }

        if ($porcentaje < 0 || $porcentaje > 100) {
            throw new \Exception('El porcentaje de descuento debe estar entre 0 y 100');
        }

        $this->update(['descuento_pct' => $porcentaje]);
        $this->compra->calcularTotal();
        
        return $this;
    }

    /**
     * Quitar descuento
     */
    public function quitarDescuento()
    {
        return $this->aplicarDescuento(0);
    }

    /**
     * Duplicar item (crear una copia)
     */
    public function duplicar()
    {
        if ($this->compra->estado !== Compra::ESTADO_BORRADOR) {
            throw new \Exception('Solo se puede duplicar items en compras en borrador');
        }

        $nuevoItem = $this->replicate();
        $nuevoItem->save();
        
        $this->compra->calcularTotal();
        
        return $nuevoItem;
    }

    /**
     * Obtener resumen del item
     */
    public function getResumen()
    {
        return [
            'id' => $this->id,
            'producto' => $this->info_producto,
            'cantidad' => $this->cantidad,
            'costo_unitario' => $this->costo_unitario,
            'descuento_pct' => $this->descuento_pct,
            'monto_descuento' => $this->monto_descuento,
            'costo_unitario_final' => $this->costo_unitario_final,
            'subtotal_sin_descuento' => $this->subtotal_sin_descuento,
            'subtotal' => $this->subtotal,
            'tiene_descuento' => $this->tiene_descuento
        ];
    }

    /**
     * Validar datos del item
     */
    public function validar()
    {
        $errores = [];

        if ($this->cantidad <= 0) {
            $errores[] = 'La cantidad debe ser mayor a 0';
        }

        if ($this->costo_unitario < 0) {
            $errores[] = 'El costo unitario no puede ser negativo';
        }

        if ($this->descuento_pct < 0 || $this->descuento_pct > 100) {
            $errores[] = 'El descuento debe estar entre 0% y 100%';
        }

        if (!$this->producto_id || !$this->producto) {
            $errores[] = 'Debe seleccionar un producto válido';
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }

    /**
     * Obtener costo promedio para el producto
     */
    public function getCostoPromedioProducto()
    {
        return static::where('producto_id', $this->producto_id)
            ->whereHas('compra', function ($query) {
                $query->where('estado', '!=', Compra::ESTADO_ANULADA);
            })
            ->avg('costo_unitario');
    }
}
