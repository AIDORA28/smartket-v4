<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\EmpresaScope;
use App\Models\Core\Empresa;

class Marca extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marcas';
    
    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo',
        'descripcion',
        'color',
        'icono',
        'activa',
        'productos_count'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'productos_count' => 'integer'
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
     * Empresa a la que pertenece la marca
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Productos de esta marca
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'marca_id');
    }

    /**
     * Productos activos de esta marca
     */
    public function productosActivos(): HasMany
    {
        return $this->productos()->where('activo', true);
    }

    // ========================= SCOPES =========================

    /**
     * Scope: Solo marcas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope: Buscar por nombre o código
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'ILIKE', "%{$termino}%")
              ->orWhere('codigo', 'ILIKE', "%{$termino}%")
              ->orWhere('descripcion', 'ILIKE', "%{$termino}%");
        });
    }

    /**
     * Scope: Ordenar por nombre
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('nombre', 'asc');
    }

    // ========================= MÉTODOS ESTÁTICOS =========================

    /**
     * Buscar marca por código en la empresa actual
     */
    public static function porCodigo(string $codigo): ?self
    {
        return static::where('codigo', strtoupper($codigo))->first();
    }

    // ========================= MÉTODOS DE INSTANCIA =========================

    /**
     * Activar/desactivar marca
     */
    public function toggleActiva(): bool
    {
        $this->activa = !$this->activa;
        return $this->save();
    }

    /**
     * Actualizar contador de productos
     */
    public function actualizarContadorProductos(): void
    {
        $this->productos_count = $this->productos()->count();
        $this->save();
    }

    /**
     * Array para select/options
     */
    public function toSelectOption(): array
    {
        return [
            'value' => $this->id,
            'label' => $this->nombre,
            'color' => $this->color,
            'icono' => $this->icono,
            'productos_count' => $this->productos_count
        ];
    }
}
