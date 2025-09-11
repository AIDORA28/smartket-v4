<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSucursalAcceso extends Pivot
{
    protected $table = 'user_sucursal_accesos';
    
    protected $fillable = [
        'user_id',
        'sucursal_id',
        'rol_en_sucursal',
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

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('rol_en_sucursal', $role);
    }

    // Métodos de utilidad
    public function isAdmin()
    {
        return $this->rol_en_sucursal === 'admin';
    }

    public function isSubgerente()
    {
        return $this->rol_en_sucursal === 'subgerente';
    }

    public function canManageStock()
    {
        return in_array($this->rol_en_sucursal, ['admin', 'subgerente', 'almacenero']);
    }

    public function canSell()
    {
        return in_array($this->rol_en_sucursal, ['admin', 'subgerente', 'vendedor', 'cajero']);
    }
}
