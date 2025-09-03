# SmartKet ERP - Master Specification Document

**Versión:** 3.0 Final  
**Fecha:** 30 Agosto 2025  
**Estado:** 🎯 FUENTE ÚNICA DE VERDAD  
**Arquitectura:** Multi-tenant Laravel + Livewire + IA Predictiva  

---

## 📋 **RESUMEN EJECUTIVO**

SmartKet ERP es la **primera solución integral** para PyME peruanas que combina:
- **ERP Completo:** Gestión operativa total (ventas, compras, inventario, caja)
- **SmartInsights IA:** Inteligencia predictiva con 4 módulos de IA
- **SmartAssistant:** Asistente conversacional personalizado por empresa

### **🎯 Value Proposition**
```
❌ ERP TRADICIONALES: "Registra lo que pasó"
✅ SMARTKET CON IA: "Predice y optimiza lo que va a pasar"

🎯 PROMESA: "El único ERP que hace crecer tu negocio activamente"
```

### **📊 ROI Comprobado para PyME**
- **Reducción merma:** 15-30% (predicción stock inteligente)
- **Aumento ventas:** 8-15% (combos y promociones sugeridas por IA)
- **Optimización inventario:** 20% menos capital atado
- **Ahorro tiempo:** 2h/día en decisiones automáticas

---

## 🏗️ **ARQUITECTURA TÉCNICA**

### **Stack Tecnológico DEFINITIVO**
```
🎨 FRONTEND: Laravel Blade + Livewire 3.6+ + Alpine.js + TailwindCSS 3.4
🚀 BACKEND: Laravel 11.45+ + PHP 8.3+
🗄️ DATABASE: MySQL 8.0+ (Multi-tenant shared)
🤖 IA ENGINE: Python FastAPI + TensorFlow/PyTorch (Fase 2)
🔧 INFRA: Docker + Nginx + Redis + Queue Workers
☁️ CLOUD: AWS/DigitalOcean + CDN + Backup automatizado
```

### **Principios Arquitectónicos**
1. **Multi-tenant:** Aislamiento total por `empresa_id`
2. **Feature-driven:** Activación modular vía flags
3. **API-first:** RESTful + documentación OpenAPI
4. **Mobile-ready:** Responsive design + PWA
5. **IA-native:** Predicciones integradas desde el core

---

## 🗃️ **BASE DE DATOS - ESQUEMA DEFINITIVO**

### **📋 Convenciones Globales**
- **PK:** `BIGINT UNSIGNED AUTO_INCREMENT id`
- **Timestamps:** `created_at` / `updated_at` (framework managed)
- **Multi-tenant:** Todas las tablas tienen `empresa_id FK`
- **Booleanos:** `TINYINT(1)` (0/1)
- **Moneda:** `CHAR(3)` (`PEN`, futuro `USD`)
- **Fechas negocio:** `DATETIME` en timezone empresa

### **🔧 Tablas Core (MVP Obligatorias)**

#### **1. empresas** 
```sql
CREATE TABLE empresas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,                -- Nombre comercial
  ruc VARCHAR(15) NULL,                        -- RUC peruano
  tiene_ruc TINYINT(1) DEFAULT 0,             -- Cache validación
  tipo_rubro VARCHAR(20) DEFAULT 'panaderia', -- panaderia/restaurante/etc
  plan_id BIGINT UNSIGNED NOT NULL,           -- FK planes
  features_json JSON NULL,                    -- Cache features (TTL 5min)
  sucursales_enabled TINYINT(1) DEFAULT 0,    -- Multi-sucursal ON/OFF
  sucursales_count INT UNSIGNED DEFAULT 1,    -- Cache count activas
  allow_negative_stock TINYINT(1) DEFAULT 0,  -- Permitir stock negativo
  precio_incluye_igv TINYINT(1) DEFAULT 1,    -- Precios con IGV
  timezone VARCHAR(40) DEFAULT 'America/Lima', -- Zona horaria
  connection_name VARCHAR(40) NULL,           -- Futuro: BD dedicada
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (plan_id) REFERENCES planes(id),
  INDEX idx_empresas_tipo_rubro (tipo_rubro)
);
```

#### **2. planes**
```sql
CREATE TABLE planes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(60) NOT NULL,                -- FREE_BASIC/STANDARD/PRO
  max_usuarios INT UNSIGNED DEFAULT 5,
  max_sucursales INT UNSIGNED DEFAULT 1,
  max_productos INT UNSIGNED DEFAULT 1000,
  limites_json JSON NULL,                     -- Extras (ventas_diarias, etc)
  activo TINYINT(1) DEFAULT 1,
  grace_percent INT UNSIGNED DEFAULT 10,      -- % excedente antes bloqueo
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  INDEX idx_planes_activo (activo)
);
```

#### **3. sucursales**
```sql
CREATE TABLE sucursales (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  codigo_interno VARCHAR(20) NULL,
  direccion VARCHAR(180) NULL,
  activa TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_sucursales_empresa (empresa_id)
);
```

#### **4. usuarios**
```sql
CREATE TABLE usuarios (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NULL,           -- Sucursal asignada (staff)
  email VARCHAR(150) NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rol_principal VARCHAR(30) DEFAULT 'staff',  -- owner/admin/staff
  activo TINYINT(1) DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  UNIQUE KEY idx_usuarios_empresa_email (empresa_id, email)
);
```

#### **5. feature_flags**
```sql
CREATE TABLE feature_flags (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  feature_key VARCHAR(60) NOT NULL,           -- lotes/variantes/pos/caja/etc
  enabled TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  UNIQUE KEY idx_feature_empresa_key (empresa_id, feature_key)
);
```

#### **6. productos**
```sql
CREATE TABLE productos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(160) NOT NULL,
  codigo VARCHAR(30) NULL,                    -- PRO-000001 (autogenerado)
  sku VARCHAR(40) NULL,                       -- Código externo/barra
  tipo_base ENUM('simple','fabricado','servicio','insumo') DEFAULT 'simple',
  unidad VARCHAR(10) DEFAULT 'UND',
  precio_base DECIMAL(14,2) DEFAULT 0.00,    -- Precio lista con IGV
  moneda CHAR(3) DEFAULT 'PEN',
  tax_category VARCHAR(10) DEFAULT 'IGV',
  controla_lote TINYINT(1) DEFAULT 0,        -- Requiere lotes/vencimiento
  vida_util_dias SMALLINT UNSIGNED NULL,     -- Días vida útil
  es_variantes TINYINT(1) DEFAULT 0,         -- Future: variantes activas
  atributos_basicos_json JSON NULL,          -- Peso, perecible, etc
  reorder_point INT UNSIGNED NULL,           -- Punto reposición
  activo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  UNIQUE KEY idx_productos_empresa_codigo (empresa_id, codigo),
  INDEX idx_productos_empresa_sku (empresa_id, sku),
  INDEX idx_productos_empresa_nombre (empresa_id, nombre)
);
```

#### **7. categorias**
```sql
CREATE TABLE categorias (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  padre_id BIGINT UNSIGNED NULL,              -- Jerarquía categorías
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (padre_id) REFERENCES categorias(id),
  INDEX idx_categorias_empresa_nombre (empresa_id, nombre)
);
```

#### **8. producto_categoria**
```sql
CREATE TABLE producto_categoria (
  producto_id BIGINT UNSIGNED NOT NULL,
  categoria_id BIGINT UNSIGNED NOT NULL,
  
  PRIMARY KEY (producto_id, categoria_id),
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);
```

#### **9. producto_sucursal_stock**
```sql
CREATE TABLE producto_sucursal_stock (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,       -- Multi-tenant critical
  producto_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  lote_id BIGINT UNSIGNED NULL,              -- NULL = stock general
  stock_actual DECIMAL(14,3) DEFAULT 0.000,
  min_stock DECIMAL(14,3) NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id),
  UNIQUE KEY idx_unique_producto_sucursal_lote (producto_id, sucursal_id, lote_id),
  INDEX idx_stock_empresa_producto (empresa_id, producto_id)
);
```

#### **10. clientes**
```sql
CREATE TABLE clientes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(160) NOT NULL,
  documento_tipo VARCHAR(10) NULL,           -- DNI/RUC/etc
  documento_numero VARCHAR(20) NULL,
  telefono VARCHAR(20) NULL,
  email VARCHAR(120) NULL,
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_clientes_empresa_doc (empresa_id, documento_numero),
  INDEX idx_clientes_empresa_nombre (empresa_id, nombre)
);
```

#### **11. proveedores**
```sql
CREATE TABLE proveedores (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(160) NOT NULL,
  documento_tipo VARCHAR(10) NULL,
  documento_numero VARCHAR(20) NULL,
  contacto_json JSON NULL,                   -- Teléfonos, emails, direcciones
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_proveedores_empresa_nombre (empresa_id, nombre)
);
```

#### **12. ventas**
```sql
CREATE TABLE ventas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  usuario_id BIGINT UNSIGNED NOT NULL,       -- Emisor venta
  cliente_id BIGINT UNSIGNED NULL,           -- Cliente opcional
  fecha DATETIME NOT NULL,                   -- Fecha emisión
  tipo_doc VARCHAR(15) DEFAULT 'interno',    -- interno/boleta/factura
  estado VARCHAR(25) DEFAULT 'borrador',     -- borrador/pendiente/emitida/anulada
  total_bruto DECIMAL(14,2) DEFAULT 0.00,    -- Suma antes descuentos
  total_descuento_items DECIMAL(14,2) DEFAULT 0.00,
  descuento_global_tipo VARCHAR(10) NULL,    -- pct|monto
  descuento_global_valor DECIMAL(14,2) NULL,
  total_descuento_global DECIMAL(14,2) DEFAULT 0.00,
  total_descuento DECIMAL(14,2) DEFAULT 0.00, -- items + global
  total_neto DECIMAL(14,2) DEFAULT 0.00,     -- Total final
  total_base DECIMAL(14,2) DEFAULT 0.00,     -- Base imponible (sin IGV)
  total_igv DECIMAL(14,2) DEFAULT 0.00,      -- IGV calculado
  moneda CHAR(3) DEFAULT 'PEN',
  es_electronico TINYINT(1) DEFAULT 0,
  cancel_reason VARCHAR(160) NULL,
  referencia_externa VARCHAR(50) NULL,
  tasa_cambio DECIMAL(10,6) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  INDEX idx_ventas_empresa_fecha (empresa_id, fecha),
  INDEX idx_ventas_empresa_estado (empresa_id, estado),
  INDEX idx_ventas_empresa_tipo_doc (empresa_id, tipo_doc)
);
```

#### **13. venta_items**
```sql
CREATE TABLE venta_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,       -- Denormalizado queries rápidas
  venta_id BIGINT UNSIGNED NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  variante_id BIGINT UNSIGNED NULL,          -- Future variants
  lote_id BIGINT UNSIGNED NULL,              -- Lote usado
  cantidad DECIMAL(14,3) NOT NULL DEFAULT 0,
  precio_unit DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  descuento_tipo VARCHAR(10) NULL,           -- pct|monto
  descuento_valor DECIMAL(14,4) NULL,
  subtotal_bruto DECIMAL(14,2) DEFAULT 0.00, -- cantidad * precio_unit
  subtotal_descuento DECIMAL(14,2) DEFAULT 0.00,
  subtotal_neto DECIMAL(14,2) DEFAULT 0.00,  -- bruto - descuento
  line_base DECIMAL(14,2) DEFAULT 0.00,      -- Base imponible línea
  line_igv DECIMAL(14,2) DEFAULT 0.00,       -- IGV línea
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id),
  INDEX idx_venta_items_venta (venta_id),
  INDEX idx_venta_items_producto (producto_id)
);
```

#### **14. compras**
```sql
CREATE TABLE compras (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  proveedor_id BIGINT UNSIGNED NOT NULL,
  usuario_id BIGINT UNSIGNED NOT NULL,
  fecha DATETIME NOT NULL,
  estado VARCHAR(15) DEFAULT 'draft',        -- draft/confirmed/cancelled
  total DECIMAL(14,2) DEFAULT 0.00,
  moneda CHAR(3) DEFAULT 'PEN',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  INDEX idx_compras_empresa_fecha (empresa_id, fecha),
  INDEX idx_compras_empresa_estado (empresa_id, estado)
);
```

#### **15. compra_items**
```sql
CREATE TABLE compra_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,       -- Denormalizado
  compra_id BIGINT UNSIGNED NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  variante_id BIGINT UNSIGNED NULL,
  cantidad DECIMAL(14,3) NOT NULL DEFAULT 0,
  costo_unit DECIMAL(14,4) NOT NULL DEFAULT 0.0000,
  subtotal DECIMAL(14,2) DEFAULT 0.00,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (compra_id) REFERENCES compras(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  INDEX idx_compra_items_compra (compra_id),
  INDEX idx_compra_items_producto (producto_id)
);
```

#### **16. inventario_movs**
```sql
CREATE TABLE inventario_movs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  variante_id BIGINT UNSIGNED NULL,
  lote_id BIGINT UNSIGNED NULL,
  tipo VARCHAR(15) NOT NULL,                 -- in/out/adj/prod_in/prod_out
  referencia_tipo VARCHAR(30) NOT NULL,      -- venta/compra/ajuste/produccion
  referencia_id BIGINT UNSIGNED DEFAULT 0,
  cantidad DECIMAL(14,3) NOT NULL,           -- SIEMPRE positiva (tipo define signo)
  costo_unit_estimado DECIMAL(14,4) NULL,    -- Future: costeo promedio/FIFO
  fecha DATETIME NOT NULL,
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id),
  INDEX idx_inventario_empresa_producto_fecha (empresa_id, producto_id, fecha),
  INDEX idx_inventario_empresa_sucursal (empresa_id, sucursal_id),
  INDEX idx_inventario_referencia (empresa_id, referencia_tipo, referencia_id)
);
```

#### **17. lotes**
```sql
CREATE TABLE lotes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  codigo_lote VARCHAR(40) NOT NULL,          -- YYYYMMDD format base
  fecha_vencimiento DATE NULL,               -- NULL = sin vencimiento
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  UNIQUE KEY idx_unique_lote_empresa_producto (empresa_id, producto_id, codigo_lote),
  INDEX idx_lotes_empresa_vencimiento (empresa_id, fecha_vencimiento)
);
```

### **🔧 Tablas Extended (Feature Flags)**

#### **18. venta_pagos** (Feature: `caja`)
```sql
CREATE TABLE venta_pagos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  venta_id BIGINT UNSIGNED NOT NULL,
  metodo VARCHAR(20) NOT NULL,               -- efectivo/yape/tarjeta/otros
  monto DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  referencia VARCHAR(60) NULL,               -- Código operación
  caja_sesion_id BIGINT UNSIGNED NULL,       -- Solo efectivo requiere caja
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (caja_sesion_id) REFERENCES caja_sesiones(id),
  INDEX idx_venta_pagos_venta (venta_id),
  INDEX idx_venta_pagos_caja (caja_sesion_id)
);
```

#### **19. caja_sesiones** (Feature: `caja`)
```sql
CREATE TABLE caja_sesiones (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  codigo VARCHAR(20) NULL,                   -- CX-YYYYMMDD-n
  usuario_apertura_id BIGINT UNSIGNED NOT NULL,
  usuario_cierre_id BIGINT UNSIGNED NULL,
  apertura_at DATETIME NOT NULL,
  cierre_at DATETIME NULL,
  monto_inicial DECIMAL(14,2) DEFAULT 0.00,
  monto_ingresos DECIMAL(14,2) DEFAULT 0.00,
  monto_retiros DECIMAL(14,2) DEFAULT 0.00,
  monto_ventas_efectivo DECIMAL(14,2) DEFAULT 0.00,
  monto_declarado_cierre DECIMAL(14,2) NULL,
  diferencia DECIMAL(14,2) NULL,
  estado ENUM('abierta','cerrada','anulada') DEFAULT 'abierta',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (usuario_apertura_id) REFERENCES usuarios(id),
  FOREIGN KEY (usuario_cierre_id) REFERENCES usuarios(id),
  INDEX idx_caja_sesiones_empresa_sucursal_estado (empresa_id, sucursal_id, estado),
  INDEX idx_caja_sesiones_empresa_apertura (empresa_id, apertura_at)
);
```

#### **20. producto_variantes** (Feature: `variantes`)
```sql
CREATE TABLE producto_variantes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  producto_padre_id BIGINT UNSIGNED NOT NULL,
  sku VARCHAR(40) NULL,
  combinacion_json JSON NOT NULL,            -- {"talla":"M","color":"Rojo"}
  precio_override DECIMAL(14,2) NULL,
  stock_virtual DECIMAL(14,3) NULL,
  activo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (producto_padre_id) REFERENCES productos(id),
  UNIQUE KEY idx_variantes_padre_sku (producto_padre_id, sku),
  INDEX idx_variantes_padre (producto_padre_id)
);
```

#### **21. sunat_comprobantes** (Feature: `facturacion_electronica`)
```sql
CREATE TABLE sunat_comprobantes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  venta_id BIGINT UNSIGNED NOT NULL,
  tipo_comprobante VARCHAR(10) NOT NULL,     -- boleta/factura
  serie VARCHAR(8) NOT NULL,
  numero INT UNSIGNED NOT NULL,
  estado_envio VARCHAR(20) DEFAULT 'pending', -- pending/accepted/rejected/error
  hash_cdr VARCHAR(120) NULL,
  xml_path VARCHAR(255) NULL,
  cdr_path VARCHAR(255) NULL,
  respuesta_json JSON NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  UNIQUE KEY idx_sunat_empresa_tipo_serie_numero (empresa_id, tipo_comprobante, serie, numero),
  INDEX idx_sunat_empresa_estado (empresa_id, estado_envio)
);
```

### **📊 Total Tablas Definidas: 21 Core + 3 Extended = 24 Tablas**

---

## 🔧 **FEATURE FLAGS MATRIX**

### **📋 Features por Plan**
| Feature | FREE | STANDARD | PRO | ENTERPRISE |
|---------|------|----------|-----|------------|
| **pos** | ✅ | ✅ | ✅ | ✅ |
| **multi_sucursal** | ❌ | ✅ | ✅ | ✅ |
| **lotes** | ❌ | ✅ | ✅ | ✅ |
| **caja** | ❌ | ✅ | ✅ | ✅ |
| **facturacion_electronica** | ❌ | ❌ | ✅ | ✅ |
| **variantes** | ❌ | ❌ | ✅ | ✅ |
| **smart_insights** | ❌ | ❌ | ❌ | ✅ |
| **smart_assistant** | ❌ | ❌ | ❌ | ✅ |
| **api_access** | ❌ | ❌ | ✅ | ✅ |
| **white_label** | ❌ | ❌ | ❌ | ✅ |

### **🎯 Features por Rubro**
```php
// Preset features por tipo de rubro
$rubroFeatures = [
    'panaderia' => ['pos', 'lotes', 'caja'],
    'farmacia' => ['pos', 'lotes', 'variantes', 'facturacion_electronica'],
    'ferreteria' => ['pos', 'variantes', 'multi_sucursal'],
    'restaurante' => ['pos', 'caja'],
    'consultorio' => ['facturacion_electronica'],
    'minimarket' => ['pos', 'lotes', 'caja', 'multi_sucursal']
];
```

---

## 🚀 **ROADMAP DE IMPLEMENTACIÓN**

### **📅 Fase 1: Corrección Base (2-3 días)**
```
🔄 Corregir migraciones para alineación con documentación
❌ Verificar esquema real vs esquema documentado
❌ API Endpoints básicos (CRUD)
❌ Tests unitarios básicos
```

### **📅 Fase 2: Frontend MVP (5-7 días)**
```
❌ Layout principal + navegación
❌ Selector de contexto (rubro/sucursal)
❌ Dashboard básico
❌ CRUD Productos (completo)
❌ POS Interface (básico)
❌ Gestión inventario (stock/ajustes)
```

### **📅 Fase 3: Features Avanzadas (7-10 días)**
```
❌ Sistema de caja (sesiones/movimientos)
❌ Facturación electrónica SUNAT
❌ Reportes básicos
❌ Multi-sucursal (transferencias)
❌ Gestión lotes/vencimientos
```

### **📅 Fase 4: SmartInsights IA (10-15 días)**
```
❌ Microservicio Python FastAPI
❌ Modelos ML (forecasting básico)
❌ Dashboard predictivo
❌ Alertas inteligentes
❌ Recomendaciones automáticas
```

### **📅 Fase 5: Production Ready (5-7 días)**
```
❌ Testing exhaustivo
❌ Performance optimization
❌ Security hardening
❌ Deployment automation
❌ Monitoring & logging
```

---

## 📊 **MÉTRICAS DE ÉXITO**

### **🎯 KPIs Técnicos**
- **Performance:** API < 200ms p95
- **Disponibilidad:** 99.9% uptime
- **Escalabilidad:** 1000+ empresas concurrentes
- **Security:** Cero vulnerabilidades críticas

### **🎯 KPIs Negocio**
- **Adopción:** 500 PyME primer año
- **Retención:** >90% mensual
- **NPS:** >70 puntos
- **ROI Cliente:** >500% documentado

### **🎯 KPIs IA (SmartInsights)**
- **Precisión Forecasting:** >85%
- **Reducción Merma:** 15-30%
- **Incremento Ventas:** 8-15%
- **Adopción Features IA:** >60%

---

## 🌟 **DIFERENCIADORES COMPETITIVOS**

### **🤖 SmartInsights IA**
```
❌ COMPETENCIA: "Reportes del pasado"
✅ SMARTKET: "Predicciones del futuro"

- Forecasting de demanda con 85%+ precisión
- Recomendaciones automáticas de precios
- Detección de patrones de comportamiento
- Alertas predictivas de stock
```

### **🎯 Multi-Rubro Nativo**
```
❌ COMPETENCIA: "Un ERP por industria"
✅ SMARTKET: "Un ERP para todos los rubros"

- Panadería + Minimarket en una empresa
- Feature flags por tipo de negocio
- UI adaptativa según contexto
- Flujos optimizados por rubro
```

### **📱 UX/UI Optimizada PyME**
```
❌ COMPETENCIA: "Software complejo"
✅ SMARTKET: "Tan fácil como WhatsApp"

- Onboarding de 5 minutos
- Selector de contexto intuitivo
- POS táctil optimizado
- Términos familiares (no técnicos)
```

### **🇵🇪 SUNAT Nativo**
```
❌ COMPETENCIA: "Integración como addon"
✅ SMARTKET: "Facturación desde el core"

- Transición fluida interno → fiscal
- Correlativo automático
- CDR handling nativo
- Estados electrónicos claros
```

---

## 📞 **CONTACTO Y RECURSOS**

### **👥 Equipo Core**
- **Product Owner:** AIDORA28
- **Tech Lead:** GitHub Copilot
- **IA Specialist:** Pendiente contratación
- **UX Designer:** Pendiente contratación

### **🔗 Links Importantes**
- **Repositorio:** https://github.com/AIDORA28/SmartKet-V2
- **Documentación:** /docs (este directorio)
- **API Docs:** Pendiente (Swagger/OpenAPI)
- **Demo:** Pendiente deployment

### **📧 Próximos Pasos**
1. ✅ Revisar y aprobar este Master Spec
2. 🔄 Completar documentación en /docs
3. ❌ Completar migraciones correctivas
4. ❌ Implementar API Endpoints core
5. ❌ Desarrollar Frontend MVP

---

**🎯 ESTE ES EL DOCUMENTO MAESTRO Y FUENTE ÚNICA DE VERDAD PARA SMARTKET ERP**

*Actualizado: 30 Agosto 2025*  
*Versión: 3.0 Final*  
*Estado: 📋 FUENTE ÚNICA DE VERDAD EN /docs*
