<?php

namespace App\Models\Purchases;

use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\EmpresaScope;

class Recepcion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recepciones';

    protected $fillable = [
        'empresa_id',
        'compra_id',
        'sucursal_id',
        'user_id',
        'fecha_recepcion',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EmpresaScope);
    }

    // ==================== ESTADOS DISPONIBLES ====================
    const ESTADO_PARCIAL = 'parcial';
    const ESTADO_COMPLETA = 'completa';
    const ESTADO_CON_DIFERENCIAS = 'con_diferencias';

    const ESTADOS = [
        self::ESTADO_PARCIAL => 'Parcial',
        self::ESTADO_COMPLETA => 'Completa',
        self::ESTADO_CON_DIFERENCIAS => 'Con Diferencias'
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
     * Relación con compra
     */
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Relación with sucursal
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Relación con usuario que registra
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con items de recepción (si se implementa tabla separada)
     */
    public function items()
    {
        // Por ahora retornamos los items de la compra
        // En el futuro se puede crear una tabla recepcion_items
        return $this->compra->items();
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
     * Scope para recepciones por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para recepciones parciales
     */
    public function scopeParciales($query)
    {
        return $query->where('estado', self::ESTADO_PARCIAL);
    }

    /**
     * Scope para recepciones completas
     */
    public function scopeCompletas($query)
    {
        return $query->where('estado', self::ESTADO_COMPLETA);
    }

    /**
     * Scope para recepciones con diferencias
     */
    public function scopeConDiferencias($query)
    {
        return $query->where('estado', self::ESTADO_CON_DIFERENCIAS);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_recepcion', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para filtrar por sucursal
     */
    public function scopePorSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    /**
     * Scope para recepciones del día
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now()->format('Y-m-d');
        return $query->whereDate('fecha_recepcion', $fecha);
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
     * Verificar si la recepción está completa
     */
    public function getEstaCompletaAttribute()
    {
        return $this->estado === self::ESTADO_COMPLETA;
    }

    /**
     * Verificar si es recepción parcial
     */
    public function getEsParcialAttribute()
    {
        return $this->estado === self::ESTADO_PARCIAL;
    }

    /**
     * Verificar si tiene diferencias
     */
    public function getTieneDiferenciasAttribute()
    {
        return $this->estado === self::ESTADO_CON_DIFERENCIAS;
    }

    /**
     * Obtener información de la compra relacionada
     */
    public function getInfoCompraAttribute()
    {
        return [
            'id' => $this->compra->id,
            'fecha' => $this->compra->fecha->format('d/m/Y'),
            'proveedor' => $this->compra->proveedor->nombre,
            'numero_doc' => $this->compra->numero_doc,
            'total' => $this->compra->total,
            'estado' => $this->compra->estado_nombre
        ];
    }

    // ==================== MÉTODOS DE NEGOCIO ====================

    /**
     * Marcar recepción como completa
     */
    public function marcarComoCompleta($observaciones = null)
    {
        if ($this->estado === self::ESTADO_COMPLETA) {
            throw new \Exception('La recepción ya está marcada como completa');
        }

        $datosActualizacion = ['estado' => self::ESTADO_COMPLETA];
        
        if ($observaciones) {
            $datosActualizacion['observaciones'] = $this->observaciones . "\n" . $observaciones;
        }

        $this->update($datosActualizacion);
        
        // Actualizar estado de la compra si es necesario
        $this->compra->marcarComoRecibida();
        
        return $this;
    }

    /**
     * Marcar recepción con diferencias
     */
    public function marcarConDiferencias($diferencias)
    {
        $observacionesDiferencias = "DIFERENCIAS ENCONTRADAS:\n" . $diferencias;
        
        $this->update([
            'estado' => self::ESTADO_CON_DIFERENCIAS,
            'observaciones' => $this->observaciones . "\n" . $observacionesDiferencias
        ]);
        
        return $this;
    }

    /**
     * Procesar recepción de items
     */
    public function procesarRecepcion($itemsRecibidos)
    {
        $diferenciasEncontradas = false;
        $diferenciasDetalle = [];

        foreach ($itemsRecibidos as $itemData) {
            $compraItem = $this->compra->items()->find($itemData['compra_item_id']);
            
            if (!$compraItem) {
                continue;
            }

            $cantidadEsperada = $compraItem->cantidad;
            $cantidadRecibida = $itemData['cantidad_recibida'];

            if ($cantidadRecibida != $cantidadEsperada) {
                $diferenciasEncontradas = true;
                $diferenciasDetalle[] = sprintf(
                    "Producto %s: Esperado %.3f, Recibido %.3f (Diferencia: %.3f)",
                    $compraItem->producto->nombre,
                    $cantidadEsperada,
                    $cantidadRecibida,
                    $cantidadRecibida - $cantidadEsperada
                );
            }

            // Aquí se actualizaría el stock (cuando se implemente el módulo Warehouse)
            // $this->actualizarStock($compraItem, $cantidadRecibida);
        }

        if ($diferenciasEncontradas) {
            $this->marcarConDiferencias(implode("\n", $diferenciasDetalle));
        } else {
            $this->marcarComoCompleta("Recepción procesada sin diferencias");
        }

        return [
            'tiene_diferencias' => $diferenciasEncontradas,
            'diferencias' => $diferenciasDetalle,
            'estado_final' => $this->estado
        ];
    }

    /**
     * Calcular porcentaje de recepción
     */
    public function calcularPorcentajeRecepcion()
    {
        $totalEsperado = $this->compra->items()->sum('cantidad');
        
        // Por ahora asumimos que si está completa es 100%
        // En el futuro se calculará desde recepcion_items
        if ($this->esta_completa) {
            return 100;
        } elseif ($this->es_parcial) {
            return 50; // Valor aproximado
        } else {
            return 0;
        }
    }

    /**
     * Obtener resumen de la recepción
     */
    public function getResumen()
    {
        return [
            'id' => $this->id,
            'fecha_recepcion' => $this->fecha_recepcion->format('d/m/Y H:i'),
            'compra' => $this->info_compra,
            'sucursal' => $this->sucursal->nombre,
            'usuario' => $this->usuario->name,
            'estado' => $this->estado_nombre,
            'porcentaje_recepcion' => $this->calcularPorcentajeRecepcion(),
            'observaciones' => $this->observaciones
        ];
    }

    /**
     * Generar reporte de recepción
     */
    public function generarReporte()
    {
        return [
            'recepcion' => $this->getResumen(),
            'items_compra' => $this->compra->items()->with('producto')->get()->map(function ($item) {
                return [
                    'producto' => $item->producto->nombre,
                    'cantidad_esperada' => $item->cantidad,
                    'costo_unitario' => $item->costo_unitario,
                    'subtotal' => $item->subtotal
                ];
            }),
            'estadisticas' => [
                'total_items' => $this->compra->items()->count(),
                'valor_total' => $this->compra->total,
                'estado_recepcion' => $this->estado_nombre
            ]
        ];
    }

    /**
     * Validar que se puede procesar la recepción
     */
    public function validarProcesamiento()
    {
        $errores = [];

        if ($this->compra->estado !== Compra::ESTADO_CONFIRMADA) {
            $errores[] = 'La compra debe estar confirmada para poder recibir mercadería';
        }

        if ($this->esta_completa) {
            $errores[] = 'Esta recepción ya fue marcada como completa';
        }

        if ($this->compra->items()->count() === 0) {
            $errores[] = 'La compra no tiene items para recibir';
        }

        return [
            'puede_procesar' => empty($errores),
            'errores' => $errores
        ];
    }
}
