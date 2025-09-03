# 🔧 SmartKet ERP - Corrección de Migraciones

**Versión:** 1.0  
**Fecha:** 30 Agosto 2025  
**Estado:** 📋 ANÁLISIS COMPLETO DE DISCREPANCIAS  

---

## 🎯 **PROPÓSITO DE ESTE DOCUMENTO**

Este documento identifica las **discrepancias exactas** entre:
1. **Esquema documentado** en `DATABASE_SCHEMA.md`
2. **Migraciones actuales** en la base de datos

### **🚨 IMPORTANTE**
- NO ejecutar migraciones hasta leer este documento completo
- Cada discrepancia requiere una migración correctiva específica
- Verificar estado actual antes de aplicar correcciones

---

## 📊 **ANÁLISIS DE DISCREPANCIAS**

### **🏢 Tabla: empresas**

#### **🔍 Diferencias Detectadas**
```sql
-- ❌ PROBLEMA: Campo duplicado 'timezone'
-- Estado actual: Existe column 'timezone' 
-- Esquema documentado: También define 'timezone'
-- ACCIÓN: Verificar si ya existe antes de agregar

-- ❌ PROBLEMA: Campos faltantes
-- Campo faltante: 'tipo_rubro' (esquema: VARCHAR(20) DEFAULT 'panaderia')
-- Campo faltante: 'features_json' (esquema: JSON NULL)
-- Campo faltante: 'sucursales_enabled' (esquema: TINYINT(1) DEFAULT 0)
-- Campo faltante: 'sucursales_count' (esquema: INT UNSIGNED DEFAULT 1)
-- Campo faltante: 'allow_negative_stock' (esquema: TINYINT(1) DEFAULT 0)
-- Campo faltante: 'precio_incluye_igv' (esquema: TINYINT(1) DEFAULT 1)
-- Campo faltante: 'connection_name' (esquema: VARCHAR(40) NULL)

-- ❌ PROBLEMA: Índices faltantes
-- Índice faltante: idx_empresas_tipo_rubro
-- Índice faltante: idx_empresas_plan
```

#### **✅ Migración Correctiva**
```sql
-- 2024_08_30_120001_fix_empresas_table.php
ALTER TABLE empresas 
ADD COLUMN IF NOT EXISTS tipo_rubro VARCHAR(20) DEFAULT 'panaderia' AFTER ruc,
ADD COLUMN IF NOT EXISTS features_json JSON NULL AFTER plan_id,
ADD COLUMN IF NOT EXISTS sucursales_enabled TINYINT(1) DEFAULT 0 AFTER features_json,
ADD COLUMN IF NOT EXISTS sucursales_count INT UNSIGNED DEFAULT 1 AFTER sucursales_enabled,
ADD COLUMN IF NOT EXISTS allow_negative_stock TINYINT(1) DEFAULT 0 AFTER sucursales_count,
ADD COLUMN IF NOT EXISTS precio_incluye_igv TINYINT(1) DEFAULT 1 AFTER allow_negative_stock,
ADD COLUMN IF NOT EXISTS connection_name VARCHAR(40) NULL AFTER timezone;

-- Crear índices si no existen
CREATE INDEX IF NOT EXISTS idx_empresas_tipo_rubro ON empresas(tipo_rubro);
CREATE INDEX IF NOT EXISTS idx_empresas_plan ON empresas(plan_id);
```

---

### **📋 Tabla: planes**

#### **🔍 Diferencias Detectadas**
```sql
-- ❌ PROBLEMA: Tabla no existe
-- Estado actual: No existe tabla 'planes'
-- Esquema documentado: Tabla completa definida
-- ACCIÓN: Crear tabla completa
```

#### **✅ Migración Correctiva**
```sql
-- 2024_08_30_120002_create_planes_table.php
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

-- Insertar planes básicos
INSERT INTO planes (nombre, max_usuarios, max_sucursales, max_productos, activo) VALUES
('FREE_BASIC', 2, 1, 100, 1),
('STANDARD', 5, 3, 1000, 1),
('PRO', 15, 10, 5000, 1),
('ENTERPRISE', 50, 50, 50000, 1);
```

---

### **🏢 Tabla: sucursales**

#### **🔍 Diferencias Detectadas**
```sql
-- ❌ PROBLEMA: Tabla no existe
-- Estado actual: No existe tabla 'sucursales'
-- Esquema documentado: Tabla completa definida
-- ACCIÓN: Crear tabla completa
```

#### **✅ Migración Correctiva**
```sql
-- 2024_08_30_120003_create_sucursales_table.php
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

### **👤 Tabla: usuarios**

#### **🔍 Diferencias Detectadas**
```sql
-- ❌ PROBLEMA: Nombre de tabla incorrecto
-- Estado actual: Tabla 'users' (Laravel default)
-- Esquema documentado: Tabla 'usuarios'
-- ACCIÓN: Rename table o crear nueva

-- ❌ PROBLEMA: Campos faltantes
-- Campo faltante: 'sucursal_id' (esquema: BIGINT UNSIGNED NULL)
-- Campo faltante: 'rol_principal' (esquema: VARCHAR(30) DEFAULT 'staff')
-- Campo faltante: 'activo' (esquema: TINYINT(1) DEFAULT 1)
-- Campo faltante: 'last_login_at' (esquema: DATETIME NULL)

-- ❌ PROBLEMA: Campos con nombre diferente
-- Campo actual: 'name' → Esquema: 'nombre'
-- Campo actual: 'password' → Esquema: 'password_hash'
```

#### **✅ Migración Correctiva**
```sql
-- 2024_08_30_120004_fix_users_to_usuarios.php

-- Opción 1: Renombrar tabla y ajustar campos
RENAME TABLE users TO usuarios;

ALTER TABLE usuarios 
ADD COLUMN empresa_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN sucursal_id BIGINT UNSIGNED NULL AFTER empresa_id,
CHANGE COLUMN name nombre VARCHAR(120) NOT NULL,
CHANGE COLUMN password password_hash VARCHAR(255) NOT NULL,
ADD COLUMN rol_principal VARCHAR(30) DEFAULT 'staff' AFTER password_hash,
ADD COLUMN activo TINYINT(1) DEFAULT 1 AFTER rol_principal,
ADD COLUMN last_login_at DATETIME NULL AFTER activo;

-- Añadir foreign keys
ALTER TABLE usuarios
ADD FOREIGN KEY (empresa_id) REFERENCES empresas(id),
ADD FOREIGN KEY (sucursal_id) REFERENCES sucursales(id);

-- Crear índices
CREATE UNIQUE INDEX idx_usuarios_empresa_email ON usuarios(empresa_id, email);
CREATE INDEX idx_usuarios_empresa_rol ON usuarios(empresa_id, rol_principal);
CREATE INDEX idx_usuarios_sucursal ON usuarios(sucursal_id);
```

---

### **🚩 Tabla: feature_flags**

#### **🔍 Diferencias Detectadas**
```sql
-- ❌ PROBLEMA: Tabla no existe
-- Estado actual: No existe tabla 'feature_flags'
-- Esquema documentado: Tabla completa definida
-- ACCIÓN: Crear tabla completa
```

#### **✅ Migración Correctiva**
```sql
-- 2024_08_30_120005_create_feature_flags_table.php
CREATE TABLE feature_flags (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id BIGINT UNSIGNED NOT NULL,
  feature_key VARCHAR(60) NOT NULL COMMENT 'pos/multi_sucursal/lotes/caja/etc',
  enabled TINYINT(1) DEFAULT 0 COMMENT 'Si la feature está habilitada',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  
  FOREIGN KEY (empresa_id) REFERENCES empresas(id),
  UNIQUE KEY idx_feature_empresa_key (empresa_id, feature_key),
  INDEX idx_feature_empresa_enabled (empresa_id, enabled)
);
```

---

### **📦 Tabla: productos**

#### **🔍 Diferencias Detectadas**
```sql
-- ❌ PROBLEMA: Campos faltantes/diferentes
-- Campo faltante: 'codigo' (esquema: VARCHAR(30) NULL autogenerado)
-- Campo actual: 'codigo_barra' → Esquema: 'sku'  
-- Campo faltante: 'tipo_base' (esquema: ENUM('simple','fabricado','servicio','insumo'))
-- Campo faltante: 'tax_category' (esquema: VARCHAR(10) DEFAULT 'IGV')
-- Campo faltante: 'controla_lote' (esquema: TINYINT(1) DEFAULT 0)
-- Campo faltante: 'vida_util_dias' (esquema: SMALLINT UNSIGNED NULL)
-- Campo faltante: 'es_variantes' (esquema: TINYINT(1) DEFAULT 0)
-- Campo faltante: 'atributos_basicos_json' (esquema: JSON NULL)
-- Campo faltante: 'reorder_point' (esquema: INT UNSIGNED NULL)
```

#### **✅ Migración Correctiva**
```sql
-- 2024_08_30_120006_fix_productos_table.php
ALTER TABLE productos
ADD COLUMN codigo VARCHAR(30) NULL COMMENT 'Código interno autogenerado: PRO-000001' AFTER nombre,
CHANGE COLUMN codigo_barra sku VARCHAR(40) NULL COMMENT 'SKU/Código de barras externo',
ADD COLUMN tipo_base ENUM('simple','fabricado','servicio','insumo') DEFAULT 'simple' AFTER sku,
ADD COLUMN tax_category VARCHAR(10) DEFAULT 'IGV' AFTER moneda,
ADD COLUMN controla_lote TINYINT(1) DEFAULT 0 AFTER tax_category,
ADD COLUMN vida_util_dias SMALLINT UNSIGNED NULL AFTER controla_lote,
ADD COLUMN es_variantes TINYINT(1) DEFAULT 0 AFTER vida_util_dias,
ADD COLUMN atributos_basicos_json JSON NULL AFTER es_variantes,
ADD COLUMN reorder_point INT UNSIGNED NULL AFTER atributos_basicos_json;

-- Crear índices faltantes
CREATE UNIQUE INDEX IF NOT EXISTS idx_productos_empresa_codigo ON productos(empresa_id, codigo);
CREATE INDEX IF NOT EXISTS idx_productos_empresa_sku ON productos(empresa_id, sku);
CREATE INDEX IF NOT EXISTS idx_productos_empresa_tipo ON productos(empresa_id, tipo_base);
```

---

### **📊 Tabla: ventas**

#### **🔍 Diferencias Detectadas**
```sql
-- ❌ PROBLEMA: Estructura de campos diferente
-- Campo actual: 'total' → Esquema: múltiples campos de totales
-- Campos faltantes: 'total_bruto', 'total_descuento_items', etc.
-- Campo faltante: 'tipo_doc' (esquema: VARCHAR(15) DEFAULT 'interno')
-- Campo faltante: 'descuento_global_*' (esquema: campos de descuento global)
-- Campo faltante: 'total_base', 'total_igv' (esquema: campos fiscales)
-- Campo faltante: 'es_electronico' (esquema: TINYINT(1) DEFAULT 0)
-- Campo faltante: 'cancel_reason' (esquema: VARCHAR(160) NULL)
```

#### **✅ Migración Correctiva**
```sql
-- 2024_08_30_120007_fix_ventas_table.php
ALTER TABLE ventas
ADD COLUMN tipo_doc VARCHAR(15) DEFAULT 'interno' AFTER cliente_id,
CHANGE COLUMN total total_bruto DECIMAL(14,2) DEFAULT 0.00,
ADD COLUMN total_descuento_items DECIMAL(14,2) DEFAULT 0.00 AFTER total_bruto,
ADD COLUMN descuento_global_tipo VARCHAR(10) NULL AFTER total_descuento_items,
ADD COLUMN descuento_global_valor DECIMAL(14,2) NULL AFTER descuento_global_tipo,
ADD COLUMN total_descuento_global DECIMAL(14,2) DEFAULT 0.00 AFTER descuento_global_valor,
ADD COLUMN total_descuento DECIMAL(14,2) DEFAULT 0.00 AFTER total_descuento_global,
ADD COLUMN total_neto DECIMAL(14,2) DEFAULT 0.00 AFTER total_descuento,
ADD COLUMN total_base DECIMAL(14,2) DEFAULT 0.00 AFTER total_neto,
ADD COLUMN total_igv DECIMAL(14,2) DEFAULT 0.00 AFTER total_base,
ADD COLUMN es_electronico TINYINT(1) DEFAULT 0 AFTER moneda,
ADD COLUMN cancel_reason VARCHAR(160) NULL AFTER es_electronico,
ADD COLUMN referencia_externa VARCHAR(50) NULL AFTER cancel_reason,
ADD COLUMN tasa_cambio DECIMAL(10,6) NULL AFTER referencia_externa;

-- Crear índices faltantes
CREATE INDEX IF NOT EXISTS idx_ventas_empresa_tipo_doc ON ventas(empresa_id, tipo_doc);
```

---

### **🛒 Tabla: venta_detalles → venta_items**

#### **🔍 Diferencias Detectadas**
```sql
-- ❌ PROBLEMA: Nombre de tabla incorrecto
-- Estado actual: 'venta_detalles'
-- Esquema documentado: 'venta_items'
-- ACCIÓN: Rename table

-- ❌ PROBLEMA: Campos con estructura diferente
-- Campos actuales: 'precio', 'descuento' simples
-- Esquema: estructura compleja de precios y descuentos
```

#### **✅ Migración Correctiva**
```sql
-- 2024_08_30_120008_fix_venta_detalles_to_items.php
RENAME TABLE venta_detalles TO venta_items;

ALTER TABLE venta_items
ADD COLUMN variante_id BIGINT UNSIGNED NULL AFTER producto_id,
ADD COLUMN lote_id BIGINT UNSIGNED NULL AFTER variante_id,
CHANGE COLUMN precio precio_unit DECIMAL(14,2) NOT NULL DEFAULT 0.00,
ADD COLUMN descuento_tipo VARCHAR(10) NULL AFTER precio_unit,
ADD COLUMN descuento_valor DECIMAL(14,4) NULL AFTER descuento_tipo,
CHANGE COLUMN descuento subtotal_descuento DECIMAL(14,2) DEFAULT 0.00,
ADD COLUMN subtotal_bruto DECIMAL(14,2) DEFAULT 0.00 AFTER descuento_valor,
ADD COLUMN subtotal_neto DECIMAL(14,2) DEFAULT 0.00 AFTER subtotal_descuento,
ADD COLUMN line_base DECIMAL(14,2) DEFAULT 0.00 AFTER subtotal_neto,
ADD COLUMN line_igv DECIMAL(14,2) DEFAULT 0.00 AFTER line_base;

-- Agregar foreign keys faltantes
ALTER TABLE venta_items
ADD FOREIGN KEY (lote_id) REFERENCES lotes(id);

-- Crear índices
CREATE INDEX IF NOT EXISTS idx_venta_items_empresa_producto ON venta_items(empresa_id, producto_id);
```

---

## 📋 **TABLAS COMPLETAMENTE FALTANTES**

### **📦 Tablas de Inventario**
```sql
-- FALTANTES: producto_sucursal_stock, lotes, inventario_movs
-- ACCIÓN: Crear migraciones completas
```

### **👥 Tablas de Personas**
```sql  
-- FALTANTES: clientes, proveedores
-- ACCIÓN: Crear migraciones completas
```

### **🛒 Tablas de Compras**
```sql
-- FALTANTES: compras, compra_items
-- ACCIÓN: Crear migraciones completas
```

### **🔧 Tablas de Features**
```sql
-- FALTANTES: venta_pagos, caja_sesiones, producto_variantes, sunat_comprobantes
-- ACCIÓN: Crear migraciones completas (con feature flags)
```

---

## 🎯 **PLAN DE EJECUCIÓN**

### **🔄 Orden de Migraciones**
```
1. 2024_08_30_120001_fix_empresas_table.php
2. 2024_08_30_120002_create_planes_table.php  
3. 2024_08_30_120003_create_sucursales_table.php
4. 2024_08_30_120004_fix_users_to_usuarios.php
5. 2024_08_30_120005_create_feature_flags_table.php
6. 2024_08_30_120006_fix_productos_table.php
7. 2024_08_30_120007_fix_ventas_table.php
8. 2024_08_30_120008_fix_venta_detalles_to_items.php
9. 2024_08_30_120009_create_categorias_table.php
10. 2024_08_30_120010_create_producto_categoria_table.php
11. 2024_08_30_120011_create_clientes_table.php
12. 2024_08_30_120012_create_proveedores_table.php
13. 2024_08_30_120013_create_lotes_table.php
14. 2024_08_30_120014_create_producto_sucursal_stock_table.php
15. 2024_08_30_120015_create_inventario_movs_table.php
16. 2024_08_30_120016_create_compras_table.php
17. 2024_08_30_120017_create_compra_items_table.php
18. 2024_08_30_120018_create_venta_pagos_table.php
19. 2024_08_30_120019_create_caja_sesiones_table.php
20. 2024_08_30_120020_create_producto_variantes_table.php
21. 2024_08_30_120021_create_sunat_comprobantes_table.php
```

### **⚠️ PRECAUCIONES**
```
1. BACKUP completo antes de ejecutar
2. Ejecutar en entorno de desarrollo primero
3. Verificar foreign key constraints
4. Probar con datos de ejemplo
5. Verificar que aplicaciones no se rompan
```

### **🔍 VERIFICACIÓN POST-MIGRACIÓN**
```sql
-- Verificar que todas las tablas existen
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'smartket';

-- Verificar que todos los foreign keys están creados
SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'smartket' 
  AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Verificar que todos los índices están creados
SELECT TABLE_NAME, INDEX_NAME 
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_SCHEMA = 'smartket'
  AND INDEX_NAME != 'PRIMARY';
```

---

## 🚨 **COMANDOS DE EJECUCIÓN**

### **📝 Crear Migraciones**
```bash
# Crear todas las migraciones correctivas
php artisan make:migration fix_empresas_table
php artisan make:migration create_planes_table
php artisan make:migration create_sucursales_table
# ... continuar con todas
```

### **▶️ Ejecutar Migraciones**
```bash
# Verificar estado actual
php artisan migrate:status

# Ejecutar migraciones pendientes
php artisan migrate

# En caso de problemas, rollback específico
php artisan migrate:rollback --step=1
```

### **🔍 Verificar Resultado**
```bash
# Verificar todas las tablas
php artisan migrate:status

# Ejecutar seeders si es necesario
php artisan db:seed --class=PlanesSeeder
php artisan db:seed --class=EmpresaDefaultSeeder
```

---

**🔧 ESTE DOCUMENTO MAPEA TODAS LAS CORRECCIONES NECESARIAS**

*Actualizado: 30 Agosto 2025*  
*Estado: 📋 ANÁLISIS COMPLETO DE DISCREPANCIAS*  
*Próximo paso: Crear y ejecutar migraciones correctivas en orden*
