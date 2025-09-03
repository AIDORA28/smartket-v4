<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureFlag extends Model
{
    protected $table = 'feature_flags';
    
    protected $fillable = [
        'empresa_id',
        'feature_key',
        'enabled'
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    // Helper methods
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    // Scope methods
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeForFeature($query, string $feature)
    {
        return $query->where('feature_key', $feature);
    }
}
