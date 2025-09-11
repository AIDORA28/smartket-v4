<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ReporteTemplate extends Model
{
    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
        'tipo',
        'subtipo',
        'configuracion_json',
        'filtros_defecto_json',
        'columnas_json',
        'formato_defecto',
        'es_publico',
        'es_sistema',
        'orden_display',
        'activo'
    ];

    protected $casts = [
        'configuracion_json' => 'array',
        'filtros_defecto_json' => 'array',
        'columnas_json' => 'array',
        'es_publico' => 'boolean',
        'es_sistema' => 'boolean',
        'orden_display' => 'integer',
        'activo' => 'boolean'
    ];

    // =====================================================================
    // RELACIONES
    // =====================================================================

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function reportes(): HasMany
    {
        return $this->hasMany(Reporte::class, 'template_id');
    }

    // =====================================================================
    // SCOPES
    // =====================================================================

    public function scopeEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where(function($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId)
              ->orWhereNull('empresa_id'); // Templates globales
        });
    }

    public function scopeTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopePublicos(Builder $query): Builder
    {
        return $query->where('es_publico', true);
    }

    public function scopeSistema(Builder $query): Builder
    {
        return $query->where('es_sistema', true);
    }

    public function scopeOrdenDisplay(Builder $query): Builder
    {
        return $query->orderBy('orden_display', 'asc')
                    ->orderBy('nombre', 'asc');
    }

    // =====================================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================================

    /**
     * Verificar si el template es editable
     */
    public function esEditable(): bool
    {
        return !$this->es_sistema;
    }

    /**
     * Crear reporte desde template
     */
    public function crearReporte(int $usuarioId, array $filtros = [], string $formato = null): Reporte
    {
        $configuracion = $this->configuracion_json;
        $configuracion['template_id'] = $this->id;
        $configuracion['template_nombre'] = $this->nombre;

        return Reporte::create([
            'empresa_id' => $this->empresa_id,
            'usuario_id' => $usuarioId,
            'nombre' => $this->nombre . ' - ' . now()->format('d/m/Y H:i'),
            'tipo' => $this->tipo,
            'subtipo' => $this->subtipo,
            'filtros_json' => array_merge($this->filtros_defecto_json ?? [], $filtros),
            'configuracion_json' => $configuracion,
            'formato' => $formato ?? $this->formato_defecto,
            'estado' => Reporte::ESTADO_GENERANDO
        ]);
    }

    /**
     * Obtener columnas configuradas
     */
    public function getColumnasConfiguracion(): array
    {
        $columnas = $this->columnas_json;
        
        if (!$columnas) {
            // Columnas por defecto según el tipo
            return $this->getColumnasDefecto();
        }

        return $columnas;
    }

    /**
     * Obtener columnas por defecto según tipo
     */
    private function getColumnasDefecto(): array
    {
        switch ($this->tipo) {
            case Reporte::TIPO_VENTAS:
                return [
                    'fecha' => ['label' => 'Fecha', 'tipo' => 'datetime', 'visible' => true],
                    'numero' => ['label' => 'Número', 'tipo' => 'string', 'visible' => true],
                    'cliente' => ['label' => 'Cliente', 'tipo' => 'string', 'visible' => true],
                    'total' => ['label' => 'Total', 'tipo' => 'currency', 'visible' => true]
                ];
                
            case Reporte::TIPO_PRODUCTOS:
                return [
                    'codigo' => ['label' => 'Código', 'tipo' => 'string', 'visible' => true],
                    'nombre' => ['label' => 'Nombre', 'tipo' => 'string', 'visible' => true],
                    'categoria' => ['label' => 'Categoría', 'tipo' => 'string', 'visible' => true],
                    'stock' => ['label' => 'Stock', 'tipo' => 'number', 'visible' => true],
                    'precio' => ['label' => 'Precio', 'tipo' => 'currency', 'visible' => true]
                ];
                
            case Reporte::TIPO_INVENTARIO:
                return [
                    'producto' => ['label' => 'Producto', 'tipo' => 'string', 'visible' => true],
                    'sucursal' => ['label' => 'Sucursal', 'tipo' => 'string', 'visible' => true],
                    'stock_actual' => ['label' => 'Stock Actual', 'tipo' => 'number', 'visible' => true],
                    'stock_minimo' => ['label' => 'Stock Mínimo', 'tipo' => 'number', 'visible' => true]
                ];
                
            default:
                return [];
        }
    }

    /**
     * Obtener templates del sistema
     */
    public static function getTemplatesSistema(): array
    {
        return [
            [
                'nombre' => 'Ventas por Período',
                'descripcion' => 'Reporte de ventas agrupadas por período',
                'tipo' => Reporte::TIPO_VENTAS,
                'subtipo' => 'ventas_periodo',
                'es_sistema' => true,
                'es_publico' => true,
                'configuracion_json' => [
                    'agrupacion' => 'fecha',
                    'incluir_totales' => true,
                    'incluir_graficos' => true
                ],
                'filtros_defecto_json' => [
                    'fecha_desde' => date('Y-m-01'),
                    'fecha_hasta' => date('Y-m-t')
                ]
            ],
            [
                'nombre' => 'Productos con Stock Bajo',
                'descripcion' => 'Productos que están por debajo del stock mínimo',
                'tipo' => Reporte::TIPO_INVENTARIO,
                'subtipo' => 'stock_bajo',
                'es_sistema' => true,
                'es_publico' => true,
                'configuracion_json' => [
                    'solo_stock_bajo' => true,
                    'incluir_sugerencias' => true
                ]
            ],
            [
                'nombre' => 'Lotes Próximos a Vencer',
                'descripcion' => 'Lotes que vencen en los próximos días',
                'tipo' => Reporte::TIPO_LOTES,
                'subtipo' => 'proximos_vencer',
                'es_sistema' => true,
                'es_publico' => true,
                'configuracion_json' => [
                    'dias_alerta' => 30,
                    'incluir_vencidos' => false
                ]
            ]
        ];
    }

    /**
     * Crear templates del sistema
     */
    public static function crearTemplatesSistema(int $empresaId = null): void
    {
        $templates = self::getTemplatesSistema();
        
        foreach ($templates as $index => $templateData) {
            $templateData['empresa_id'] = $empresaId;
            $templateData['orden_display'] = $index + 1;
            $templateData['activo'] = true;
            
            self::updateOrCreate([
                'nombre' => $templateData['nombre'],
                'tipo' => $templateData['tipo'],
                'empresa_id' => $empresaId
            ], $templateData);
        }
    }
}

