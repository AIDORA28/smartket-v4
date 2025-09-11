<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Roles base disponibles siempre
     */
    const ROLES_BASE = [
        'admin' => 'Administrador de Sucursal',
        'vendedor' => 'Vendedor',
        'cajero' => 'Cajero',
        'almacenero' => 'Almacenero'
    ];

    /**
     * Roles premium que requieren addons específicos
     */
    const ROLES_PREMIUM = [
        'subgerente' => 'Subgerente',  // Requiere addon sucursales
        'gerente' => 'Gerente'         // Requiere addon negocios
    ];

    /**
     * Rol especial del propietario
     */
    const ROLE_OWNER = 'owner';

    protected $fillable = [
        'name',
        'empresa_id',
        'sucursal_id',
        'empresa_activa_id',
        'sucursal_activa_id',
        'email',
        'password_hash',
        'rol_principal',
        'activo',
        'ultimo_cambio_contexto'
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
        'ultimo_cambio_contexto' => 'datetime',
    ];

    // Relationships - Legacy (mantener compatibilidad)
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Relaciones de contexto activo
    public function empresaActiva(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_activa_id');
    }

    public function sucursalActiva(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_activa_id');
    }

    // Relaciones multi-tenant
    public function empresasAccesibles()
    {
        return $this->belongsToMany(Empresa::class, 'user_empresa_accesos')
                    ->using(UserEmpresaAcceso::class)
                    ->withPivot(['rol_en_empresa', 'activo'])
                    ->withTimestamps()
                    ->wherePivot('activo', true)
                    ->where('empresas.activa', true)
                    ->select(['empresas.id', 'empresas.nombre', 'empresas.logo']);
    }

    public function sucursalesAccesibles()
    {
        return $this->belongsToMany(Sucursal::class, 'user_sucursal_accesos')
                    ->using(UserSucursalAcceso::class)
                    ->withPivot(['rol_en_sucursal', 'activo'])
                    ->withTimestamps()
                    ->wherePivot('activo', true)
                    ->where('sucursales.activa', true)
                    ->select(['sucursales.id', 'sucursales.nombre']);
    }

    // Accesos específicos (incluye las pivot tables)
    public function empresaAccesos()
    {
        return $this->hasMany(UserEmpresaAcceso::class)->where('activo', true);
    }

    public function sucursalAccesos()
    {
        return $this->hasMany(UserSucursalAcceso::class)->where('activo', true);
    }

    // Role helpers
    /**
     * Obtener todos los roles disponibles (base + premium)
     */
    public static function getAllRoles()
    {
        return array_merge(self::ROLES_BASE, self::ROLES_PREMIUM);
    }

    /**
     * Verificar si el usuario es un Owner
     */
    public function isOwner(): bool
    {
        return $this->rol_principal === self::ROLE_OWNER;
    }

    /**
     * Verificar si el usuario es un Administrador (incluyendo owner)
     */
    public function isAdmin(): bool
    {
        return in_array($this->rol_principal, ['owner', 'admin']);
    }

    /**
     * Verificar si el usuario puede gestionar stock
     */
    public function canManageStock(): bool
    {
        return in_array($this->rol_principal, ['owner', 'admin', 'almacenero']);
    }

    /**
     * Verificar si el usuario puede vender
     */
    public function canSell(): bool
    {
        return in_array($this->rol_principal, ['owner', 'admin', 'cajero', 'vendedor']);
    }

    /**
     * Verificar si un rol es premium y requiere addon
     */
    public static function isRolePremium($role): bool
    {
        return array_key_exists($role, self::ROLES_PREMIUM);
    }

    /**
     * Verificar si el rol está disponible para la empresa
     */
    public function isRoleAvailableForEmpresa($role): bool
    {
        // Rol owner siempre disponible para asignación inicial
        if ($role === self::ROLE_OWNER) {
            return true;
        }

        // Roles base siempre disponibles
        if (array_key_exists($role, self::ROLES_BASE)) {
            return true;
        }

        // Verificar roles premium
        if (array_key_exists($role, self::ROLES_PREMIUM)) {
            $empresa = $this->empresa;
            
            if (!$empresa) {
                return false;
            }
            
            // Gerente requiere addon negocios
            if ($role === 'gerente') {
                return $empresa->hasAddon('negocios');
            }
            
            // Subgerente requiere addon sucursales
            if ($role === 'subgerente') {
                return $empresa->hasAddon('sucursales');
            }
        }

        return false;
    }

    /**
     * Validar si el usuario puede asignar roles
     */
    public function canAssignRole($roleToAssign): bool
    {
        // Solo el Owner puede asignar roles
        if (!$this->isOwner()) {
            return false;
        }

        // Verificar que el rol esté disponible para la empresa
        return $this->isRoleAvailableForEmpresa($roleToAssign);
    }

    /**
     * Obtener límite de usuarios según plan de empresa
     */
    public function getUserLimitForEmpresa(): int
    {
        if (!$this->empresa) {
            return 0;
        }

        $plan = $this->empresa->plan;
        if (!$plan) {
            return 1; // Solo el owner
        }

        return $plan->max_usuarios ?? 1; // Usar max_usuarios en lugar de limite_usuarios
    }

    /**
     * Verificar si se puede crear un nuevo usuario en la empresa
     */
    public function canCreateNewUser(): bool
    {
        if (!$this->isOwner()) {
            return false;
        }

        $currentUserCount = User::where('empresa_id', $this->empresa_id)
                               ->where('activo', true)
                               ->count();

        return $currentUserCount < $this->getUserLimitForEmpresa();
    }

    /**
     * Obtener roles disponibles para asignar basado en addons
     */
    public function getAvailableRolesForAssignment(): array
    {
        $availableRoles = self::ROLES_BASE;
        
        if ($this->empresa) {
            // Agregar roles premium si tienen los addons
            if ($this->empresa->hasAddon('sucursales')) {
                $availableRoles['subgerente'] = self::ROLES_PREMIUM['subgerente'];
            }
            
            if ($this->empresa->hasAddon('negocios')) {
                $availableRoles['gerente'] = self::ROLES_PREMIUM['gerente'];
            }
        }

        return $availableRoles;
    }

    /**
     * Obtener información del rol actual
     */
    public function getRoleInfo(): array
    {
        $allRoles = self::getAllRoles();
        
        if ($this->rol_principal === self::ROLE_OWNER) {
            return [
                'key' => self::ROLE_OWNER,
                'name' => 'Propietario',
                'type' => 'owner',
                'permissions' => ['all']
            ];
        }

        if (array_key_exists($this->rol_principal, $allRoles)) {
            return [
                'key' => $this->rol_principal,
                'name' => $allRoles[$this->rol_principal],
                'type' => self::isRolePremium($this->rol_principal) ? 'premium' : 'base',
                'permissions' => $this->getRolePermissions()
            ];
        }

        return [
            'key' => 'unknown',
            'name' => 'Rol desconocido',
            'type' => 'unknown',
            'permissions' => []
        ];
    }

    /**
     * Obtener permisos del rol actual
     */
    private function getRolePermissions(): array
    {
        $permissions = [];

        switch ($this->rol_principal) {
            case 'admin':
                $permissions = ['manage_users', 'manage_stock', 'sell', 'reports', 'manage_branch'];
                break;
            case 'vendedor':
                $permissions = ['sell', 'view_products'];
                break;
            case 'cajero':
                $permissions = ['sell', 'manage_cash'];
                break;
            case 'almacenero':
                $permissions = ['manage_stock', 'view_inventory'];
                break;
            case 'subgerente':
                $permissions = ['manage_users', 'manage_stock', 'sell', 'reports', 'manage_multiple_branches'];
                break;
            case 'gerente':
                $permissions = ['manage_users', 'manage_stock', 'sell', 'reports', 'manage_multiple_businesses'];
                break;
        }

        return $permissions;
    }

    // =====================================
    // MÉTODOS MULTI-TENANT
    // =====================================

    /**
     * Verificar si el usuario tiene acceso a una empresa específica
     */
    public function canAccessEmpresa(int $empresaId): bool
    {
        // Verificar acceso directo (legacy)
        if ($this->empresa_id == $empresaId) {
            return true;
        }

        // Verificar acceso multi-tenant
        return $this->empresasAccesibles()->where('empresas.id', $empresaId)->exists();
    }

    /**
     * Verificar si el usuario tiene acceso a una sucursal específica
     */
    public function canAccessSucursal(int $sucursalId): bool
    {
        // Verificar acceso directo (legacy)
        if ($this->sucursal_id == $sucursalId) {
            return true;
        }

        // Verificar acceso multi-tenant
        return $this->sucursalesAccesibles()->where('sucursales.id', $sucursalId)->exists();
    }

    /**
     * Obtener el rol del usuario en una empresa específica
     */
    public function getRolInEmpresa(int $empresaId): ?string
    {
        // Verificar acceso legacy primero
        if ($this->empresa_id == $empresaId) {
            return $this->rol_principal;
        }

        // Verificar acceso multi-tenant
        $acceso = $this->empresaAccesos()->where('empresa_id', $empresaId)->first();
        return $acceso ? $acceso->rol_en_empresa : null;
    }

    /**
     * Obtener el rol del usuario en una sucursal específica
     */
    public function getRolInSucursal(int $sucursalId): ?string
    {
        // Verificar acceso legacy primero
        if ($this->sucursal_id == $sucursalId) {
            return $this->rol_principal;
        }

        // Verificar acceso multi-tenant
        $acceso = $this->sucursalAccesos()->where('sucursal_id', $sucursalId)->first();
        return $acceso ? $acceso->rol_en_sucursal : null;
    }

    /**
     * Cambiar contexto activo (empresa y sucursal)
     */
    public function switchContext(?int $empresaId = null, ?int $sucursalId = null): bool
    {
        // Verificar permisos de acceso
        if ($empresaId && !$this->canAccessEmpresa($empresaId)) {
            return false;
        }

        if ($sucursalId && !$this->canAccessSucursal($sucursalId)) {
            return false;
        }

        // Actualizar contexto
        $this->update([
            'empresa_activa_id' => $empresaId ?? $this->empresa_activa_id,
            'sucursal_activa_id' => $sucursalId ?? $this->sucursal_activa_id,
            'ultimo_cambio_contexto' => now()
        ]);

        return true;
    }

    /**
     * Verificar si necesita seleccionar contexto (múltiples accesos)
     */
    public function needsContextSelection(): bool
    {
        $empresasCount = $this->empresasAccesibles()->count();
        $sucursalesCount = $this->sucursalesAccesibles()->count();

        // Si tiene acceso a múltiples empresas o sucursales, necesita seleccionar
        return $empresasCount > 1 || $sucursalesCount > 1;
    }

    /**
     * Obtener contexto actual o por defecto
     */
    public function getCurrentContext(): array
    {
        $empresaActual = $this->empresaActiva ?? $this->empresa;
        $sucursalActual = $this->sucursalActiva ?? $this->sucursal;

        return [
            'empresa' => $empresaActual ? [
                'id' => $empresaActual->id,
                'nombre' => $empresaActual->nombre,
                'rol' => $this->getRolInEmpresa($empresaActual->id)
            ] : null,
            'sucursal' => $sucursalActual ? [
                'id' => $sucursalActual->id,
                'nombre' => $sucursalActual->nombre,
                'rol' => $this->getRolInSucursal($sucursalActual->id)
            ] : null,
            'needs_selection' => $this->needsContextSelection()
        ];
    }

    /**
     * Agregar acceso a empresa
     */
    public function grantEmpresaAccess(int $empresaId, string $rol = 'admin'): bool
    {
        try {
            $this->empresaAccesos()->updateOrCreate(
                ['empresa_id' => $empresaId],
                ['rol_en_empresa' => $rol, 'activo' => true]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Agregar acceso a sucursal
     */
    public function grantSucursalAccess(int $sucursalId, string $rol = 'vendedor'): bool
    {
        try {
            $this->sucursalAccesos()->updateOrCreate(
                ['sucursal_id' => $sucursalId],
                ['rol_en_sucursal' => $rol, 'activo' => true]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Revocar acceso a empresa
     */
    public function revokeEmpresaAccess(int $empresaId): bool
    {
        return $this->empresaAccesos()
                   ->where('empresa_id', $empresaId)
                   ->update(['activo' => false]) > 0;
    }

    /**
     * Revocar acceso a sucursal
     */
    public function revokeSucursalAccess(int $sucursalId): bool
    {
        return $this->sucursalAccesos()
                   ->where('sucursal_id', $sucursalId)
                   ->update(['activo' => false]) > 0;
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

