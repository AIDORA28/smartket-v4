<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AnalyticsEvento extends Model
{
    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'sucursal_id',
        'evento',
        'categoria',
        'entidad_tipo',
        'entidad_id',
        'datos_json',
        'metadatos_json',
        'valor_numerico',
        'valor_texto',
        'timestamp_evento',
        'session_id'
    ];

    protected $casts = [
        'datos_json' => 'array',
        'metadatos_json' => 'array',
        'valor_numerico' => 'decimal:4',
        'timestamp_evento' => 'datetime'
    ];

    // Constantes para categorías
    const CATEGORIA_VENTAS = 'VENTAS';
    const CATEGORIA_INVENTARIO = 'INVENTARIO';
    const CATEGORIA_CAJA = 'CAJA';
    const CATEGORIA_USUARIOS = 'USUARIOS';
    const CATEGORIA_SISTEMA = 'SISTEMA';

    // Eventos comunes
    const EVENTO_VENTA_COMPLETADA = 'venta_completada';
    const EVENTO_PRODUCTO_AGOTADO = 'producto_agotado';
    const EVENTO_LOTE_VENCIDO = 'lote_vencido';
    const EVENTO_CAJA_ABIERTA = 'caja_abierta';
    const EVENTO_CAJA_CERRADA = 'caja_cerrada';
    const EVENTO_LOGIN = 'login';
    const EVENTO_LOGOUT = 'logout';
    const EVENTO_REPORTE_GENERADO = 'reporte_generado';

    // =====================================================================
    // RELACIONES
    // =====================================================================

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    // =====================================================================
    // SCOPES
    // =====================================================================

    public function scopeEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeEvento(Builder $query, string $evento): Builder
    {
        return $query->where('evento', $evento);
    }

    public function scopeCategoria(Builder $query, string $categoria): Builder
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeEntidad(Builder $query, string $tipo, string $id = null): Builder
    {
        $query->where('entidad_tipo', $tipo);
        
        if ($id) {
            $query->where('entidad_id', $id);
        }
        
        return $query;
    }

    public function scopeEntreFechas(Builder $query, Carbon $desde, Carbon $hasta): Builder
    {
        return $query->whereBetween('timestamp_evento', [$desde, $hasta]);
    }

    public function scopeHoy(Builder $query): Builder
    {
        return $query->whereDate('timestamp_evento', Carbon::today());
    }

    public function scopeEsteMes(Builder $query): Builder
    {
        return $query->whereBetween('timestamp_evento', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ]);
    }

    public function scopeUltimosDias(Builder $query, int $dias): Builder
    {
        return $query->where('timestamp_evento', '>=', Carbon::now()->subDays($dias));
    }

    // =====================================================================
    // MÉTODOS ESTÁTICOS PARA REGISTRO
    // =====================================================================

    /**
     * Registrar evento de venta
     */
    public static function registrarVenta(int $empresaId, int $ventaId, float $total, ?int $usuarioId = null, ?int $sucursalId = null): void
    {
        self::registrarEvento([
            'empresa_id' => $empresaId,
            'usuario_id' => $usuarioId,
            'sucursal_id' => $sucursalId,
            'evento' => self::EVENTO_VENTA_COMPLETADA,
            'categoria' => self::CATEGORIA_VENTAS,
            'entidad_tipo' => 'venta',
            'entidad_id' => (string)$ventaId,
            'valor_numerico' => $total
        ]);
    }

    /**
     * Registrar evento de producto agotado
     */
    public static function registrarProductoAgotado(int $empresaId, int $productoId, ?int $sucursalId = null): void
    {
        self::registrarEvento([
            'empresa_id' => $empresaId,
            'sucursal_id' => $sucursalId,
            'evento' => self::EVENTO_PRODUCTO_AGOTADO,
            'categoria' => self::CATEGORIA_INVENTARIO,
            'entidad_tipo' => 'producto',
            'entidad_id' => (string)$productoId
        ]);
    }

    /**
     * Registrar evento de lote vencido
     */
    public static function registrarLoteVencido(int $empresaId, int $loteId, float $stockPerdido, ?int $sucursalId = null): void
    {
        self::registrarEvento([
            'empresa_id' => $empresaId,
            'sucursal_id' => $sucursalId,
            'evento' => self::EVENTO_LOTE_VENCIDO,
            'categoria' => self::CATEGORIA_INVENTARIO,
            'entidad_tipo' => 'lote',
            'entidad_id' => (string)$loteId,
            'valor_numerico' => $stockPerdido
        ]);
    }

    /**
     * Registrar evento de caja
     */
    public static function registrarEventoCaja(int $empresaId, string $evento, int $cajaId, float $monto, int $usuarioId, int $sucursalId): void
    {
        self::registrarEvento([
            'empresa_id' => $empresaId,
            'usuario_id' => $usuarioId,
            'sucursal_id' => $sucursalId,
            'evento' => $evento,
            'categoria' => self::CATEGORIA_CAJA,
            'entidad_tipo' => 'caja',
            'entidad_id' => (string)$cajaId,
            'valor_numerico' => $monto
        ]);
    }

    /**
     * Registrar login/logout
     */
    public static function registrarAcceso(int $empresaId, int $usuarioId, string $evento, ?int $sucursalId = null): void
    {
        self::registrarEvento([
            'empresa_id' => $empresaId,
            'usuario_id' => $usuarioId,
            'sucursal_id' => $sucursalId,
            'evento' => $evento,
            'categoria' => self::CATEGORIA_USUARIOS,
            'entidad_tipo' => 'usuario',
            'entidad_id' => (string)$usuarioId,
            'metadatos_json' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]
        ]);
    }

    /**
     * Método genérico para registrar eventos
     */
    private static function registrarEvento(array $datos): void
    {
        $datos['timestamp_evento'] = $datos['timestamp_evento'] ?? Carbon::now();
        $datos['session_id'] = $datos['session_id'] ?? session()->getId();

        self::create($datos);
    }

    // =====================================================================
    // MÉTODOS DE ANALYTICS
    // =====================================================================

    /**
     * Obtener estadísticas de ventas
     */
    public static function getEstadisticasVentas(int $empresaId, Carbon $desde, Carbon $hasta): array
    {
        $eventos = self::empresa($empresaId)
            ->evento(self::EVENTO_VENTA_COMPLETADA)
            ->entreFechas($desde, $hasta)
            ->get();

        return [
            'total_ventas' => $eventos->count(),
            'monto_total' => $eventos->sum('valor_numerico'),
            'promedio_venta' => $eventos->avg('valor_numerico'),
            'ventas_por_dia' => $eventos->groupBy(function($evento) {
                return $evento->timestamp_evento->format('Y-m-d');
            })->map(function($eventosDelDia) {
                return [
                    'cantidad' => $eventosDelDia->count(),
                    'monto' => $eventosDelDia->sum('valor_numerico')
                ];
            })
        ];
    }

    /**
     * Obtener productos más agotados
     */
    public static function getProductosMasAgotados(int $empresaId, int $dias = 30): array
    {
        return self::empresa($empresaId)
            ->evento(self::EVENTO_PRODUCTO_AGOTADO)
            ->ultimosDias($dias)
            ->selectRaw('entidad_id, COUNT(*) as veces_agotado')
            ->groupBy('entidad_id')
            ->orderByDesc('veces_agotado')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Limpiar eventos antiguos
     */
    public static function limpiarEventosAntiguos(int $diasMantener = 90): int
    {
        $fechaLimite = Carbon::now()->subDays($diasMantener);
        
        return self::where('created_at', '<', $fechaLimite)->delete();
    }
}
