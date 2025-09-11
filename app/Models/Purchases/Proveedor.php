<?php

namespace App\Models\Purchases;

use App\Models\Core\Empresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\EmpresaScope;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'documento_tipo',
        'documento_numero', 
        'telefono',
        'email',
        'direccion',
        'contacto_json'
    ];

    protected $casts = [
        'contacto_json' => 'array'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EmpresaScope);
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
     * Relación con compras
     */
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    /**
     * Compras recientes del proveedor
     */
    public function comprasRecientes()
    {
        return $this->hasMany(Compra::class)
                    ->orderBy('fecha', 'desc');
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
     * Scope para proveedores activos (que tienen compras recientes)
     */
    public function scopeActivos($query)
    {
        return $query->whereHas('compras', function ($q) {
            $q->where('created_at', '>=', now()->subMonths(6));
        });
    }

    /**
     * Scope para buscar por nombre o documento
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('documento_numero', 'like', "%{$termino}%")
              ->orWhere('telefono', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Formatear nombre del proveedor
     */
    public function getNombreFormateadoAttribute()
    {
        $nombre = $this->nombre;
        
        if ($this->documento_numero) {
            $nombre .= " ({$this->documento_tipo}: {$this->documento_numero})";
        }
        
        return $nombre;
    }

    /**
     * Verificar si es empresa (RUC)
     */
    public function getEsEmpresaAttribute()
    {
        return $this->documento_tipo === 'RUC';
    }

    /**
     * Obtener información de contacto principal
     */
    public function getContactoPrincipalAttribute()
    {
        $contacto = [];
        
        if ($this->telefono) {
            $contacto['telefono'] = $this->telefono;
        }
        
        if ($this->email) {
            $contacto['email'] = $this->email;
        }
        
        if ($this->contacto_json && is_array($this->contacto_json)) {
            $contacto = array_merge($contacto, $this->contacto_json);
        }
        
        return $contacto;
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Obtener estadísticas del proveedor
     */
    public function getEstadisticas()
    {
        return [
            'total_compras' => $this->compras()->count(),
            'monto_total_compras' => $this->compras()->where('estado', '!=', 'anulada')->sum('total'),
            'ultima_compra' => $this->compras()->latest('fecha')->first()?->fecha,
            'promedio_compra' => $this->compras()->where('estado', '!=', 'anulada')->avg('total') ?? 0,
            'compras_pendientes' => $this->compras()->where('estado', 'confirmada')->count(),
        ];
    }

    /**
     * Obtener productos más comprados a este proveedor
     */
    public function getProductosMasComprados($limit = 10)
    {
        return $this->compras()
            ->join('compra_items', 'compras.id', '=', 'compra_items.compra_id')
            ->join('productos', 'compra_items.producto_id', '=', 'productos.id')
            ->selectRaw('productos.nombre, SUM(compra_items.cantidad) as total_cantidad, COUNT(*) as veces_comprado')
            ->where('compras.estado', '!=', 'anulada')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_cantidad', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Verificar si el proveedor está activo
     */
    public function estaActivo()
    {
        return $this->compras()->where('created_at', '>=', now()->subMonths(6))->exists();
    }

    /**
     * Obtener historial de compras del último año
     */
    public function getHistorialCompras($meses = 12)
    {
        return $this->compras()
            ->where('fecha', '>=', now()->subMonths($meses))
            ->orderBy('fecha', 'desc')
            ->with(['items.producto', 'sucursalDestino'])
            ->get();
    }

    /**
     * Actualizar información de contacto
     */
    public function actualizarContacto($nuevoContacto)
    {
        $contactoActual = $this->contacto_json ?? [];
        $contactoActualizado = array_merge($contactoActual, $nuevoContacto);
        
        $this->update(['contacto_json' => $contactoActualizado]);
        
        return $this;
    }
}
