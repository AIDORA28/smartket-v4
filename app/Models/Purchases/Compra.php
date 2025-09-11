<?php

namespace App\Models\Purchases;

use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\EmpresaScope;

class Compra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compras';

    protected $fillable = [
        'empresa_id',
        'proveedor_id',
        'sucursal_destino_id',
        'user_id',
        'fecha',
        'numero_doc',
        'tipo_doc',
        'estado',
        'total',
        'moneda',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EmpresaScope);
    }

    // ==================== ESTADOS DISPONIBLES ====================
    const ESTADO_BORRADOR = 'borrador';
    const ESTADO_CONFIRMADA = 'confirmada';
    const ESTADO_RECIBIDA = 'recibida';
    const ESTADO_ANULADA = 'anulada';

    const ESTADOS = [
        self::ESTADO_BORRADOR => 'Borrador',
        self::ESTADO_CONFIRMADA => 'Confirmada',
        self::ESTADO_RECIBIDA => 'Recibida',
        self::ESTADO_ANULADA => 'Anulada'
    ];

    // ==================== TIPOS DE DOCUMENTO ====================
    const TIPO_FACTURA = 'factura';
    const TIPO_BOLETA = 'boleta';
    const TIPO_RECIBO = 'recibo';
    const TIPO_NOTA = 'nota';

    const TIPOS_DOC = [
        self::TIPO_FACTURA => 'Factura',
        self::TIPO_BOLETA => 'Boleta',
        self::TIPO_RECIBO => 'Recibo',
        self::TIPO_NOTA => 'Nota'
    ];

    // ==================== RELACIONES ====================

    /**
     * Relación con empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación con proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación con sucursal destino
     */
    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    /**
     * Relación con usuario que registra
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con items de compra
     */
    public function items()
    {
        return $this->hasMany(CompraItem::class);
    }

    /**
     * Relación con recepciones
     */
    public function recepciones()
    {
        return $this->hasMany(Recepcion::class);
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
     * Scope para compras por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para compras en borrador
     */
    public function scopeBorrador($query)
    {
        return $query->where('estado', self::ESTADO_BORRADOR);
    }

    /**
     * Scope para compras confirmadas
     */
    public function scopeConfirmadas($query)
    {
        return $query->where('estado', self::ESTADO_CONFIRMADA);
    }

    /**
     * Scope para compras recibidas
     */
    public function scopeRecibidas($query)
    {
        return $query->where('estado', self::ESTADO_RECIBIDA);
    }

    /**
     * Scope para compras anuladas
     */
    public function scopeAnuladas($query)
    {
        return $query->where('estado', self::ESTADO_ANULADA);
    }

    /**
     * Scope para compras activas (no anuladas)
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', '!=', self::ESTADO_ANULADA);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para filtrar por proveedor
     */
    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Obtener nombre del estado
     */
    public function getEstadoNombreAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    /**
     * Obtener nombre del tipo de documento
     */
    public function getTipoDocNombreAttribute()
    {
        return self::TIPOS_DOC[$this->tipo_doc] ?? $this->tipo_doc;
    }

    /**
     * Verificar si la compra está en borrador
     */
    public function getEsBorradorAttribute()
    {
        return $this->estado === self::ESTADO_BORRADOR;
    }

    /**
     * Verificar si la compra está confirmada
     */
    public function getEstaConfirmadaAttribute()
    {
        return $this->estado === self::ESTADO_CONFIRMADA;
    }

    /**
     * Verificar si la compra está recibida
     */
    public function getEstaRecibidaAttribute()
    {
        return $this->estado === self::ESTADO_RECIBIDA;
    }

    /**
     * Verificar si la compra está anulada
     */
    public function getEstaAnuladaAttribute()
    {
        return $this->estado === self::ESTADO_ANULADA;
    }

    /**
     * Obtener el porcentaje de recepción
     */
    public function getPorcentajeRecepcionAttribute()
    {
        $totalItems = $this->items()->sum('cantidad');
        $totalRecibido = $this->recepciones()
            ->join('recepcion_items', 'recepciones.id', '=', 'recepcion_items.recepcion_id')
            ->sum('recepcion_items.cantidad_recibida');

        if ($totalItems == 0) {
            return 0;
        }

        return round(($totalRecibido / $totalItems) * 100, 2);
    }

    // ==================== MÉTODOS DE NEGOCIO ====================

    /**
     * Confirmar la compra
     */
    public function confirmar()
    {
        if ($this->estado !== self::ESTADO_BORRADOR) {
            throw new \Exception('Solo se pueden confirmar compras en borrador');
        }

        if ($this->items()->count() === 0) {
            throw new \Exception('No se puede confirmar una compra sin items');
        }

        $this->update(['estado' => self::ESTADO_CONFIRMADA]);
        
        return $this;
    }

    /**
     * Anular la compra
     */
    public function anular($motivo = null)
    {
        if ($this->estado === self::ESTADO_ANULADA) {
            throw new \Exception('La compra ya está anulada');
        }

        if ($this->estado === self::ESTADO_RECIBIDA) {
            throw new \Exception('No se puede anular una compra que ya fue recibida');
        }

        $observaciones = $this->observaciones;
        if ($motivo) {
            $observaciones .= "\nAnulada: " . $motivo;
        }

        $this->update([
            'estado' => self::ESTADO_ANULADA,
            'observaciones' => $observaciones
        ]);
        
        return $this;
    }

    /**
     * Marcar como recibida
     */
    public function marcarComoRecibida()
    {
        if ($this->estado !== self::ESTADO_CONFIRMADA) {
            throw new \Exception('Solo se pueden marcar como recibidas las compras confirmadas');
        }

        $this->update(['estado' => self::ESTADO_RECIBIDA]);
        
        return $this;
    }

    /**
     * Calcular total desde items
     */
    public function calcularTotal()
    {
        $total = $this->items()->sum('subtotal');
        $this->update(['total' => $total]);
        
        return $this;
    }

    /**
     * Agregar item a la compra
     */
    public function agregarItem($productoId, $cantidad, $costoUnitario, $descuentoPct = 0)
    {
        if ($this->estado !== self::ESTADO_BORRADOR) {
            throw new \Exception('Solo se pueden agregar items a compras en borrador');
        }

        $subtotal = ($cantidad * $costoUnitario) * (1 - ($descuentoPct / 100));

        $item = $this->items()->create([
            'empresa_id' => $this->empresa_id,
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'costo_unitario' => $costoUnitario,
            'descuento_pct' => $descuentoPct,
            'subtotal' => $subtotal
        ]);

        $this->calcularTotal();

        return $item;
    }

    /**
     * Obtener resumen de la compra
     */
    public function getResumen()
    {
        return [
            'id' => $this->id,
            'fecha' => $this->fecha->format('d/m/Y'),
            'proveedor' => $this->proveedor->nombre,
            'numero_doc' => $this->numero_doc,
            'tipo_doc' => $this->tipo_doc_nombre,
            'estado' => $this->estado_nombre,
            'total' => $this->total,
            'moneda' => $this->moneda,
            'cantidad_items' => $this->items()->count(),
            'porcentaje_recepcion' => $this->porcentaje_recepcion
        ];
    }

    /**
     * Obtener historial de estados
     */
    public function getHistorialEstados()
    {
        // Esta funcionalidad se puede implementar con un modelo separado
        // para tracking de cambios de estado
        return [
            'creacion' => $this->created_at,
            'estado_actual' => $this->estado,
            'ultima_modificacion' => $this->updated_at
        ];
    }
}
