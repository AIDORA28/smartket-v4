<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserEmpresaAcceso extends Pivot
{
    protected $table = 'user_empresa_accesos';
    
    protected $fillable = [
        'user_id',
        'empresa_id',
        'rol_en_empresa',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('rol_en_empresa', $role);
    }

    // Métodos de utilidad
    public function isOwner()
    {
        return $this->rol_en_empresa === 'owner';
    }

    public function isGerente()
    {
        return $this->rol_en_empresa === 'gerente';
    }

    public function isAdmin()
    {
        return $this->rol_en_empresa === 'admin';
    }
}
