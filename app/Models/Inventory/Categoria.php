<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\EmpresaScope;
use App\Models\Core\Empresa;
use Illuminate\Support\Facades\Auth;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorias';
    
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
     * Empresa a la que pertenece la categoría
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Productos de esta categoría
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    /**
     * Productos activos de esta categoría
     */
    public function productosActivos(): HasMany
    {
        return $this->productos()->where('activo', true);
    }

    // ========================= SCOPES =========================

    /**
     * Scope: Solo categorías activas
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

    // ========================= MUTATORS =========================

    /**
     * Normalizar nombre
     */
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = trim(ucwords(strtolower($value)));
    }

    /**
     * Normalizar código
     */
    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = $value ? strtoupper(trim($value)) : null;
    }

    // ========================= ACCESSORS =========================

    /**
     * Estado de la categoría como texto
     */
    public function getEstadoTextoAttribute(): string
    {
        return $this->activa ? 'Activa' : 'Inactiva';
    }

    /**
     * Color para badge
     */
    public function getColorBadgeAttribute(): string
    {
        return $this->activa ? 'green' : 'gray';
    }

    // ========================= MÉTODOS ESTÁTICOS =========================

    /**
     * Buscar categoría por código en la empresa actual
     */
    public static function porCodigo(string $codigo): ?self
    {
        return static::where('codigo', strtoupper($codigo))->first();
    }

    /**
     * Crear categoría con datos mínimos
     */
    public static function crear(array $datos): self
    {
        return static::create([
            'empresa_id' => Auth::user()->empresa_actual_id,
            'nombre' => $datos['nombre'],
            'codigo' => $datos['codigo'] ?? null,
            'descripcion' => $datos['descripcion'] ?? null,
            'color' => $datos['color'] ?? '#6B7280',
            'icono' => $datos['icono'] ?? '📦',
            'activa' => $datos['activa'] ?? true
        ]);
    }

    // ========================= MÉTODOS DE INSTANCIA =========================

    /**
     * Activar/desactivar categoría
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
     * Verificar si tiene productos asociados
     */
    public function tieneProductos(): bool
    {
        return $this->productos()->exists();
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
