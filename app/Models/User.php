<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'empresa_id',
        'sucursal_id',
        'email',
        'password_hash',
        'rol_principal',
        'activo'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // Authentication
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    // Laravel expects 'password' attribute, map to password_hash
    public function getPasswordAttribute()
    {
        return $this->password_hash;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = bcrypt($value);
    }

    protected $casts = [
        'activo' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Method to get accessible companies for multi-tenant support
    public function empresasAccesibles()
    {
        // For now, return the user's assigned company
        // In the future, this could be expanded for multi-company access
        if ($this->empresa_id) {
            return Empresa::where('id', $this->empresa_id)->where('activa', true);
        }
        return Empresa::whereRaw('1 = 0'); // Return empty query
    }

    // Role helpers
    public function isOwner(): bool
    {
        return $this->rol_principal === 'owner';
    }

    public function isAdmin(): bool
    {
        return in_array($this->rol_principal, ['owner', 'admin']);
    }

    public function canManageStock(): bool
    {
        return in_array($this->rol_principal, ['owner', 'admin', 'almacenero']);
    }

    public function canSell(): bool
    {
        return in_array($this->rol_principal, ['owner', 'admin', 'cajero', 'vendedor']);
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
