# 🚀 SmartKet ERP - Especificación Backend

**Versión:** 1.0  
**Fecha:** 30 Agosto 2025  
**Estado:** 📋 ESPECIFICACIÓN TÉCNICA DETALLADA  

---

## 🎯 **ARQUITECTURA BACKEND**

### **🏗️ Stack Backend**
```yaml
Framework: Laravel 11.45+
PHP: PHP 8.3+
Database: MySQL 8.0+
Cache: Redis
Queue: Redis + Laravel Horizon
Search: Laravel Scout (opcional)
Storage: Local/S3 (configurable)
Authentication: Laravel Sanctum
Authorization: Spatie Permission
```

### **📁 Estructura de Directorios**
```
app/
├── Http/
│   ├── Controllers/             # API + Web Controllers
│   ├── Livewire/               # Livewire Components
│   ├── Middleware/             # Custom middleware
│   ├── Requests/               # Form request validation
│   └── Resources/              # API resources (futuro)
├── Models/                     # Eloquent models
├── Services/                   # Business logic services
├── Traits/                     # Reusable model traits
├── Observers/                  # Model event observers
├── Helpers/                    # Helper classes
├── Enums/                      # PHP enums (futuro)
└── Exceptions/                 # Custom exceptions
```

---

## 📊 **MODELOS ELOQUENT**

### **🏢 Modelos Core**

#### **Empresa.php**
```php
<?php
namespace App\Models;

class Empresa extends Model
{
    protected $fillable = [
        'nombre', 'ruc', 'tiene_ruc', 'tipo_rubro', 'plan_id',
        'features_json', 'sucursales_enabled', 'sucursales_count',
        'allow_negative_stock', 'precio_incluye_igv', 'timezone'
    ];

    protected $casts = [
        'features_json' => 'array',
        'tiene_ruc' => 'boolean',
        'sucursales_enabled' => 'boolean',
        'allow_negative_stock' => 'boolean',
        'precio_incluye_igv' => 'boolean',
    ];

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function featureFlags()
    {
        return $this->hasMany(FeatureFlag::class);
    }

    // Helper methods
    public function hasFeature(string $feature): bool
    {
        return $this->featureFlags()
            ->where('feature_key', $feature)
            ->where('enabled', true)
            ->exists();
    }

    public function getSucursalPrincipal()
    {
        return $this->sucursales()->first();
    }
}
```

#### **Usuario.php**
```php
<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasEmpresaScope;

    protected $fillable = [
        'empresa_id', 'sucursal_id', 'email', 'nombre', 
        'password_hash', 'rol_principal', 'activo'
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'activo' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Authentication
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relationships
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
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
}
```

#### **Producto.php**
```php
<?php
namespace App\Models;

class Producto extends Model
{
    use HasEmpresaScope;

    protected $fillable = [
        'empresa_id', 'nombre', 'codigo', 'sku', 'tipo_base',
        'unidad', 'precio_base', 'moneda', 'tax_category',
        'controla_lote', 'vida_util_dias', 'es_variantes',
        'atributos_basicos_json', 'reorder_point', 'activo'
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'controla_lote' => 'boolean',
        'es_variantes' => 'boolean',
        'activo' => 'boolean',
        'atributos_basicos_json' => 'array',
    ];

    // Relationships
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'producto_categoria');
    }

    public function stocks()
    {
        return $this->hasMany(ProductoSucursalStock::class);
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    public function ventaItems()
    {
        return $this->hasMany(VentaItem::class);
    }

    public function compraItems()
    {
        return $this->hasMany(CompraItem::class);
    }

    public function movimientosInventario()
    {
        return $this->hasMany(InventarioMov::class);
    }

    // Helper methods
    public function getStockEnSucursal($sucursal_id, $lote_id = null)
    {
        return $this->stocks()
            ->where('sucursal_id', $sucursal_id)
            ->where('lote_id', $lote_id)
            ->first()?->stock_actual ?? 0;
    }

    public function getTotalStock()
    {
        return $this->stocks()->sum('stock_actual');
    }

    public function needsReorder($sucursal_id = null): bool
    {
        if (!$this->reorder_point) return false;

        $stock = $sucursal_id 
            ? $this->getStockEnSucursal($sucursal_id)
            : $this->getTotalStock();

        return $stock <= $this->reorder_point;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($producto) {
            if (!$producto->codigo) {
                $producto->codigo = static::generateProductCode($producto->empresa_id);
            }
        });
    }

    public static function generateProductCode($empresa_id): string
    {
        $lastProduct = static::where('empresa_id', $empresa_id)
            ->whereNotNull('codigo')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastProduct 
            ? (int) substr($lastProduct->codigo, -6) + 1 
            : 1;

        return 'PRO-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
```

### **💰 Modelos de Ventas**

#### **Venta.php**
```php
<?php
namespace App\Models;

class Venta extends Model
{
    use HasEmpresaScope;

    protected $fillable = [
        'empresa_id', 'sucursal_id', 'usuario_id', 'cliente_id',
        'fecha', 'tipo_doc', 'estado', 'total_bruto',
        'total_descuento_items', 'descuento_global_tipo',
        'descuento_global_valor', 'total_descuento_global',
        'total_descuento', 'total_neto', 'total_base',
        'total_igv', 'moneda', 'es_electronico',
        'cancel_reason', 'referencia_externa', 'tasa_cambio'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total_bruto' => 'decimal:2',
        'total_descuento_items' => 'decimal:2',
        'descuento_global_valor' => 'decimal:2',
        'total_descuento_global' => 'decimal:2',
        'total_descuento' => 'decimal:2',
        'total_neto' => 'decimal:2',
        'total_base' => 'decimal:2',
        'total_igv' => 'decimal:2',
        'es_electronico' => 'boolean',
        'tasa_cambio' => 'decimal:6',
    ];

    // Relationships
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items()
    {
        return $this->hasMany(VentaItem::class);
    }

    public function pagos()
    {
        return $this->hasMany(VentaPago::class);
    }

    // Estado helpers
    public function isBorrador(): bool
    {
        return $this->estado === 'borrador';
    }

    public function isEmitida(): bool
    {
        return $this->estado === 'emitida';
    }

    public function isAnulada(): bool
    {
        return $this->estado === 'anulada';
    }

    public function canEdit(): bool
    {
        return $this->isBorrador();
    }

    public function canCancel(): bool
    {
        return $this->isEmitida();
    }

    // Business methods
    public function calcularTotales(): void
    {
        $this->total_bruto = $this->items->sum('subtotal_bruto');
        $this->total_descuento_items = $this->items->sum('subtotal_descuento');
        
        // Descuento global
        if ($this->descuento_global_tipo && $this->descuento_global_valor) {
            $base = $this->total_bruto - $this->total_descuento_items;
            $this->total_descuento_global = $this->descuento_global_tipo === 'pct'
                ? $base * ($this->descuento_global_valor / 100)
                : $this->descuento_global_valor;
        }

        $this->total_descuento = $this->total_descuento_items + $this->total_descuento_global;
        $this->total_neto = $this->total_bruto - $this->total_descuento;
        
        // IGV calculation (18%)
        $this->total_base = $this->total_neto / 1.18;
        $this->total_igv = $this->total_neto - $this->total_base;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($venta) {
            if (!$venta->fecha) {
                $venta->fecha = now();
            }
        });
    }
}
```

#### **VentaItem.php**
```php
<?php
namespace App\Models;

class VentaItem extends Model
{
    protected $table = 'venta_items';

    protected $fillable = [
        'empresa_id', 'venta_id', 'producto_id', 'variante_id',
        'lote_id', 'cantidad', 'precio_unit', 'descuento_tipo',
        'descuento_valor', 'subtotal_bruto', 'subtotal_descuento',
        'subtotal_neto', 'line_base', 'line_igv'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio_unit' => 'decimal:2',
        'descuento_valor' => 'decimal:4',
        'subtotal_bruto' => 'decimal:2',
        'subtotal_descuento' => 'decimal:2',
        'subtotal_neto' => 'decimal:2',
        'line_base' => 'decimal:2',
        'line_igv' => 'decimal:2',
    ];

    // Relationships
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    // Business methods
    public function calcularSubtotales(): void
    {
        $this->subtotal_bruto = $this->cantidad * $this->precio_unit;
        
        // Descuento
        if ($this->descuento_tipo && $this->descuento_valor) {
            $this->subtotal_descuento = $this->descuento_tipo === 'pct'
                ? $this->subtotal_bruto * ($this->descuento_valor / 100)
                : $this->descuento_valor;
        }

        $this->subtotal_neto = $this->subtotal_bruto - $this->subtotal_descuento;
        
        // IGV calculation
        $this->line_base = $this->subtotal_neto / 1.18;
        $this->line_igv = $this->subtotal_neto - $this->line_base;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            $item->calcularSubtotales();
        });
    }
}
```

---

## 🛠️ **SERVICIOS DE NEGOCIO**

### **🏢 TenantService.php**
```php
<?php
namespace App\Services;

class TenantService
{
    private ?Empresa $currentEmpresa = null;

    public function setEmpresa(int $empresaId): void
    {
        $this->currentEmpresa = Empresa::findOrFail($empresaId);
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->currentEmpresa;
    }

    public function getEmpresaId(): ?int
    {
        return $this->currentEmpresa?->id;
    }

    public function hasFeature(string $feature): bool
    {
        if (!$this->currentEmpresa) {
            return false;
        }

        return $this->currentEmpresa->hasFeature($feature);
    }

    public function validateEmpresaAccess(int $empresaId): bool
    {
        return $this->currentEmpresa && $this->currentEmpresa->id === $empresaId;
    }
}
```

### **🚩 FeatureFlagService.php**
```php
<?php
namespace App\Services;

class FeatureFlagService
{
    public function isEnabled(string $feature, ?int $empresaId = null): bool
    {
        $empresaId = $empresaId ?: app(TenantService::class)->getEmpresaId();
        
        if (!$empresaId) {
            return false;
        }

        return FeatureFlag::where('empresa_id', $empresaId)
            ->where('feature_key', $feature)
            ->where('enabled', true)
            ->exists();
    }

    public function enable(string $feature, int $empresaId): void
    {
        FeatureFlag::updateOrCreate(
            ['empresa_id' => $empresaId, 'feature_key' => $feature],
            ['enabled' => true]
        );
    }

    public function disable(string $feature, int $empresaId): void
    {
        FeatureFlag::updateOrCreate(
            ['empresa_id' => $empresaId, 'feature_key' => $feature],
            ['enabled' => false]
        );
    }

    public function getEnabledFeatures(int $empresaId): array
    {
        return FeatureFlag::where('empresa_id', $empresaId)
            ->where('enabled', true)
            ->pluck('feature_key')
            ->toArray();
    }
}
```

### **📦 ProductoService.php**
```php
<?php
namespace App\Services;

class ProductoService
{
    public function crear(array $data): Producto
    {
        $data['empresa_id'] = app(TenantService::class)->getEmpresaId();
        
        $producto = Producto::create($data);
        
        // Crear stock inicial en sucursal principal
        $sucursalPrincipal = app(TenantService::class)->getEmpresa()->getSucursalPrincipal();
        
        ProductoSucursalStock::create([
            'empresa_id' => $producto->empresa_id,
            'producto_id' => $producto->id,
            'sucursal_id' => $sucursalPrincipal->id,
            'stock_actual' => 0,
        ]);

        return $producto;
    }

    public function ajustarStock(int $productoId, int $sucursalId, float $nuevaCantidad, string $motivo = 'ajuste'): void
    {
        $empresaId = app(TenantService::class)->getEmpresaId();
        
        $stock = ProductoSucursalStock::firstOrCreate([
            'empresa_id' => $empresaId,
            'producto_id' => $productoId,
            'sucursal_id' => $sucursalId,
            'lote_id' => null,
        ], [
            'stock_actual' => 0,
        ]);

        $cantidadAnterior = $stock->stock_actual;
        $diferencia = $nuevaCantidad - $cantidadAnterior;

        if ($diferencia != 0) {
            // Actualizar stock
            $stock->update(['stock_actual' => $nuevaCantidad]);

            // Registrar movimiento
            InventarioMov::create([
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalId,
                'producto_id' => $productoId,
                'tipo' => $diferencia > 0 ? 'in' : 'out',
                'referencia_tipo' => 'ajuste',
                'referencia_id' => 0,
                'cantidad' => abs($diferencia),
                'fecha' => now(),
            ]);
        }
    }

    public function transferirStock(int $productoId, int $sucursalOrigenId, int $sucursalDestinoId, float $cantidad): void
    {
        if (!app(TenantService::class)->hasFeature('multi_sucursal')) {
            throw new \Exception('Multi-sucursal no está habilitado');
        }

        $empresaId = app(TenantService::class)->getEmpresaId();

        DB::transaction(function () use ($empresaId, $productoId, $sucursalOrigenId, $sucursalDestinoId, $cantidad) {
            // Verificar stock origen
            $stockOrigen = ProductoSucursalStock::where([
                'empresa_id' => $empresaId,
                'producto_id' => $productoId,
                'sucursal_id' => $sucursalOrigenId,
            ])->first();

            if (!$stockOrigen || $stockOrigen->stock_actual < $cantidad) {
                throw new \Exception('Stock insuficiente en sucursal origen');
            }

            // Reducir stock origen
            $stockOrigen->decrement('stock_actual', $cantidad);

            // Aumentar stock destino
            $stockDestino = ProductoSucursalStock::firstOrCreate([
                'empresa_id' => $empresaId,
                'producto_id' => $productoId,
                'sucursal_id' => $sucursalDestinoId,
                'lote_id' => null,
            ], ['stock_actual' => 0]);

            $stockDestino->increment('stock_actual', $cantidad);

            // Registrar movimientos
            InventarioMov::create([
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalOrigenId,
                'producto_id' => $productoId,
                'tipo' => 'transfer_out',
                'referencia_tipo' => 'transferencia',
                'referencia_id' => 0,
                'cantidad' => $cantidad,
                'fecha' => now(),
            ]);

            InventarioMov::create([
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalDestinoId,
                'producto_id' => $productoId,
                'tipo' => 'transfer_in',
                'referencia_tipo' => 'transferencia',
                'referencia_id' => 0,
                'cantidad' => $cantidad,
                'fecha' => now(),
            ]);
        });
    }
}
```

### **💰 VentaService.php**
```php
<?php
namespace App\Services;

class VentaService
{
    public function crear(array $data): Venta
    {
        return DB::transaction(function () use ($data) {
            $empresaId = app(TenantService::class)->getEmpresaId();
            
            $venta = Venta::create([
                'empresa_id' => $empresaId,
                'sucursal_id' => $data['sucursal_id'],
                'usuario_id' => auth()->id(),
                'cliente_id' => $data['cliente_id'] ?? null,
                'tipo_doc' => $data['tipo_doc'] ?? 'interno',
                'estado' => 'borrador',
                'fecha' => $data['fecha'] ?? now(),
            ]);

            foreach ($data['items'] as $itemData) {
                $this->agregarItem($venta, $itemData);
            }

            $venta->calcularTotales();
            $venta->save();

            return $venta;
        });
    }

    public function agregarItem(Venta $venta, array $itemData): VentaItem
    {
        $item = VentaItem::create([
            'empresa_id' => $venta->empresa_id,
            'venta_id' => $venta->id,
            'producto_id' => $itemData['producto_id'],
            'lote_id' => $itemData['lote_id'] ?? null,
            'cantidad' => $itemData['cantidad'],
            'precio_unit' => $itemData['precio_unit'],
            'descuento_tipo' => $itemData['descuento_tipo'] ?? null,
            'descuento_valor' => $itemData['descuento_valor'] ?? null,
        ]);

        return $item;
    }

    public function procesar(Venta $venta): void
    {
        if (!$venta->canEdit()) {
            throw new \Exception('La venta no se puede procesar');
        }

        DB::transaction(function () use ($venta) {
            // Verificar stock disponible
            foreach ($venta->items as $item) {
                $stockDisponible = $this->getStockDisponible(
                    $item->producto_id, 
                    $venta->sucursal_id, 
                    $item->lote_id
                );

                if ($stockDisponible < $item->cantidad) {
                    throw new \Exception("Stock insuficiente para {$item->producto->nombre}");
                }
            }

            // Actualizar stock
            foreach ($venta->items as $item) {
                $this->reducirStock($item);
            }

            // Cambiar estado
            $venta->update(['estado' => 'emitida']);
        });
    }

    private function getStockDisponible(int $productoId, int $sucursalId, ?int $loteId = null): float
    {
        $stock = ProductoSucursalStock::where([
            'producto_id' => $productoId,
            'sucursal_id' => $sucursalId,
            'lote_id' => $loteId,
        ])->first();

        return $stock?->stock_actual ?? 0;
    }

    private function reducirStock(VentaItem $item): void
    {
        $stock = ProductoSucursalStock::where([
            'producto_id' => $item->producto_id,
            'sucursal_id' => $item->venta->sucursal_id,
            'lote_id' => $item->lote_id,
        ])->first();

        if ($stock) {
            $stock->decrement('stock_actual', $item->cantidad);

            // Registrar movimiento
            InventarioMov::create([
                'empresa_id' => $item->empresa_id,
                'sucursal_id' => $item->venta->sucursal_id,
                'producto_id' => $item->producto_id,
                'lote_id' => $item->lote_id,
                'tipo' => 'out',
                'referencia_tipo' => 'venta',
                'referencia_id' => $item->venta_id,
                'cantidad' => $item->cantidad,
                'fecha' => $item->venta->fecha,
            ]);
        }
    }

    public function anular(Venta $venta, string $motivo): void
    {
        if (!$venta->canCancel()) {
            throw new \Exception('La venta no se puede anular');
        }

        DB::transaction(function () use ($venta, $motivo) {
            // Devolver stock
            foreach ($venta->items as $item) {
                $this->devolverStock($item);
            }

            // Anular venta
            $venta->update([
                'estado' => 'anulada',
                'cancel_reason' => $motivo,
            ]);
        });
    }

    private function devolverStock(VentaItem $item): void
    {
        $stock = ProductoSucursalStock::firstOrCreate([
            'empresa_id' => $item->empresa_id,
            'producto_id' => $item->producto_id,
            'sucursal_id' => $item->venta->sucursal_id,
            'lote_id' => $item->lote_id,
        ], ['stock_actual' => 0]);

        $stock->increment('stock_actual', $item->cantidad);

        // Registrar movimiento
        InventarioMov::create([
            'empresa_id' => $item->empresa_id,
            'sucursal_id' => $item->venta->sucursal_id,
            'producto_id' => $item->producto_id,
            'lote_id' => $item->lote_id,
            'tipo' => 'in',
            'referencia_tipo' => 'venta_anulada',
            'referencia_id' => $item->venta_id,
            'cantidad' => $item->cantidad,
            'fecha' => now(),
        ]);
    }
}
```

---

## 🔒 **MIDDLEWARES**

### **🏢 EmpresaScope.php**
```php
<?php
namespace App\Http\Middleware;

class EmpresaScope
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            app(TenantService::class)->setEmpresa($user->empresa_id);
        }

        return $next($request);
    }
}
```

### **🚩 FeatureGuard.php**
```php
<?php
namespace App\Http\Middleware;

class FeatureGuard
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        if (!app(FeatureFlagService::class)->isEnabled($feature)) {
            abort(403, "La funcionalidad '$feature' no está disponible en su plan");
        }

        return $next($request);
    }
}
```

---

## 📊 **OBSERVERS**

### **🎯 VentaItemObserver.php**
```php
<?php
namespace App\Observers;

class VentaItemObserver
{
    public function created(VentaItem $item): void
    {
        $this->recalcularTotalesVenta($item->venta);
    }

    public function updated(VentaItem $item): void
    {
        $this->recalcularTotalesVenta($item->venta);
    }

    public function deleted(VentaItem $item): void
    {
        $this->recalcularTotalesVenta($item->venta);
    }

    private function recalcularTotalesVenta(Venta $venta): void
    {
        $venta->calcularTotales();
        $venta->saveQuietly(); // No triggear observers
    }
}
```

---

## 🎯 **TRAITS**

### **🏢 HasEmpresaScope.php**
```php
<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasEmpresaScope
{
    protected static function bootHasEmpresaScope(): void
    {
        static::addGlobalScope('empresa', function (Builder $builder) {
            if (app()->has(TenantService::class)) {
                $empresaId = app(TenantService::class)->getEmpresaId();
                if ($empresaId) {
                    $builder->where('empresa_id', $empresaId);
                }
            }
        });

        static::creating(function ($model) {
            if (!$model->empresa_id && app()->has(TenantService::class)) {
                $model->empresa_id = app(TenantService::class)->getEmpresaId();
            }
        });
    }
}
```

---

## 📡 **RUTAS**

### **🌐 web.php**
```php
<?php
// routes/web.php

Route::middleware(['auth', 'empresa.scope'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Context management
    Route::get('/context-selector', ContextSelector::class)->name('context.selector');
    
    // Productos
    Route::prefix('productos')->group(function () {
        Route::get('/', ProductoTable::class)->name('productos.index');
        Route::get('/crear', ProductoForm::class)->name('productos.create');
        Route::get('/{producto}/editar', ProductoForm::class)->name('productos.edit');
        Route::get('/{producto}', ProductoShow::class)->name('productos.show');
        Route::get('/{producto}/stock', ProductoStock::class)->name('productos.stock');
    });
    
    // Ventas
    Route::prefix('ventas')->group(function () {
        Route::get('/', VentaTable::class)->name('ventas.index');
        Route::get('/crear', VentaForm::class)->name('ventas.create');
        Route::get('/{venta}', VentaShow::class)->name('ventas.show');
        Route::get('/{venta}/imprimir', [VentaController::class, 'print'])->name('ventas.print');
    });
    
    // POS
    Route::get('/pos', POS::class)->name('pos');
    
    // Inventario
    Route::prefix('inventario')->group(function () {
        Route::get('/stock', InventarioStock::class)->name('inventario.stock');
        Route::get('/movimientos', InventarioMovimientos::class)->name('inventario.movimientos');
        Route::get('/ajustes', InventarioAjustes::class)->name('inventario.ajustes');
        
        Route::middleware('feature:multi_sucursal')->group(function () {
            Route::get('/transferencias', InventarioTransferencias::class)->name('inventario.transferencias');
        });
    });
    
    // Caja (feature flag)
    Route::middleware('feature:caja')->prefix('caja')->group(function () {
        Route::get('/sesiones', CajaSesiones::class)->name('caja.sesiones');
        Route::get('/abrir', CajaAbrir::class)->name('caja.abrir');
        Route::get('/cerrar', CajaCerrar::class)->name('caja.cerrar');
    });
    
    // Reportes
    Route::prefix('reportes')->group(function () {
        Route::get('/ventas', ReporteVentas::class)->name('reportes.ventas');
        Route::get('/inventario', ReporteInventario::class)->name('reportes.inventario');
        Route::get('/productos', ReporteProductos::class)->name('reportes.productos');
    });
});
```

---

## 🎯 **ESTADO ACTUAL DE IMPLEMENTACIÓN**

### **✅ Completado**
```
Models:
- ✅ Empresa, Usuario, Sucursal
- ✅ Producto, Categoria
- ✅ Cliente, Proveedor
- ✅ Venta, VentaItem
- ✅ ProductoSucursalStock, InventarioMov
- ✅ FeatureFlag

Services:
- ✅ TenantService
- ✅ FeatureFlagService
- ✅ ProductoService (básico)
- ✅ VentaService (básico)

Traits & Observers:
- ✅ HasEmpresaScope
- ✅ VentaItemObserver básico
```

### **🔄 En Progreso**
```
Controllers:
- 🔄 ProductoController (CRUD básico)
- 🔄 VentaController (básico)
- 🔄 InventarioController (básico)

Services:
- 🔄 CompraService
- 🔄 InventarioService
- 🔄 CajaService
```

### **❌ Pendiente**
```
Controllers:
- ❌ ClienteController
- ❌ ProveedorController
- ❌ ReporteController
- ❌ CajaController

Services:
- ❌ ReporteService
- ❌ SunatService
- ❌ NotificationService

Middleware:
- ❌ PlanLimitsMiddleware
- ❌ AuditLogMiddleware

Tests:
- ❌ Unit tests para Services
- ❌ Feature tests para Controllers
- ❌ Integration tests
```

---

**🚀 ESTA ESPECIFICACIÓN DEFINE LA ARQUITECTURA BACKEND COMPLETA**

*Actualizado: 30 Agosto 2025*  
*Estado: 📋 ESPECIFICACIÓN TÉCNICA COMPLETA*  
*Próximo paso: Implementar controllers y servicios pendientes*
