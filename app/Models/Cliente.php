<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'tipo_documento',
        'numero_documento',
        'nombre',
        'apellidos',
        'razon_social',
        'email',
        'telefono',
        'direccion',
        'distrito',
        'provincia',
        'departamento',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // Relación con empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Scopes
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    // Métodos auxiliares
    public function getNombreCompletoAttribute()
    {
        if ($this->tipo_documento === 'RUC') {
            return $this->razon_social;
        }
        return trim($this->nombre . ' ' . $this->apellidos);
    }

    public function getEsEmpresaAttribute()
    {
        return $this->tipo_documento === 'RUC';
    }
}
