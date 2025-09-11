<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\EmpresaScope;
use App\Models\Core\Empresa;

class UnidadMedida extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unidades_medida';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'abreviacion',
        'tipo',
        'icono',
        'activa',
        'productos_count'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'productos_count' => 'integer'
    ];

    // ========================= CONSTANTES =========================

    public const TIPOS = [
        'PESO' => 'Peso',
        'VOLUMEN' => 'Volumen',
        'LONGITUD' => 'Longitud',
        'AREA' => 'Área',
        'CONTABLE' => 'Contable',
        'TIEMPO' => 'Tiempo',
        'GENERAL' => 'General'
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
     * Empresa a la que pertenece la unidad
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Productos que usan esta unidad
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'unidad_medida_id');
    }

    // ========================= SCOPES =========================

    /**
     * Scope: Solo unidades activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope: Por tipo de unidad
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Buscar por nombre o abreviación
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'ILIKE', "%{$termino}%")
              ->orWhere('abreviacion', 'ILIKE', "%{$termino}%");
        });
    }

    /**
     * Scope: Ordenado por nombre
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('nombre');
    }

    // ========================= ACCESSORS =========================

    /**
     * Texto completo: nombre (abrev)
     */
    public function getTextoCompletoAttribute(): string
    {
        return "{$this->nombre} ({$this->abreviacion})";
    }

    /**
     * Nombre del tipo de unidad
     */
    public function getTipoNombreAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    // ========================= MÉTODOS DE INSTANCIA =========================

    /**
     * Array para select/options
     */
    public function toSelectOption(): array
    {
        return [
            'value' => $this->id,
            'label' => $this->texto_completo,
            'abreviacion' => $this->abreviacion,
            'tipo' => $this->tipo,
            'tipo_nombre' => $this->tipo_nombre,
            'icono' => $this->icono
        ];
    }

    /**
     * Verificar si tiene productos asociados
     */
    public function tieneProductos(): bool
    {
        return $this->productos()->exists();
    }

    /**
     * Toggle estado activa
     */
    public function toggleActiva(): bool
    {
        $this->activa = !$this->activa;
        return $this->save();
    }
}
