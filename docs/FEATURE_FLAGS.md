# 🚩 SmartKet ERP - Feature Flags y Gestión de Features

**Versión:** 1.0  
**Fecha:** 30 Agosto 2025  
**Estado:** 📋 SISTEMA DE FEATURES DEFINIDO  

---

## 🎯 **SISTEMA DE FEATURE FLAGS**

### **🏗️ Arquitectura del Sistema**
```
📊 TABLA: feature_flags
├── empresa_id (FK)
├── feature_key (string)
└── enabled (boolean)

🔧 SERVICIO: FeatureFlagService
├── isEnabled(feature, empresa_id)
├── enable(feature, empresa_id)
├── disable(feature, empresa_id)
└── getEnabledFeatures(empresa_id)

🛡️ MIDDLEWARE: FeatureGuard
└── Protección de rutas por feature

🎨 BLADE DIRECTIVE: @feature
└── Mostrar contenido condicional
```

---

## 📋 **CATÁLOGO DE FEATURES**

### **🆓 Features Básicas (Incluidas en todos los planes)**

#### **pos** - Punto de Venta
```yaml
Descripción: Interfaz de punto de venta básica
Incluye:
  - POS touch interface
  - Carrito de productos
  - Venta directa (interno/boleta/factura)
  - Cálculo automático de totales
  - Selección de clientes
Tablas afectadas:
  - ventas
  - venta_items
Rutas protegidas:
  - /pos
Componentes:
  - POS.php (Livewire)
  - pos.blade.php
```

### **💼 Features Estándar (Planes STANDARD+)**

#### **multi_sucursal** - Multi-sucursal
```yaml
Descripción: Gestión de múltiples sucursales
Incluye:
  - Creación de sucursales
  - Stock por sucursal
  - Transferencias entre sucursales
  - Reportes por sucursal
  - Usuarios asignados a sucursales
Tablas afectadas:
  - sucursales
  - producto_sucursal_stock
  - usuarios (sucursal_id)
  - inventario_movs
Rutas protegidas:
  - /sucursales/*
  - /inventario/transferencias
Middleware aplicado:
  - feature:multi_sucursal
```

#### **lotes** - Control de Lotes
```yaml
Descripción: Gestión de productos por lotes y vencimientos
Incluye:
  - Creación de lotes automática/manual
  - Control de fechas de vencimiento
  - Stock por lote
  - Alertas de vencimiento cercano
  - Trazabilidad FIFO/FEFO
  - Reportes de merma por vencimiento
Tablas afectadas:
  - lotes
  - producto_sucursal_stock (lote_id)
  - venta_items (lote_id)
  - inventario_movs (lote_id)
Configuración producto:
  - controla_lote (boolean)
  - vida_util_dias (int)
Rutas protegidas:
  - /productos/*/lotes
  - /inventario/lotes
  - /inventario/vencimientos
```

#### **caja** - Gestión de Caja
```yaml
Descripción: Control de sesiones de caja y flujo de efectivo
Incluye:
  - Apertura/cierre de caja
  - Control de efectivo por sesión
  - Arqueo de caja
  - Ingresos y retiros
  - Cuadre automático
  - Reportes de caja
Tablas afectadas:
  - caja_sesiones
  - venta_pagos
Métodos de pago:
  - efectivo (requiere caja abierta)
  - yape/plin/bim (no requiere caja)
  - tarjeta (no requiere caja)
  - transferencia (no requiere caja)
Rutas protegidas:
  - /caja/*
Validaciones:
  - Solo un usuario puede tener caja abierta por sucursal
  - Ventas en efectivo requieren caja abierta
```

### **⭐ Features Premium (Plan PRO+)**

#### **facturacion_electronica** - Facturación Electrónica SUNAT
```yaml
Descripción: Integración completa con SUNAT para comprobantes electrónicos
Incluye:
  - Generación de XML (UBL 2.1)
  - Envío automático a SUNAT
  - Procesamiento de CDR
  - Manejo de rechazos y errores
  - Numeración automática por serie
  - Conversion interno → fiscal
  - Estados: pending/accepted/rejected/error
Tablas afectadas:
  - sunat_comprobantes
  - ventas (es_electronico)
Configuración empresa:
  - RUC obligatorio
  - Certificado digital
  - Credenciales SUNAT
  - Series configuradas
Rutas protegidas:
  - /facturacion/*
  - /sunat/*
Integración:
  - SUNAT Beta/Producción
  - Queue para envíos async
```

#### **variantes** - Variantes de Productos
```yaml
Descripción: Productos con múltiples variaciones (talla, color, etc.)
Incluye:
  - Producto padre con variantes hijas
  - Atributos configurables por variante
  - Precios específicos por variante
  - Stock independiente por variante
  - SKU único por variante
  - Búsqueda por variante en POS
Tablas afectadas:
  - producto_variantes
  - venta_items (variante_id)
  - compra_items (variante_id)
  - inventario_movs (variante_id)
Configuración producto:
  - es_variantes (boolean)
Atributos soportados:
  - Talla (XS, S, M, L, XL)
  - Color (Rojo, Azul, Verde, etc.)
  - Material (Algodón, Poliéster, etc.)
  - Personalizado (JSON flexible)
```

#### **api_access** - Acceso a API
```yaml
Descripción: API REST completa para integraciones
Incluye:
  - Endpoints CRUD para todas las entidades
  - Autenticación por API tokens
  - Rate limiting por empresa
  - Webhooks para eventos importantes
  - Documentación Swagger/OpenAPI
  - SDKs para lenguajes populares
Endpoints principales:
  - /api/productos
  - /api/ventas
  - /api/inventario
  - /api/clientes
  - /api/reportes
Autenticación:
  - Laravel Sanctum tokens
  - Scope-based permissions
Rate limits:
  - FREE: No API
  - STANDARD: No API
  - PRO: 1000 req/día
  - ENTERPRISE: 10000 req/día
```

### **🏢 Features Enterprise (Plan ENTERPRISE)**

#### **smart_insights** - SmartInsights IA
```yaml
Descripción: Inteligencia artificial predictiva integrada
Incluye:
  - Forecasting de demanda por producto
  - Detección de patrones de comportamiento
  - Recomendaciones automáticas de precios
  - Alertas predictivas de stock
  - Análisis de combinaciones de productos
  - Optimización de inventario
  - Predicción de clientes frecuentes
Módulos IA:
  - Forecasting Engine (demanda)
  - Pattern Discovery (comportamiento)
  - Smart Recommendations (precios/combos)
  - Real-time Alerts (anomalías)
Infraestructura:
  - Python FastAPI microservice
  - TensorFlow/PyTorch models
  - Redis cache para predicciones
  - Queue para processing asyncrónico
API Endpoints:
  - /ai/forecast
  - /ai/patterns
  - /ai/recommendations
  - /ai/alerts (WebSocket)
```

#### **smart_assistant** - SmartAssistant IA
```yaml
Descripción: Asistente conversacional personalizado por empresa
Incluye:
  - Chat integrado en la UI
  - Respuestas basadas en datos de la empresa
  - Consultas en lenguaje natural
  - Recomendaciones proactivas
  - Análisis de texto y voz
  - Integración con WhatsApp Business
Capabilities:
  - "¿Cuáles son mis productos más vendidos?"
  - "¿Qué productos están por vencer?"
  - "¿Cuánto vendí ayer?"
  - "¿Cuándo debo reponer X producto?"
  - "¿Qué cliente compra más?"
Tecnología:
  - OpenAI GPT integrado
  - RAG (Retrieval Augmented Generation)
  - Vector database para contexto
  - WhatsApp Business API
Interface:
  - Chat widget en dashboard
  - Comandos por voz
  - WhatsApp integration
```

#### **white_label** - Marca Blanca
```yaml
Descripción: Personalización completa de la marca
Incluye:
  - Logo personalizado
  - Colores corporativos
  - Dominio personalizado
  - Email templates customizados
  - PDF templates personalizados
  - Términos y condiciones propios
  - Soporte directo (sin marca SmartKet)
Personalización:
  - Logo (header, favicon, login)
  - Colores (primary, secondary, accent)
  - Tipografía
  - Documentos PDF (facturas, reportes)
  - Emails transaccionales
Configuración:
  - Panel de personalización
  - Previsualización en tiempo real
  - Assets storage en CDN
  - DNS management incluido
```

---

## 📊 **MATRIZ DE FEATURES POR PLAN**

```
┌─────────────────────┬──────┬─────────┬─────┬────────────┐
│ Feature             │ FREE │ STANDARD│ PRO │ ENTERPRISE │
├─────────────────────┼──────┼─────────┼─────┼────────────┤
│ pos                 │  ✅  │    ✅   │ ✅  │     ✅     │
│ multi_sucursal      │  ❌  │    ✅   │ ✅  │     ✅     │
│ lotes               │  ❌  │    ✅   │ ✅  │     ✅     │
│ caja                │  ❌  │    ✅   │ ✅  │     ✅     │
│ facturacion_electronic│ ❌ │    ❌   │ ✅  │     ✅     │
│ variantes           │  ❌  │    ❌   │ ✅  │     ✅     │
│ api_access          │  ❌  │    ❌   │ ✅  │     ✅     │
│ smart_insights      │  ❌  │    ❌   │ ❌  │     ✅     │
│ smart_assistant     │  ❌  │    ❌   │ ❌  │     ✅     │
│ white_label         │  ❌  │    ❌   │ ❌  │     ✅     │
└─────────────────────┴──────┴─────────┴─────┴────────────┘
```

---

## 🎯 **FEATURES POR RUBRO**

### **🥖 Panadería**
```yaml
Features recomendadas:
  - pos (básico)
  - lotes (control vencimientos)
  - caja (manejo efectivo)
  
Flujo típico:
  1. Producción diaria con lotes
  2. Venta POS con control vencimientos
  3. Manejo caja efectivo
  4. Alertas productos por vencer
```

### **💊 Farmacia**
```yaml
Features recomendadas:
  - pos (venta medicamentos)
  - lotes (obligatorio por regulación)
  - variantes (concentraciones)
  - facturacion_electronica (B2B)
  
Flujo típico:
  1. Control estricto de lotes
  2. Variantes por concentración
  3. Facturación a clínicas/hospitales
  4. Alertas vencimiento crítico
```

### **🔧 Ferretería**
```yaml
Features recomendadas:
  - pos (venta mostrador)
  - variantes (medidas/colores)
  - multi_sucursal (locales múltiples)
  - api_access (integración proveedores)
  
Flujo típico:
  1. Productos con múltiples variantes
  2. Stock distribuido en sucursales
  3. Integración con proveedores
  4. Transferencias entre locales
```

### **🍽️ Restaurante**
```yaml
Features recomendadas:
  - pos (comandas)
  - caja (propinas/efectivo)
  - lotes (ingredientes perecibles)
  
Flujo típico:
  1. POS para comandas
  2. Control ingredientes por lote
  3. Caja con propinas
  4. Reportes por turno
```

### **🏪 Minimarket**
```yaml
Features recomendadas:
  - pos (checkout rápido)
  - lotes (productos perecibles)
  - caja (multiple payment methods)
  - multi_sucursal (cadena tiendas)
  - smart_insights (recomendaciones stock)
  
Flujo típico:
  1. POS optimizado para volumen
  2. Control vencimientos automático
  3. Múltiples métodos de pago
  4. IA para reposición inteligente
```

### **👩‍⚕️ Consultorio**
```yaml
Features recomendadas:
  - facturacion_electronica (servicios profesionales)
  - api_access (sistemas médicos)
  
Flujo típico:
  1. Facturación de servicios
  2. Integración con sistemas médicos
  3. Reportes profesionales
```

---

## 🔧 **IMPLEMENTACIÓN TÉCNICA**

### **🗄️ Base de Datos**
```sql
-- Tabla principal de feature flags
CREATE TABLE feature_flags (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  feature_key VARCHAR(60) NOT NULL,
  enabled TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  UNIQUE KEY idx_feature_empresa_key (empresa_id, feature_key),
  INDEX idx_feature_empresa_enabled (empresa_id, enabled)
);

-- Configuración de features por plan
CREATE TABLE plan_features (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plan_id BIGINT UNSIGNED NOT NULL,
  feature_key VARCHAR(60) NOT NULL,
  included TINYINT(1) DEFAULT 1,
  
  FOREIGN KEY (plan_id) REFERENCES planes(id),
  UNIQUE KEY idx_plan_feature (plan_id, feature_key)
);
```

### **🔧 Service Class**
```php
<?php
namespace App\Services;

class FeatureFlagService
{
    private $cache = [];
    
    public function isEnabled(string $feature, ?int $empresaId = null): bool
    {
        $empresaId = $empresaId ?: $this->getCurrentEmpresaId();
        
        if (!$empresaId) {
            return false;
        }
        
        $cacheKey = "feature:{$empresaId}:{$feature}";
        
        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = Cache::remember($cacheKey, 300, function() use ($feature, $empresaId) {
                return FeatureFlag::where('empresa_id', $empresaId)
                    ->where('feature_key', $feature)
                    ->where('enabled', true)
                    ->exists();
            });
        }
        
        return $this->cache[$cacheKey];
    }
    
    public function enableForEmpresa(string $feature, int $empresaId): void
    {
        FeatureFlag::updateOrCreate(
            ['empresa_id' => $empresaId, 'feature_key' => $feature],
            ['enabled' => true]
        );
        
        $this->clearCache($empresaId, $feature);
    }
    
    public function disableForEmpresa(string $feature, int $empresaId): void
    {
        FeatureFlag::updateOrCreate(
            ['empresa_id' => $empresaId, 'feature_key' => $feature],
            ['enabled' => false]
        );
        
        $this->clearCache($empresaId, $feature);
    }
    
    public function syncPlanFeatures(Empresa $empresa): void
    {
        $planFeatures = PlanFeature::where('plan_id', $empresa->plan_id)
            ->where('included', true)
            ->pluck('feature_key');
            
        foreach ($planFeatures as $feature) {
            $this->enableForEmpresa($feature, $empresa->id);
        }
        
        // Deshabilitar features no incluidas en el plan
        $allFeatures = $this->getAllFeatures();
        $featuresToDisable = array_diff($allFeatures, $planFeatures->toArray());
        
        foreach ($featuresToDisable as $feature) {
            $this->disableForEmpresa($feature, $empresa->id);
        }
    }
    
    private function clearCache(int $empresaId, string $feature): void
    {
        $cacheKey = "feature:{$empresaId}:{$feature}";
        Cache::forget($cacheKey);
        unset($this->cache[$cacheKey]);
    }
    
    private function getCurrentEmpresaId(): ?int
    {
        return app(TenantService::class)->getEmpresaId();
    }
    
    private function getAllFeatures(): array
    {
        return [
            'pos',
            'multi_sucursal',
            'lotes',
            'caja',
            'facturacion_electronica',
            'variantes',
            'api_access',
            'smart_insights',
            'smart_assistant',
            'white_label'
        ];
    }
}
```

### **🛡️ Middleware**
```php
<?php
namespace App\Http\Middleware;

class FeatureGuard
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        if (!app(FeatureFlagService::class)->isEnabled($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Feature not available',
                    'feature' => $feature,
                    'message' => "La funcionalidad '{$feature}' no está disponible en su plan actual."
                ], 403);
            }
            
            abort(403, "La funcionalidad '{$feature}' no está disponible en su plan actual.");
        }
        
        return $next($request);
    }
}
```

### **🎨 Blade Directive**
```php
<?php
// En AppServiceProvider::boot()

Blade::directive('feature', function ($feature) {
    return "<?php if(app(\App\Services\FeatureFlagService::class)->isEnabled($feature)): ?>";
});

Blade::directive('endfeature', function () {
    return "<?php endif; ?>";
});

Blade::directive('nofeature', function ($feature) {
    return "<?php if(!app(\App\Services\FeatureFlagService::class)->isEnabled($feature)): ?>";
});

Blade::directive('endnofeature', function () {
    return "<?php endif; ?>";
});
```

### **🎨 Uso en Blade**
```blade
{{-- Mostrar contenido solo si la feature está habilitada --}}
@feature('multi_sucursal')
    <div class="sucursal-selector">
        <select name="sucursal_id">
            @foreach($sucursales as $sucursal)
                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
            @endforeach
        </select>
    </div>
@endfeature

{{-- Mostrar contenido solo si la feature NO está habilitada --}}
@nofeature('caja')
    <div class="alert alert-info">
        <p>La gestión de caja no está disponible en su plan actual.</p>
        <a href="{{ route('planes.upgrade') }}">Actualizar Plan</a>
    </div>
@endnofeature

{{-- Navegación condicional --}}
<nav>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('productos.index') }}">Productos</a>
    <a href="{{ route('ventas.index') }}">Ventas</a>
    
    @feature('caja')
        <a href="{{ route('caja.sesiones') }}">Caja</a>
    @endfeature
    
    @feature('multi_sucursal')
        <a href="{{ route('sucursales.index') }}">Sucursales</a>
    @endfeature
</nav>
```

---

## 🔄 **ACTIVACIÓN AUTOMÁTICA POR RUBRO**

### **🔧 Preset de Features**
```php
<?php
// En EmpresaService o durante registro

class EmpresaService
{
    private $rubroFeatures = [
        'panaderia' => ['pos', 'lotes', 'caja'],
        'farmacia' => ['pos', 'lotes', 'variantes', 'facturacion_electronica'],
        'ferreteria' => ['pos', 'variantes', 'multi_sucursal'],
        'restaurante' => ['pos', 'caja', 'lotes'],
        'consultorio' => ['facturacion_electronica'],
        'minimarket' => ['pos', 'lotes', 'caja', 'multi_sucursal'],
        'otros' => ['pos'] // Mínimo básico
    ];
    
    public function activateRubroFeatures(Empresa $empresa): void
    {
        $features = $this->rubroFeatures[$empresa->tipo_rubro] ?? ['pos'];
        $featureService = app(FeatureFlagService::class);
        
        foreach ($features as $feature) {
            // Solo activar si está incluido en el plan
            if ($this->isFeatureIncludedInPlan($feature, $empresa->plan_id)) {
                $featureService->enableForEmpresa($feature, $empresa->id);
            }
        }
    }
    
    private function isFeatureIncludedInPlan(string $feature, int $planId): bool
    {
        return PlanFeature::where('plan_id', $planId)
            ->where('feature_key', $feature)
            ->where('included', true)
            ->exists();
    }
}
```

---

## 📊 **MONITOREO Y MÉTRICAS**

### **📈 Métricas de Adopción**
```php
<?php
// Analytics de features
class FeatureAnalytics
{
    public function getAdoptionStats(): array
    {
        return [
            'pos' => [
                'empresas_habilitadas' => $this->getEmpresasWithFeature('pos'),
                'uso_diario' => $this->getDailyUsage('pos'),
                'conversion_rate' => $this->getConversionRate('pos')
            ],
            'caja' => [
                'empresas_habilitadas' => $this->getEmpresasWithFeature('caja'),
                'sesiones_diarias' => $this->getDailyCajaSessions(),
                'tiempo_promedio_sesion' => $this->getAverageSessionTime()
            ],
            // ... más métricas por feature
        ];
    }
    
    private function getEmpresasWithFeature(string $feature): int
    {
        return FeatureFlag::where('feature_key', $feature)
            ->where('enabled', true)
            ->count();
    }
}
```

---

**🚩 ESTE DOCUMENTO DEFINE EL SISTEMA COMPLETO DE FEATURE FLAGS**

*Actualizado: 30 Agosto 2025*  
*Estado: 📋 SISTEMA DEFINIDO Y LISTO PARA IMPLEMENTACIÓN*  
*Próximo paso: Implementar FeatureFlagService y middleware*
