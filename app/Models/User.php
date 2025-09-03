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
        'nombre',
        'password_hash',
        'rol_principal',
        'activo'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

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

    // Authentication
    public function getAuthPassword()
    {
        return $this->password_hash;
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
