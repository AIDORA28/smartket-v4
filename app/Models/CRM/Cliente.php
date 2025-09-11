<?php

namespace App\Models\CRM;

use App\Models\Core\Empresa;
use App\Models\Sales\Venta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\EmpresaScope;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'empresa_id',
        'tipo_documento',
        'numero_documento', 
        'nombre',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'genero',
        'es_empresa',
        'limite_credito',
        'credito_usado',
        'permite_credito',
        'descuento_porcentaje',
        'activo',
        'extras_json'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'es_empresa' => 'boolean',
        'limite_credito' => 'decimal:2',
        'credito_usado' => 'decimal:2', 
        'permite_credito' => 'boolean',
        'descuento_porcentaje' => 'decimal:2',
        'activo' => 'boolean',
        'extras_json' => 'array'
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
     * Relación con ventas
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Ventas recientes del cliente
     */
    public function ventasRecientes()
    {
        return $this->hasMany(Venta::class)
                    ->orderBy('fecha_venta', 'desc');
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
     * Scope para clientes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para clientes inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    /**
     * Scope para clientes con crédito disponible
     */
    public function scopeConCredito($query)
    {
        return $query->where('permite_credito', true)
                    ->where('limite_credito', '>', 0);
    }

    /**
     * Scope para clientes sin crédito
     */
    public function scopeSinCredito($query)
    {
        return $query->where(function($q) {
            $q->where('permite_credito', false)
              ->orWhere('limite_credito', '<=', 0);
        });
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Obtener nombre completo del cliente
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombre;
    }

    /**
     * Verificar si es empresa
     */
    public function getEsEmpresaAttribute()
    {
        return $this->attributes['es_empresa'] || $this->tipo_documento === 'RUC';
    }

    /**
     * Obtener crédito disponible
     */
    public function getCreditoDisponibleAttribute()
    {
        if (!$this->permite_credito || $this->limite_credito <= 0) {
            return 0;
        }

        return max(0, $this->limite_credito - $this->credito_usado);
    }

    /**
     * Obtener estado del crédito
     */
    public function getEstadoCreditoAttribute()
    {
        if (!$this->permite_credito) {
            return 'sin_credito';
        }
        
        if ($this->credito_usado == 0) {
            return 'al_dia';
        }
        
        $porcentajeUso = ($this->credito_usado / $this->limite_credito) * 100;
        
        if ($porcentajeUso <= 50) {
            return 'bajo_uso';
        } elseif ($porcentajeUso <= 80) {
            return 'uso_moderado';
        } elseif ($porcentajeUso <= 100) {
            return 'alto_uso';
        } else {
            return 'excedido';
        }
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Verificar si el cliente puede comprar a crédito
     */
    public function puedeComprarCredito($monto = 0)
    {
        if (!$this->permite_credito || $this->limite_credito <= 0) {
            return false;
        }

        $saldoTotal = $this->credito_usado + $monto;
        return $saldoTotal <= $this->limite_credito;
    }

    /**
     * Usar crédito del cliente
     */
    public function usarCredito($monto)
    {
        if (!$this->puedeComprarCredito($monto)) {
            throw new \Exception('El cliente no tiene crédito suficiente');
        }

        $this->credito_usado += $monto;
        $this->save();
        
        return $this;
    }

    /**
     * Liberar crédito del cliente (cuando se paga una factura)
     */
    public function liberarCredito($monto)
    {
        $this->credito_usado = max(0, $this->credito_usado - $monto);
        $this->save();
        
        return $this;
    }

    /**
     * Aplicar descuento por defecto
     */
    public function aplicarDescuentoPorDefecto($subtotal)
    {
        if ($this->descuento_porcentaje <= 0) {
            return 0;
        }

        return ($subtotal * $this->descuento_porcentaje) / 100;
    }

    /**
     * Obtener información de crédito
     */
    public function getInfoCredito()
    {
        return [
            'permite_credito' => $this->permite_credito,
            'limite_credito' => $this->limite_credito,
            'credito_usado' => $this->credito_usado,
            'credito_disponible' => $this->credito_disponible,
            'estado' => $this->estado_credito
        ];
    }

    /**
     * Obtener estadísticas del cliente
     */
    public function getEstadisticas()
    {
        return [
            'total_compras' => $this->ventas()->count(),
            'monto_total_compras' => $this->ventas()->sum('total'),
            'ultima_compra' => $this->ventas()->latest('fecha_venta')->first()?->fecha_venta,
            'promedio_compra' => $this->ventas()->avg('total') ?? 0,
        ];
    }
}
