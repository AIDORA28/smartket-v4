<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Reporte extends Model
{
    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'nombre',
        'tipo',
        'subtipo',
        'filtros_json',
        'configuracion_json',
        'formato',
        'estado',
        'archivo_path',
        'total_registros',
        'fecha_generacion',
        'fecha_expiracion',
        'error_mensaje',
        'es_favorito'
    ];

    protected $casts = [
        'filtros_json' => 'array',
        'configuracion_json' => 'array',
        'fecha_generacion' => 'datetime',
        'fecha_expiracion' => 'datetime',
        'total_registros' => 'integer',
        'es_favorito' => 'boolean'
    ];

    // Constantes para tipos de reportes
    const TIPO_VENTAS = 'VENTAS';
    const TIPO_PRODUCTOS = 'PRODUCTOS';
    const TIPO_INVENTARIO = 'INVENTARIO';
    const TIPO_CAJA = 'CAJA';
    const TIPO_COMPRAS = 'COMPRAS';
    const TIPO_LOTES = 'LOTES';
    const TIPO_ANALYTICS = 'ANALYTICS';

    // Constantes para estados
    const ESTADO_GENERANDO = 'GENERANDO';
    const ESTADO_COMPLETADO = 'COMPLETADO';
    const ESTADO_ERROR = 'ERROR';

    // Constantes para formatos
    const FORMATO_HTML = 'HTML';
    const FORMATO_PDF = 'PDF';
    const FORMATO_EXCEL = 'EXCEL';
    const FORMATO_CSV = 'CSV';

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

    // =====================================================================
    // SCOPES
    // =====================================================================

    public function scopeEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }

    public function scopeCompletados(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_COMPLETADO);
    }

    public function scopeFavoritos(Builder $query): Builder
    {
        return $query->where('es_favorito', true);
    }

    public function scopeVigentes(Builder $query): Builder
    {
        return $query->where('fecha_expiracion', '>', Carbon::now())
                    ->orWhereNull('fecha_expiracion');
    }

    public function scopeExpirados(Builder $query): Builder
    {
        return $query->where('fecha_expiracion', '<=', Carbon::now());
    }

    // =====================================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================================

    /**
     * Marcar reporte como completado
     */
    public function marcarCompletado(string $archivoPath, int $totalRegistros): bool
    {
        $this->estado = self::ESTADO_COMPLETADO;
        $this->archivo_path = $archivoPath;
        $this->total_registros = $totalRegistros;
        $this->fecha_generacion = Carbon::now();
        $this->fecha_expiracion = Carbon::now()->addDays(7); // 7 días de vigencia
        $this->error_mensaje = null;

        return $this->save();
    }

    /**
     * Marcar reporte con error
     */
    public function marcarError(string $errorMensaje): bool
    {
        $this->estado = self::ESTADO_ERROR;
        $this->error_mensaje = $errorMensaje;
        $this->fecha_generacion = Carbon::now();

        return $this->save();
    }

    /**
     * Verificar si el reporte está expirado
     */
    public function estaExpirado(): bool
    {
        if (!$this->fecha_expiracion) {
            return false;
        }
        return $this->fecha_expiracion <= Carbon::now();
    }

    /**
     * Verificar si está disponible para descarga
     */
    public function estaDisponible(): bool
    {
        return $this->estado === self::ESTADO_COMPLETADO 
               && !$this->estaExpirado() 
               && $this->archivo_path 
               && file_exists(storage_path('app/' . $this->archivo_path));
    }

    /**
     * Obtener URL de descarga
     */
    public function getUrlDescarga(): ?string
    {
        if (!$this->estaDisponible()) {
            return null;
        }

        return route('reportes.descargar', ['reporte' => $this->id]);
    }

    /**
     * Obtener tamaño del archivo
     */
    public function getTamanoArchivo(): ?string
    {
        if (!$this->archivo_path || !file_exists(storage_path('app/' . $this->archivo_path))) {
            return null;
        }

        $bytes = filesize(storage_path('app/' . $this->archivo_path));
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Alternar favorito
     */
    public function toggleFavorito(): bool
    {
        $this->es_favorito = !$this->es_favorito;
        return $this->save();
    }

    /**
     * Obtener tipos de reporte disponibles
     */
    public static function getTiposDisponibles(): array
    {
        return [
            self::TIPO_VENTAS => 'Reportes de Ventas',
            self::TIPO_PRODUCTOS => 'Reportes de Productos',
            self::TIPO_INVENTARIO => 'Reportes de Inventario',
            self::TIPO_CAJA => 'Reportes de Caja',
            self::TIPO_COMPRAS => 'Reportes de Compras',
            self::TIPO_LOTES => 'Reportes de Lotes',
            self::TIPO_ANALYTICS => 'Reportes de Analytics'
        ];
    }

    /**
     * Obtener formatos disponibles
     */
    public static function getFormatosDisponibles(): array
    {
        return [
            self::FORMATO_HTML => 'Visualización HTML',
            self::FORMATO_PDF => 'Documento PDF',
            self::FORMATO_EXCEL => 'Hoja de Excel',
            self::FORMATO_CSV => 'Archivo CSV'
        ];
    }

    /**
     * Limpiar reportes expirados
     */
    public static function limpiarExpirados(): int
    {
        $reportesExpirados = self::expirados()->get();
        $eliminados = 0;

        foreach ($reportesExpirados as $reporte) {
            // Eliminar archivo físico
            if ($reporte->archivo_path && file_exists(storage_path('app/' . $reporte->archivo_path))) {
                unlink(storage_path('app/' . $reporte->archivo_path));
            }
            
            // Eliminar registro
            $reporte->delete();
            $eliminados++;
        }

        return $eliminados;
    }
}

