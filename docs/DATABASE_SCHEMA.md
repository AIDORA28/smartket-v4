# 🗄️ SmartKet ERP - Esquema de Base de Datos

**Versión:** 2.0  
**Fecha:** 31 Agosto 2025  
**Estado:** 📋 ESQUEMA DEFINITIVO - 38 MIGRACIONES MVP  

---

## 🎯 **PROPÓSITO DE ESTE DOCUMENTO**

Este documento define el **esquema definitivo** de la base de datos de SmartKet ERP con **38 migraciones para MVP**. Cualquier migración o cambio en la base de datos debe seguir **exactamente** este esquema.

### **🚨 REGLAS DE USO**
1. **ANTES** de crear cualquier migración, verificar contra este esquema
2. **SI** hay diferencias entre BD actual y este esquema, crear migración correctiva
3. **NO** cambiar este esquema sin actualizar primero la documentación
4. **VERIFICAR** que el esquema real coincida con este documento

---

## 📊 **RESUMEN DE 38 MIGRACIONES MVP**

### **🎯 DISTRIBUCIÓN POR MÓDULOS:**
```
🏢 NÚCLEO EMPRESARIAL: 6 migraciones
👥 USUARIOS Y ACCESO: 1 migración  
📦 CATÁLOGOS MAESTROS: 4 migraciones
👥 CRM BÁSICO: 2 migraciones
💰 NÚCLEO DE VENTAS: 3 migraciones
📊 INVENTARIO AVANZADO: 4 migraciones
🚚 COMPRAS: 3 migraciones
🇵🇪 SUNAT PERÚ: 2 migraciones
💰 CAJA MÚLTIPLE: 3 migraciones
🍞 MÓDULOS ESPECÍFICOS: 6 migraciones (3 panadería + 3 farmacia)
📋 GESTIÓN OPERATIVA: 4 migraciones

TOTAL: 38 MIGRACIONES
```

---

## 📋 **CONVENCIONES GLOBALES**

### **🔧 Estándares de Tablas**
```sql
-- Columnas estándar en TODAS las tablas
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
created_at TIMESTAMP NULL DEFAULT NULL
updated_at TIMESTAMP NULL DEFAULT NULL

-- Multi-tenancy: TODAS las tablas de negocio tienen
empresa_id BIGINT UNSIGNED NOT NULL
FOREIGN KEY (empresa_id) REFERENCES empresas(id)
INDEX idx_{tabla}_empresa (empresa_id)
```

### **🎯 Tipos de Datos Estándar**
```sql
-- IDs y Referencias
BIGINT UNSIGNED AUTO_INCREMENT    -- Primary keys
BIGINT UNSIGNED NOT NULL          -- Foreign keys
BIGINT UNSIGNED NULL              -- Optional foreign keys

-- Textos
VARCHAR(20)      -- Códigos cortos (RUC, códigos)
VARCHAR(60)      -- Nombres cortos (categorías, usuarios)
VARCHAR(120)     -- Nombres medianos (productos, empresas)
VARCHAR(160)     -- Nombres largos (clientes, proveedores)
VARCHAR(255)     -- Textos largos (descripciones)
TEXT             -- Textos muy largos (notas, observaciones)

-- Números
DECIMAL(14,2)    -- Montos (precios, totales)
DECIMAL(14,3)    -- Cantidades (stock, peso)
DECIMAL(14,4)    -- Costos unitarios (alta precisión)
INT UNSIGNED     -- Contadores, límites
SMALLINT         -- Días, porcentajes pequeños

-- Booleanos
TINYINT(1)       -- 0/1 para campos boolean

-- Fechas
DATETIME         -- Fechas de negocio (ventas, compras)
DATE             -- Solo fechas (vencimientos)
TIMESTAMP        -- Framework Laravel (created_at, updated_at)

-- JSON y otros
JSON             -- Configuraciones, metadatos
CHAR(3)          -- Códigos fijos (moneda, país)
ENUM()           -- Valores fijos predefinidos
```

---

## 🏢 **NÚCLEO EMPRESARIAL (6 tablas)**

### **1. planes**
```sql
CREATE TABLE planes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(60) NOT NULL COMMENT 'FREE_BASIC/STANDARD/PRO/ENTERPRISE',
  max_usuarios INT UNSIGNED DEFAULT 5 COMMENT 'Máximo usuarios permitidos',
  max_sucursales INT UNSIGNED DEFAULT 1 COMMENT 'Máximo sucursales permitidas',
  max_productos INT UNSIGNED DEFAULT 1000 COMMENT 'Máximo productos permitidos',
  limites_json JSON NULL COMMENT 'Otros límites: ventas_diarias, storage, etc',
  activo TINYINT(1) DEFAULT 1 COMMENT 'Si el plan está disponible',
  grace_percent INT UNSIGNED DEFAULT 10 COMMENT 'Porcentaje de gracia antes de bloqueo',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  INDEX idx_planes_activo (activo)
);
```

### **2. empresas**
```sql
CREATE TABLE empresas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL COMMENT 'Nombre comercial de la empresa',
  ruc VARCHAR(15) NULL COMMENT 'RUC peruano (11 dígitos)',
  tiene_ruc TINYINT(1) DEFAULT 0 COMMENT 'Cache: si RUC está validado',
  plan_id BIGINT UNSIGNED NOT NULL COMMENT 'Plan contratado',
  features_json JSON NULL COMMENT 'Cache de features habilitadas (TTL 5min)',
  sucursales_enabled TINYINT(1) DEFAULT 0 COMMENT 'Si multi-sucursal está activo',
  sucursales_count INT UNSIGNED DEFAULT 1 COMMENT 'Cache: número de sucursales activas',
  allow_negative_stock TINYINT(1) DEFAULT 0 COMMENT 'Permitir stock negativo',
  precio_incluye_igv TINYINT(1) DEFAULT 1 COMMENT 'Si precios incluyen IGV por defecto',
  timezone VARCHAR(40) DEFAULT 'America/Lima' COMMENT 'Zona horaria de la empresa',
  connection_name VARCHAR(40) NULL COMMENT 'Futuro: nombre de BD dedicada',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (plan_id) REFERENCES planes(id),
  INDEX idx_empresas_plan (plan_id)
);
```

### **3. rubros**
```sql
CREATE TABLE rubros (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(60) NOT NULL COMMENT 'panaderia/farmacia/minimarket/ferreteria/restaurante',
  modulos_default_json JSON NULL COMMENT 'Módulos por defecto para este rubro',
  activo TINYINT(1) DEFAULT 1 COMMENT 'Si el rubro está disponible',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  UNIQUE KEY idx_rubros_nombre (nombre),
  INDEX idx_rubros_activo (activo)
);
```

### **4. empresa_rubros**
```sql
CREATE TABLE empresa_rubros (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  rubro_id BIGINT UNSIGNED NOT NULL,
  es_principal TINYINT(1) DEFAULT 0 COMMENT 'Si es el rubro principal de la empresa',
  configuracion_json JSON NULL COMMENT 'Configuración específica para este rubro',
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (rubro_id) REFERENCES rubros(id),
  UNIQUE KEY idx_empresa_rubro_unico (empresa_id, rubro_id),
  INDEX idx_empresa_rubros_empresa (empresa_id),
  INDEX idx_empresa_rubros_rubro (rubro_id)
);
```

### **5. feature_flags**
```sql
CREATE TABLE feature_flags (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  feature_key VARCHAR(60) NOT NULL COMMENT 'pos/multi_sucursal/lotes/caja/facturacion_electronica/variantes/smart_insights',
  enabled TINYINT(1) DEFAULT 0 COMMENT 'Si la feature está habilitada',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  UNIQUE KEY idx_feature_empresa_key (empresa_id, feature_key),
  INDEX idx_feature_empresa_enabled (empresa_id, enabled)
);
```

### **6. sucursales**
```sql
CREATE TABLE sucursales (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(120) NOT NULL COMMENT 'Nombre de la sucursal',
  codigo_interno VARCHAR(20) NULL COMMENT 'Código interno opcional',
  direccion VARCHAR(180) NULL COMMENT 'Dirección física',
  activa TINYINT(1) DEFAULT 1 COMMENT 'Si la sucursal está activa',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_sucursales_empresa (empresa_id),
  INDEX idx_sucursales_empresa_activa (empresa_id, activa)
);
```

---

## � **USUARIOS Y ACCESO (1 tabla)**

### **7. users**
```sql
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NULL COMMENT 'Sucursal asignada para staff',
  email VARCHAR(150) NOT NULL COMMENT 'Email único por empresa',
  nombre VARCHAR(120) NOT NULL COMMENT 'Nombre completo del usuario',
  password VARCHAR(255) NOT NULL COMMENT 'Hash de contraseña',
  rol_principal VARCHAR(30) DEFAULT 'staff' COMMENT 'owner/admin/cajero/vendedor/almacenero',
  activo TINYINT(1) DEFAULT 1 COMMENT 'Si el usuario está activo',
  last_login_at DATETIME NULL COMMENT 'Último login registrado',
  email_verified_at TIMESTAMP NULL,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  UNIQUE KEY idx_users_empresa_email (empresa_id, email),
  INDEX idx_users_empresa_rol (empresa_id, rol_principal),
  INDEX idx_users_sucursal (sucursal_id)
);
```

---

## 📦 **CATÁLOGOS MAESTROS (4 tablas)**

### **8. categorias**
```sql
CREATE TABLE categorias (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(100) NOT NULL COMMENT 'Nombre de la categoría',
  padre_id BIGINT UNSIGNED NULL COMMENT 'Categoría padre para jerarquía',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (padre_id) REFERENCES categorias(id),
  INDEX idx_categorias_empresa (empresa_id),
  INDEX idx_categorias_empresa_nombre (empresa_id, nombre),
  INDEX idx_categorias_padre (padre_id)
);
```

### **9. unidades_medida**
```sql
CREATE TABLE unidades_medida (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(40) NOT NULL COMMENT 'UND/KG/LT/MT/M2/DOC/PAQ',
  descripcion VARCHAR(80) NULL COMMENT 'Descripción completa de la unidad',
  tipo VARCHAR(20) DEFAULT 'general' COMMENT 'general/peso/volumen/longitud/area',
  activo TINYINT(1) DEFAULT 1 COMMENT 'Si la unidad está disponible',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  UNIQUE KEY idx_unidades_nombre (nombre),
  INDEX idx_unidades_tipo (tipo),
  INDEX idx_unidades_activo (activo)
);
```

### **10. marcas**
```sql
CREATE TABLE marcas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(100) NOT NULL COMMENT 'Nombre de la marca',
  fabricante VARCHAR(120) NULL COMMENT 'Empresa fabricante',
  pais_origen CHAR(3) NULL COMMENT 'Código de país ISO 3166',
  activa TINYINT(1) DEFAULT 1 COMMENT 'Si la marca está activa',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_marcas_empresa (empresa_id),
  INDEX idx_marcas_empresa_nombre (empresa_id, nombre),
  INDEX idx_marcas_activa (activa)
);
```

### **11. productos**
```sql
CREATE TABLE productos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  categoria_id BIGINT UNSIGNED NULL,
  marca_id BIGINT UNSIGNED NULL,
  unidad_medida_id BIGINT UNSIGNED NULL,
  nombre VARCHAR(160) NOT NULL COMMENT 'Nombre del producto',
  codigo VARCHAR(30) NULL COMMENT 'Código interno autogenerado: PRO-000001',
  sku VARCHAR(40) NULL COMMENT 'SKU/Código de barras externo',
  tipo_base ENUM('simple','fabricado','servicio','insumo') DEFAULT 'simple' COMMENT 'Tipo base del producto',
  precio_base DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Precio de lista con IGV incluido',
  moneda CHAR(3) DEFAULT 'PEN' COMMENT 'Moneda del precio',
  tax_category VARCHAR(10) DEFAULT 'IGV' COMMENT 'Categoría tributaria',
  controla_lote TINYINT(1) DEFAULT 0 COMMENT 'Si requiere control por lotes',
  vida_util_dias SMALLINT UNSIGNED NULL COMMENT 'Días de vida útil desde compra',
  es_variantes TINYINT(1) DEFAULT 0 COMMENT 'Si tiene variantes activas',
  atributos_basicos_json JSON NULL COMMENT 'Peso, dimensiones, perecible, etc',
  reorder_point INT UNSIGNED NULL COMMENT 'Punto de reposición automática',
  activo TINYINT(1) DEFAULT 1 COMMENT 'Si el producto está activo',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (marca_id) REFERENCES marcas(id),
  FOREIGN KEY (unidad_medida_id) REFERENCES unidades_medida(id),
  UNIQUE KEY idx_productos_empresa_codigo (empresa_id, codigo),
  INDEX idx_productos_empresa_sku (empresa_id, sku),
  INDEX idx_productos_empresa_nombre (empresa_id, nombre),
  INDEX idx_productos_empresa_tipo (empresa_id, tipo_base),
  INDEX idx_productos_empresa_activo (empresa_id, activo),
  INDEX idx_productos_categoria (categoria_id),
  INDEX idx_productos_marca (marca_id)
);
```

---

---

## � **CRM BÁSICO (2 tablas)**

### **12. clientes**
```sql
CREATE TABLE clientes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(160) NOT NULL COMMENT 'Nombre completo o razón social',
  documento_tipo VARCHAR(10) NULL COMMENT 'DNI/RUC/CE/PASSPORT',
  documento_numero VARCHAR(20) NULL COMMENT 'Número del documento',
  telefono VARCHAR(20) NULL COMMENT 'Teléfono principal',
  email VARCHAR(120) NULL COMMENT 'Email principal',
  direccion VARCHAR(200) NULL COMMENT 'Dirección completa',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_clientes_empresa (empresa_id),
  INDEX idx_clientes_empresa_doc (empresa_id, documento_numero),
  INDEX idx_clientes_empresa_nombre (empresa_id, nombre)
);
```

### **13. proveedores**
```sql
CREATE TABLE proveedores (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(160) NOT NULL COMMENT 'Nombre completo o razón social',
  documento_tipo VARCHAR(10) NULL COMMENT 'DNI/RUC/CE',
  documento_numero VARCHAR(20) NULL COMMENT 'Número del documento',
  telefono VARCHAR(20) NULL COMMENT 'Teléfono principal',
  email VARCHAR(120) NULL COMMENT 'Email principal',
  direccion VARCHAR(200) NULL COMMENT 'Dirección completa',
  contacto_json JSON NULL COMMENT 'Información adicional de contacto',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_proveedores_empresa (empresa_id),
  INDEX idx_proveedores_empresa_nombre (empresa_id, nombre),
  INDEX idx_proveedores_documento (empresa_id, documento_numero)
);
```

---

## 💰 **NÚCLEO DE VENTAS (3 tablas)**

### **14. ventas**
```sql
CREATE TABLE ventas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL COMMENT 'Usuario que emite la venta',
  cliente_id BIGINT UNSIGNED NULL COMMENT 'Cliente opcional',
  fecha DATETIME NOT NULL COMMENT 'Fecha y hora de emisión',
  tipo_doc VARCHAR(15) DEFAULT 'interno' COMMENT 'interno/boleta/factura',
  estado VARCHAR(25) DEFAULT 'borrador' COMMENT 'borrador/pendiente/emitida/anulada',
  total_bruto DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Suma de subtotales antes de descuentos',
  total_descuento_items DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Suma de descuentos en items',
  descuento_global_tipo VARCHAR(10) NULL COMMENT 'pct/monto para descuento global',
  descuento_global_valor DECIMAL(14,2) NULL COMMENT 'Valor del descuento global',
  total_descuento_global DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Descuento global calculado',
  total_descuento DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Total descuentos (items + global)',
  total_neto DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Total final a pagar',
  total_base DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Base imponible (sin IGV)',
  total_igv DECIMAL(14,2) DEFAULT 0.00 COMMENT 'IGV total calculado',
  moneda CHAR(3) DEFAULT 'PEN' COMMENT 'Moneda de la venta',
  es_electronico TINYINT(1) DEFAULT 0 COMMENT 'Si es comprobante electrónico',
  cancel_reason VARCHAR(160) NULL COMMENT 'Razón de anulación',
  referencia_externa VARCHAR(50) NULL COMMENT 'Referencia externa opcional',
  tasa_cambio DECIMAL(10,6) NULL COMMENT 'Tasa de cambio si no es PEN',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  INDEX idx_ventas_empresa_fecha (empresa_id, fecha),
  INDEX idx_ventas_empresa_estado (empresa_id, estado),
  INDEX idx_ventas_empresa_tipo_doc (empresa_id, tipo_doc),
  INDEX idx_ventas_sucursal_fecha (sucursal_id, fecha),
  INDEX idx_ventas_usuario (user_id),
  INDEX idx_ventas_cliente (cliente_id)
);
```

### **15. venta_items**
```sql
CREATE TABLE venta_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL COMMENT 'Denormalizado para queries rápidas',
  venta_id BIGINT UNSIGNED NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  lote_id BIGINT UNSIGNED NULL COMMENT 'Lote específico vendido',
  cantidad DECIMAL(14,3) NOT NULL DEFAULT 0 COMMENT 'Cantidad vendida',
  precio_unit DECIMAL(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio unitario con IGV',
  descuento_tipo VARCHAR(10) NULL COMMENT 'pct/monto para descuento en línea',
  descuento_valor DECIMAL(14,4) NULL COMMENT 'Valor del descuento',
  subtotal_bruto DECIMAL(14,2) DEFAULT 0.00 COMMENT 'cantidad * precio_unit',
  subtotal_descuento DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Descuento calculado en línea',
  subtotal_neto DECIMAL(14,2) DEFAULT 0.00 COMMENT 'subtotal_bruto - subtotal_descuento',
  line_base DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Base imponible de la línea',
  line_igv DECIMAL(14,2) DEFAULT 0.00 COMMENT 'IGV de la línea',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id),
  INDEX idx_venta_items_venta (venta_id),
  INDEX idx_venta_items_producto (producto_id),
  INDEX idx_venta_items_empresa_producto (empresa_id, producto_id)
);
```

### **16. venta_pagos**
```sql
CREATE TABLE venta_pagos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  venta_id BIGINT UNSIGNED NOT NULL,
  caja_sesion_id BIGINT UNSIGNED NULL COMMENT 'Solo efectivo requiere sesión de caja',
  metodo VARCHAR(20) NOT NULL COMMENT 'efectivo/yape/tarjeta/transferencia/otros',
  monto DECIMAL(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Monto del pago',
  referencia VARCHAR(60) NULL COMMENT 'Código de operación/referencia',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (caja_sesion_id) REFERENCES caja_sesiones(id),
  INDEX idx_venta_pagos_venta (venta_id),
  INDEX idx_venta_pagos_caja (caja_sesion_id),
  INDEX idx_venta_pagos_metodo (metodo),
  INDEX idx_venta_pagos_empresa (empresa_id)
);
```

---

---

## � **INVENTARIO AVANZADO (4 tablas)**

### **17. stock_por_sucursal**
```sql
CREATE TABLE stock_por_sucursal (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL COMMENT 'Multi-tenant crítico',
  producto_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  lote_id BIGINT UNSIGNED NULL COMMENT 'NULL = stock general sin lote',
  stock_actual DECIMAL(14,3) DEFAULT 0.000 COMMENT 'Stock actual disponible',
  stock_reservado DECIMAL(14,3) DEFAULT 0.000 COMMENT 'Stock reservado en órdenes',
  min_stock DECIMAL(14,3) NULL COMMENT 'Stock mínimo para alertas',
  max_stock DECIMAL(14,3) NULL COMMENT 'Stock máximo recomendado',
  costo_promedio DECIMAL(14,4) DEFAULT 0.0000 COMMENT 'Costo promedio ponderado',
  updated_at TIMESTAMP NULL COMMENT 'Última actualización de stock',
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id),
  UNIQUE KEY idx_unique_producto_sucursal_lote (producto_id, sucursal_id, lote_id),
  INDEX idx_stock_empresa_producto (empresa_id, producto_id),
  INDEX idx_stock_empresa_sucursal (empresa_id, sucursal_id),
  INDEX idx_stock_producto_sucursal (producto_id, sucursal_id)
);
```

### **18. lotes**
```sql
CREATE TABLE lotes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  proveedor_id BIGINT UNSIGNED NULL COMMENT 'Proveedor del lote',
  codigo_lote VARCHAR(40) NOT NULL COMMENT 'Código del lote, ej: YYYYMMDD-001',
  fecha_produccion DATE NULL COMMENT 'Fecha de producción',
  fecha_vencimiento DATE NULL COMMENT 'NULL = sin vencimiento',
  estado_lote VARCHAR(20) DEFAULT 'activo' COMMENT 'activo/vencido/bloqueado/agotado',
  atributos_json JSON NULL COMMENT 'Atributos específicos del lote',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
  UNIQUE KEY idx_unique_lote_empresa_producto (empresa_id, producto_id, codigo_lote),
  INDEX idx_lotes_empresa_vencimiento (empresa_id, fecha_vencimiento),
  INDEX idx_lotes_producto (producto_id),
  INDEX idx_lotes_estado (estado_lote)
);
```

### **19. inventario_movimientos**
```sql
CREATE TABLE inventario_movimientos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  lote_id BIGINT UNSIGNED NULL COMMENT 'Lote específico del movimiento',
  tipo VARCHAR(20) NOT NULL COMMENT 'entrada/salida/ajuste/transferencia_in/transferencia_out',
  referencia_tipo VARCHAR(30) NOT NULL COMMENT 'venta/compra/ajuste/produccion/transferencia',
  referencia_id BIGINT UNSIGNED DEFAULT 0 COMMENT 'ID del documento origen',
  cantidad DECIMAL(14,3) NOT NULL COMMENT 'Cantidad del movimiento (+ o -)',
  costo_unitario DECIMAL(14,4) NULL COMMENT 'Costo unitario del movimiento',
  observaciones VARCHAR(255) NULL COMMENT 'Observaciones del movimiento',
  user_id BIGINT UNSIGNED NULL COMMENT 'Usuario que registra el movimiento',
  fecha DATETIME NOT NULL COMMENT 'Fecha y hora del movimiento',
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_inventario_empresa_producto_fecha (empresa_id, producto_id, fecha),
  INDEX idx_inventario_empresa_sucursal (empresa_id, sucursal_id),
  INDEX idx_inventario_referencia (empresa_id, referencia_tipo, referencia_id),
  INDEX idx_inventario_tipo (tipo),
  INDEX idx_inventario_usuario (user_id)
);
```

### **20. producto_categoria**
```sql
CREATE TABLE producto_categoria (
  producto_id BIGINT UNSIGNED NOT NULL,
  categoria_id BIGINT UNSIGNED NOT NULL,
  
  PRIMARY KEY (producto_id, categoria_id),
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
  INDEX idx_producto_categoria_categoria (categoria_id)
);
```

---

## 🚚 **COMPRAS (3 tablas)**

### **21. compras**
```sql
CREATE TABLE compras (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  proveedor_id BIGINT UNSIGNED NOT NULL,
  sucursal_destino_id BIGINT UNSIGNED NOT NULL COMMENT 'Sucursal donde llega la mercadería',
  user_id BIGINT UNSIGNED NOT NULL COMMENT 'Usuario que registra la compra',
  fecha DATETIME NOT NULL COMMENT 'Fecha de la compra',
  numero_doc VARCHAR(40) NULL COMMENT 'Número de documento del proveedor',
  tipo_doc VARCHAR(15) DEFAULT 'factura' COMMENT 'factura/boleta/recibo/nota',
  estado VARCHAR(15) DEFAULT 'borrador' COMMENT 'borrador/confirmada/recibida/anulada',
  total DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Total de la compra',
  moneda CHAR(3) DEFAULT 'PEN' COMMENT 'Moneda de la compra',
  observaciones TEXT NULL COMMENT 'Observaciones de la compra',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
  FOREIGN KEY (sucursal_destino_id) REFERENCES sucursales(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_compras_empresa_fecha (empresa_id, fecha),
  INDEX idx_compras_empresa_estado (empresa_id, estado),
  INDEX idx_compras_proveedor (proveedor_id),
  INDEX idx_compras_sucursal (sucursal_destino_id),
  INDEX idx_compras_usuario (user_id)
);
```

### **22. compra_items**
```sql
CREATE TABLE compra_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL COMMENT 'Denormalizado para queries',
  compra_id BIGINT UNSIGNED NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  lote_id BIGINT UNSIGNED NULL COMMENT 'Lote asignado al item',
  cantidad DECIMAL(14,3) NOT NULL DEFAULT 0 COMMENT 'Cantidad comprada',
  costo_unitario DECIMAL(14,4) NOT NULL DEFAULT 0.0000 COMMENT 'Costo unitario de compra',
  descuento_pct DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Descuento en porcentaje',
  subtotal DECIMAL(14,2) DEFAULT 0.00 COMMENT 'cantidad * costo_unitario - descuento',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (compra_id) REFERENCES compras(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id),
  INDEX idx_compra_items_compra (compra_id),
  INDEX idx_compra_items_producto (producto_id),
  INDEX idx_compra_items_empresa_producto (empresa_id, producto_id),
  INDEX idx_compra_items_lote (lote_id)
);
```

### **23. recepciones**
```sql
CREATE TABLE recepciones (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  compra_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL COMMENT 'Usuario que registra la recepción',
  fecha_recepcion DATETIME NOT NULL COMMENT 'Fecha y hora de recepción',
  estado VARCHAR(15) DEFAULT 'parcial' COMMENT 'parcial/completa/con_diferencias',
  observaciones TEXT NULL COMMENT 'Observaciones de la recepción',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (compra_id) REFERENCES compras(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_recepciones_empresa (empresa_id),
  INDEX idx_recepciones_compra (compra_id),
  INDEX idx_recepciones_sucursal (sucursal_id),
  INDEX idx_recepciones_fecha (fecha_recepcion)
);
```

---

---

## 🇵🇪 **SUNAT PERÚ (2 tablas)**

### **24. sunat_comprobantes**
```sql
CREATE TABLE sunat_comprobantes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  venta_id BIGINT UNSIGNED NOT NULL,
  sunat_serie_id BIGINT UNSIGNED NOT NULL,
  numero INT UNSIGNED NOT NULL COMMENT 'Número correlativo',
  tipo_comprobante VARCHAR(10) NOT NULL COMMENT 'boleta/factura/nota_credito/nota_debito',
  estado_envio VARCHAR(20) DEFAULT 'pendiente' COMMENT 'pendiente/aceptado/rechazado/error',
  hash_cdr VARCHAR(120) NULL COMMENT 'Hash del CDR de SUNAT',
  xml_path VARCHAR(255) NULL COMMENT 'Ruta del XML generado',
  cdr_path VARCHAR(255) NULL COMMENT 'Ruta del CDR de SUNAT',
  respuesta_json JSON NULL COMMENT 'Respuesta completa de SUNAT',
  fecha_emision DATETIME NOT NULL COMMENT 'Fecha de emisión del comprobante',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (sunat_serie_id) REFERENCES sunat_series(id),
  UNIQUE KEY idx_sunat_serie_numero (sunat_serie_id, numero),
  INDEX idx_sunat_empresa_estado (empresa_id, estado_envio),
  INDEX idx_sunat_venta (venta_id),
  INDEX idx_sunat_fecha_emision (fecha_emision)
);
```

### **25. sunat_series**
```sql
CREATE TABLE sunat_series (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  tipo_comprobante VARCHAR(10) NOT NULL COMMENT 'boleta/factura/nota_credito/nota_debito',
  serie VARCHAR(8) NOT NULL COMMENT 'Serie del comprobante: B001, F001',
  correlativo_actual INT UNSIGNED DEFAULT 0 COMMENT 'Último número usado',
  activa TINYINT(1) DEFAULT 1 COMMENT 'Si la serie está activa',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  UNIQUE KEY idx_sunat_empresa_tipo_serie (empresa_id, tipo_comprobante, serie),
  INDEX idx_sunat_empresa_activa (empresa_id, activa)
);
```

---

## 💰 **CAJA MÚLTIPLE (3 tablas)**

### **26. cajas**
```sql
CREATE TABLE cajas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(60) NOT NULL COMMENT 'Nombre de la caja: Caja Principal, Caja 2',
  codigo VARCHAR(20) NULL COMMENT 'Código único de la caja',
  tipo VARCHAR(15) DEFAULT 'principal' COMMENT 'principal/secundaria/movil',
  activa TINYINT(1) DEFAULT 1 COMMENT 'Si la caja está activa',
  configuracion_json JSON NULL COMMENT 'Configuraciones específicas de la caja',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  INDEX idx_cajas_empresa_sucursal (empresa_id, sucursal_id),
  INDEX idx_cajas_activa (activa)
);
```

### **27. caja_sesiones**
```sql
CREATE TABLE caja_sesiones (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  caja_id BIGINT UNSIGNED NOT NULL,
  user_apertura_id BIGINT UNSIGNED NOT NULL COMMENT 'Usuario que abre la sesión',
  user_cierre_id BIGINT UNSIGNED NULL COMMENT 'Usuario que cierra la sesión',
  codigo VARCHAR(20) NULL COMMENT 'Código de sesión: CX-YYYYMMDD-n',
  apertura_at DATETIME NOT NULL COMMENT 'Fecha y hora de apertura',
  cierre_at DATETIME NULL COMMENT 'Fecha y hora de cierre',
  monto_inicial DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Monto inicial en caja',
  monto_ingresos DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Ingresos adicionales',
  monto_retiros DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Retiros de caja',
  monto_ventas_efectivo DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Total ventas en efectivo',
  monto_declarado_cierre DECIMAL(14,2) NULL COMMENT 'Monto declarado al cierre',
  diferencia DECIMAL(14,2) NULL COMMENT 'Diferencia: declarado - calculado',
  estado ENUM('abierta','cerrada','anulada') DEFAULT 'abierta' COMMENT 'Estado de la sesión',
  observaciones TEXT NULL COMMENT 'Observaciones de la sesión',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (caja_id) REFERENCES cajas(id),
  FOREIGN KEY (user_apertura_id) REFERENCES users(id),
  FOREIGN KEY (user_cierre_id) REFERENCES users(id),
  INDEX idx_caja_sesiones_empresa_estado (empresa_id, estado),
  INDEX idx_caja_sesiones_caja (caja_id),
  INDEX idx_caja_sesiones_apertura (apertura_at)
);
```

### **28. caja_movimientos**
```sql
CREATE TABLE caja_movimientos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  caja_sesion_id BIGINT UNSIGNED NOT NULL,
  tipo VARCHAR(15) NOT NULL COMMENT 'ingreso/retiro/venta_efectivo',
  monto DECIMAL(14,2) NOT NULL COMMENT 'Monto del movimiento',
  concepto VARCHAR(120) NOT NULL COMMENT 'Concepto del movimiento',
  referencia_tipo VARCHAR(20) NULL COMMENT 'venta/gasto/reposicion',
  referencia_id BIGINT UNSIGNED NULL COMMENT 'ID del documento relacionado',
  user_id BIGINT UNSIGNED NOT NULL COMMENT 'Usuario que registra el movimiento',
  fecha DATETIME NOT NULL COMMENT 'Fecha y hora del movimiento',
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (caja_sesion_id) REFERENCES caja_sesiones(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_caja_movimientos_sesion (caja_sesion_id),
  INDEX idx_caja_movimientos_empresa_fecha (empresa_id, fecha),
  INDEX idx_caja_movimientos_tipo (tipo),
  INDEX idx_caja_movimientos_usuario (user_id)
);
```

---

---

## 🍞 **ESPECÍFICOS PANADERÍA (3 tablas)**

### **29. panaderia_recetas**
```sql
CREATE TABLE panaderia_recetas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  producto_final_id BIGINT UNSIGNED NOT NULL COMMENT 'Producto que se fabrica',
  nombre VARCHAR(120) NOT NULL COMMENT 'Nombre de la receta',
  rendimiento_cantidad DECIMAL(14,3) NOT NULL COMMENT 'Cantidad que produce la receta',
  tiempo_preparacion_min INT UNSIGNED NULL COMMENT 'Tiempo de preparación en minutos',
  tiempo_horneado_min INT UNSIGNED NULL COMMENT 'Tiempo de horneado en minutos',
  temperatura_celsius SMALLINT UNSIGNED NULL COMMENT 'Temperatura de horneado',
  instrucciones TEXT NULL COMMENT 'Instrucciones paso a paso',
  costo_estimado DECIMAL(14,4) DEFAULT 0.0000 COMMENT 'Costo calculado de la receta',
  activa TINYINT(1) DEFAULT 1 COMMENT 'Si la receta está activa',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (producto_final_id) REFERENCES productos(id),
  INDEX idx_panaderia_recetas_empresa (empresa_id),
  INDEX idx_panaderia_recetas_producto (producto_final_id),
  INDEX idx_panaderia_recetas_activa (activa)
);
```

### **30. panaderia_ingredientes**
```sql
CREATE TABLE panaderia_ingredientes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  receta_id BIGINT UNSIGNED NOT NULL,
  producto_ingrediente_id BIGINT UNSIGNED NOT NULL COMMENT 'Producto usado como ingrediente',
  cantidad_necesaria DECIMAL(14,3) NOT NULL COMMENT 'Cantidad necesaria del ingrediente',
  orden_agregado TINYINT UNSIGNED DEFAULT 1 COMMENT 'Orden en que se agrega el ingrediente',
  es_opcional TINYINT(1) DEFAULT 0 COMMENT 'Si el ingrediente es opcional',
  notas VARCHAR(200) NULL COMMENT 'Notas específicas del ingrediente',
  created_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (receta_id) REFERENCES panaderia_recetas(id) ON DELETE CASCADE,
  FOREIGN KEY (producto_ingrediente_id) REFERENCES productos(id),
  INDEX idx_panaderia_ingredientes_receta (receta_id),
  INDEX idx_panaderia_ingredientes_producto (producto_ingrediente_id),
  INDEX idx_panaderia_ingredientes_empresa (empresa_id)
);
```

### **31. panaderia_ordenes_produccion**
```sql
CREATE TABLE panaderia_ordenes_produccion (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  sucursal_id BIGINT UNSIGNED NOT NULL,
  receta_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL COMMENT 'Usuario que crea la orden',
  fecha_programada DATE NOT NULL COMMENT 'Fecha programada de producción',
  cantidad_objetivo DECIMAL(14,3) NOT NULL COMMENT 'Cantidad objetivo a producir',
  cantidad_real DECIMAL(14,3) NULL COMMENT 'Cantidad realmente producida',
  estado VARCHAR(20) DEFAULT 'planificada' COMMENT 'planificada/en_proceso/terminada/cancelada',
  costo_real DECIMAL(14,4) NULL COMMENT 'Costo real de producción',
  observaciones TEXT NULL COMMENT 'Observaciones de la producción',
  iniciada_at DATETIME NULL COMMENT 'Fecha y hora de inicio',
  terminada_at DATETIME NULL COMMENT 'Fecha y hora de finalización',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (receta_id) REFERENCES panaderia_recetas(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_panaderia_ordenes_empresa_fecha (empresa_id, fecha_programada),
  INDEX idx_panaderia_ordenes_sucursal (sucursal_id),
  INDEX idx_panaderia_ordenes_receta (receta_id),
  INDEX idx_panaderia_ordenes_estado (estado)
);
```

---

## � **ESPECÍFICOS FARMACIA (3 tablas)**

### **32. farmacia_prescripciones**
```sql
CREATE TABLE farmacia_prescripciones (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  venta_id BIGINT UNSIGNED NULL COMMENT 'Venta asociada si ya se dispensó',
  cliente_id BIGINT UNSIGNED NOT NULL,
  medico_nombre VARCHAR(120) NULL COMMENT 'Nombre del médico prescriptor',
  medico_cmp VARCHAR(20) NULL COMMENT 'Número CMP del médico',
  numero_receta VARCHAR(40) NULL COMMENT 'Número de la receta médica',
  fecha_emision DATE NOT NULL COMMENT 'Fecha de emisión de la prescripción',
  fecha_vencimiento DATE NOT NULL COMMENT 'Fecha de vencimiento de la prescripción',
  estado VARCHAR(20) DEFAULT 'pendiente' COMMENT 'pendiente/dispensada/vencida/anulada',
  observaciones TEXT NULL COMMENT 'Observaciones médicas',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  INDEX idx_farmacia_prescripciones_empresa (empresa_id),
  INDEX idx_farmacia_prescripciones_cliente (cliente_id),
  INDEX idx_farmacia_prescripciones_estado (estado),
  INDEX idx_farmacia_prescripciones_vencimiento (fecha_vencimiento)
);
```

### **33. farmacia_lotes_controlados**
```sql
CREATE TABLE farmacia_lotes_controlados (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  lote_id BIGINT UNSIGNED NOT NULL,
  tipo_control VARCHAR(20) NOT NULL COMMENT 'psicotrópico/estupefaciente/controlado',
  autorización_sanitaria VARCHAR(40) NULL COMMENT 'Número de autorización DIGEMID',
  requiere_receta TINYINT(1) DEFAULT 1 COMMENT 'Si requiere receta médica',
  stock_maximo_permitido DECIMAL(14,3) NULL COMMENT 'Stock máximo permitido',
  observaciones TEXT NULL COMMENT 'Observaciones del control',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (lote_id) REFERENCES lotes(id),
  INDEX idx_farmacia_lotes_empresa (empresa_id),
  INDEX idx_farmacia_lotes_tipo (tipo_control),
  INDEX idx_farmacia_lotes_lote (lote_id)
);
```

### **34. farmacia_proveedores_autorizados**
```sql
CREATE TABLE farmacia_proveedores_autorizados (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  proveedor_id BIGINT UNSIGNED NOT NULL,
  tipo_productos VARCHAR(30) NOT NULL COMMENT 'medicamentos/dispositivos/cosmeticos',
  numero_autorizacion VARCHAR(40) NULL COMMENT 'Número de autorización DIGEMID',
  fecha_vencimiento_autorizacion DATE NULL COMMENT 'Vencimiento de la autorización',
  contacto_farmaceutico VARCHAR(120) NULL COMMENT 'Contacto del químico farmacéutico',
  activo TINYINT(1) DEFAULT 1 COMMENT 'Si la autorización está vigente',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
  INDEX idx_farmacia_proveedores_empresa (empresa_id),
  INDEX idx_farmacia_proveedores_proveedor (proveedor_id),
  INDEX idx_farmacia_proveedores_tipo (tipo_productos),
  INDEX idx_farmacia_proveedores_vencimiento (fecha_vencimiento_autorizacion)
);
```

---

## 📋 **GESTIÓN OPERATIVA (4 tablas)**

### **35. empleados**
```sql
CREATE TABLE empleados (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NULL COMMENT 'Usuario del sistema asociado (opcional)',
  sucursal_principal_id BIGINT UNSIGNED NULL COMMENT 'Sucursal principal del empleado',
  codigo_empleado VARCHAR(20) NULL COMMENT 'Código interno del empleado',
  nombres VARCHAR(80) NOT NULL COMMENT 'Nombres del empleado',
  apellidos VARCHAR(80) NOT NULL COMMENT 'Apellidos del empleado',
  documento_tipo VARCHAR(10) NOT NULL COMMENT 'DNI/CE/PASSPORT',
  documento_numero VARCHAR(20) NOT NULL COMMENT 'Número del documento',
  telefono VARCHAR(20) NULL COMMENT 'Teléfono del empleado',
  email VARCHAR(120) NULL COMMENT 'Email del empleado',
  cargo VARCHAR(60) NULL COMMENT 'Cargo del empleado',
  fecha_ingreso DATE NOT NULL COMMENT 'Fecha de ingreso',
  fecha_salida DATE NULL COMMENT 'Fecha de salida (NULL = activo)',
  salario_base DECIMAL(14,2) NULL COMMENT 'Salario base mensual',
  activo TINYINT(1) DEFAULT 1 COMMENT 'Si el empleado está activo',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (sucursal_principal_id) REFERENCES sucursales(id),
  INDEX idx_empleados_empresa (empresa_id),
  INDEX idx_empleados_documento (empresa_id, documento_numero),
  INDEX idx_empleados_activo (activo),
  INDEX idx_empleados_sucursal (sucursal_principal_id)
);
```

### **36. turnos**
```sql
CREATE TABLE turnos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(60) NOT NULL COMMENT 'Nombre del turno: Mañana, Tarde, Noche',
  hora_inicio TIME NOT NULL COMMENT 'Hora de inicio del turno',
  hora_fin TIME NOT NULL COMMENT 'Hora de fin del turno',
  tolerancia_min TINYINT UNSIGNED DEFAULT 5 COMMENT 'Tolerancia en minutos',
  activo TINYINT(1) DEFAULT 1 COMMENT 'Si el turno está activo',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_turnos_empresa (empresa_id),
  INDEX idx_turnos_activo (activo)
);
```

### **37. asistencias**
```sql
CREATE TABLE asistencias (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  empleado_id BIGINT UNSIGNED NOT NULL,
  turno_id BIGINT UNSIGNED NULL COMMENT 'Turno asignado',
  fecha DATE NOT NULL COMMENT 'Fecha de la asistencia',
  hora_entrada TIME NULL COMMENT 'Hora real de entrada',
  hora_salida TIME NULL COMMENT 'Hora real de salida',
  minutos_tardanza SMALLINT UNSIGNED DEFAULT 0 COMMENT 'Minutos de tardanza',
  estado VARCHAR(15) DEFAULT 'pendiente' COMMENT 'asistio/falto/tardanza/justificado',
  observaciones VARCHAR(200) NULL COMMENT 'Observaciones de la asistencia',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  FOREIGN KEY (empleado_id) REFERENCES empleados(id),
  FOREIGN KEY (turno_id) REFERENCES turnos(id),
  UNIQUE KEY idx_asistencias_empleado_fecha (empleado_id, fecha),
  INDEX idx_asistencias_empresa_fecha (empresa_id, fecha),
  INDEX idx_asistencias_estado (estado)
);
```

### **38. descuentos_promociones**
```sql
CREATE TABLE descuentos_promociones (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(100) NOT NULL COMMENT 'Nombre de la promoción',
  tipo VARCHAR(20) NOT NULL COMMENT 'porcentaje/monto_fijo/producto_gratis/combo',
  valor DECIMAL(14,2) NOT NULL COMMENT 'Valor del descuento',
  aplicable_a VARCHAR(20) DEFAULT 'productos' COMMENT 'productos/categorias/total_venta',
  fecha_inicio DATE NOT NULL COMMENT 'Fecha de inicio de la promoción',
  fecha_fin DATE NOT NULL COMMENT 'Fecha de fin de la promoción',
  dias_semana JSON NULL COMMENT 'Días de la semana aplicables [1,2,3,4,5,6,7]',
  hora_inicio TIME NULL COMMENT 'Hora de inicio (happy hour)',
  hora_fin TIME NULL COMMENT 'Hora de fin (happy hour)',
  monto_minimo DECIMAL(14,2) NULL COMMENT 'Monto mínimo de compra',
  limite_usos INT UNSIGNED NULL COMMENT 'Límite de usos de la promoción',
  usos_actuales INT UNSIGNED DEFAULT 0 COMMENT 'Usos actuales de la promoción',
  activa TINYINT(1) DEFAULT 1 COMMENT 'Si la promoción está activa',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  INDEX idx_descuentos_empresa (empresa_id),
  INDEX idx_descuentos_fechas (fecha_inicio, fecha_fin),
  INDEX idx_descuentos_activa (activa)
);
```

---

## 📊 **RESUMEN FINAL DEL ESQUEMA**

### **📋 DISTRIBUCIÓN REAL DE 38 TABLAS**
```
🏢 NÚCLEO EMPRESARIAL: 6 tablas
   ├── planes
   ├── empresas  
   ├── rubros
   ├── empresa_rubros
   ├── feature_flags
   └── sucursales

👥 USUARIOS Y ACCESO: 1 tabla
   └── users (modificado multi-tenant)

📦 CATÁLOGOS MAESTROS: 4 tablas
   ├── categorias
   ├── unidades_medida
   ├── marcas
   └── productos

👥 CRM BÁSICO: 2 tablas
   ├── clientes
   └── proveedores

💰 NÚCLEO DE VENTAS: 3 tablas
   ├── ventas
   ├── venta_items
   └── venta_pagos

📊 INVENTARIO AVANZADO: 4 tablas
   ├── stock_por_sucursal
   ├── lotes
   ├── inventario_movimientos
   └── producto_categoria

🚚 COMPRAS: 3 tablas
   ├── compras
   ├── compra_items
   └── recepciones

🇵� SUNAT PERÚ: 2 tablas
   ├── sunat_comprobantes
   └── sunat_series

💰 CAJA MÚLTIPLE: 3 tablas
   ├── cajas
   ├── caja_sesiones
   └── caja_movimientos

🍞 ESPECÍFICOS PANADERÍA: 3 tablas
   ├── panaderia_recetas
   ├── panaderia_ingredientes
   └── panaderia_ordenes_produccion

💊 ESPECÍFICOS FARMACIA: 3 tablas
   ├── farmacia_prescripciones
   ├── farmacia_lotes_controlados
   └── farmacia_proveedores_autorizados

📋 GESTIÓN OPERATIVA: 4 tablas
   ├── empleados
   ├── turnos
   ├── asistencias
   └── descuentos_promociones

📊 TOTAL: 38 TABLAS PROFESIONALES
```

### **🔗 RELACIONES PRINCIPALES**
```
NÚCLEO EMPRESARIAL:
planes (1) ←→ (N) empresas
empresas (1) ←→ (N) rubros [empresa_rubros]
empresas (1) ←→ (N) sucursales
empresas (1) ←→ (N) users
empresas (1) ←→ (N) feature_flags

PRODUCTOS Y CATÁLOGOS:
empresas (1) ←→ (N) categorias
empresas (1) ←→ (N) marcas
categorias (1) ←→ (N) productos
marcas (1) ←→ (N) productos
unidades_medida (1) ←→ (N) productos
productos (N) ←→ (N) categorias [producto_categoria]

INVENTARIO:
productos (1) ←→ (N) lotes
productos (1) ←→ (N) stock_por_sucursal
sucursales (1) ←→ (N) stock_por_sucursal
lotes (1) ←→ (N) inventario_movimientos

VENTAS:
ventas (1) ←→ (N) venta_items
ventas (1) ←→ (N) venta_pagos
venta_items (N) ←→ (1) productos
venta_items (N) ←→ (1) lotes [opcional]

COMPRAS:
compras (1) ←→ (N) compra_items
compras (1) ←→ (N) recepciones
compra_items (N) ←→ (1) productos
compra_items (N) ←→ (1) lotes [opcional]

CAJA:
cajas (1) ←→ (N) caja_sesiones
caja_sesiones (1) ←→ (N) caja_movimientos
caja_sesiones (1) ←→ (N) venta_pagos

SUNAT:
sunat_series (1) ←→ (N) sunat_comprobantes
ventas (1) ←→ (1) sunat_comprobantes [opcional]

MÓDULOS ESPECÍFICOS:
productos (1) ←→ (N) panaderia_recetas
panaderia_recetas (1) ←→ (N) panaderia_ingredientes
panaderia_recetas (1) ←→ (N) panaderia_ordenes_produccion
lotes (1) ←→ (1) farmacia_lotes_controlados [opcional]
clientes (1) ←→ (N) farmacia_prescripciones

GESTIÓN OPERATIVA:
empleados (1) ←→ (N) asistencias
turnos (1) ←→ (N) asistencias
users (1) ←→ (1) empleados [opcional]
```

### **🎯 CARACTERÍSTICAS DEL ESQUEMA**
```
✅ MULTI-TENANT: Todas las tablas tienen empresa_id
✅ MULTI-RUBRO: Soporte para diferentes tipos de negocio
✅ MULTI-SUCURSAL: Inventario y operaciones por sucursal
✅ LOTES: Control total de vencimientos y trazabilidad
✅ SUNAT: Facturación electrónica nativa para Perú
✅ PRODUCCIÓN: Módulo completo para panaderías
✅ FARMACIA: Control de medicamentos y prescripciones
✅ CAJA MÚLTIPLE: Varias cajas por sucursal
✅ EMPLEADOS: Gestión de personal y asistencias
✅ PROMOCIONES: Sistema flexible de descuentos
```

---

## �🎯 **VALIDACIONES DE ESQUEMA**

### **✅ Checklist de Verificación**
```
□ Todas las tablas de negocio tienen empresa_id
□ Todas las tablas tienen created_at/updated_at
□ Todos los foreign keys están definidos
□ Todos los índices necesarios están creados
□ Tipos de datos son consistentes
□ Nombres de columnas siguen convenciones
□ Comentarios están presentes en columnas importantes
□ Restricciones UNIQUE donde corresponde
□ Campos JSON están bien definidos
□ ENUMs tienen valores apropiados
```

### **🔍 Queries de Verificación**
```sql
-- Verificar que todas las tablas tengan empresa_id (excepto maestros)
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'smartket' 
  AND COLUMN_NAME = 'empresa_id'
  AND TABLE_NAME NOT IN ('planes', 'rubros', 'unidades_medida');

-- Verificar foreign keys
SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'smartket' 
  AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Verificar índices
SELECT TABLE_NAME, INDEX_NAME, COLUMN_NAME
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'smartket'
ORDER BY TABLE_NAME, INDEX_NAME;
```

---

**📋 ESTE ESQUEMA ES LA DEFINICIÓN AUTORITATIVA DE LA BASE DE DATOS**

*Actualizado: 31 Agosto 2025*  
*Estado: 📋 ESQUEMA DEFINITIVO - 38 MIGRACIONES*  
*Uso: Base para generación de migraciones profesionales*
