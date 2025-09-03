<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaRubro extends Model
{
    protected $table = 'empresa_rubros';
    
    protected $fillable = [
        'empresa_id',
        'rubro_id',
        'es_principal',
        'configuracion_json'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'configuracion_json' => 'array',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function rubro(): BelongsTo
    {
        return $this->belongsTo(Rubro::class);
    }

    // Helper methods
    public function isPrincipal(): bool
    {
        return $this->es_principal;
    }

    public function getConfiguracion(string $key, $default = null)
    {
        return $this->configuracion_json[$key] ?? $default;
    }
}
