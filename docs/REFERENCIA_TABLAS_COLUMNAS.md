# 📋 REFERENCIA RÁPIDA - TABLAS Y COLUMNAS SmartKet v4

> **Referencia directa de nombres de tablas y columnas con tipos de datos**  
> Para consulta rápida durante desarrollo backend - NO asumir nombres

---

## 🎯 TABLAS PRINCIPALES DEL ERP

### **productos** (13 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
categoria_id          bigint(64) NOT NULL → categorias.id  
nombre                varchar(150) NOT NULL
codigo_interno        varchar(30) NULL
codigo_barra          varchar(50) NULL
descripcion           text NULL
precio_costo          numeric(10,4) NOT NULL DEFAULT 0
precio_venta          numeric(10,4) NOT NULL DEFAULT 0
margen_ganancia       numeric(5,2) NOT NULL DEFAULT 0
incluye_igv           smallint(16) NOT NULL DEFAULT 1
unidad_medida         varchar(20) NOT NULL DEFAULT 'UNIDAD'
permite_decimales     smallint(16) NOT NULL DEFAULT 0
maneja_stock          smallint(16) NOT NULL DEFAULT 1
stock_minimo          numeric(10,2) NOT NULL DEFAULT 0
stock_maximo          numeric(10,2) NOT NULL DEFAULT 0
activo                smallint(16) NOT NULL DEFAULT 1
imagen_url            varchar(255) NULL
extras_json           json NULL
created_at            timestamp NULL
updated_at            timestamp NULL
marca_id              bigint(64) NULL → marcas.id
unidad_medida_id      bigint(64) NULL → unidades_medida.id
```

### **categorias** (59 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
nombre                varchar(120) NOT NULL
codigo                varchar(20) NULL
descripcion           text NULL
color                 varchar(7) NOT NULL DEFAULT '#6B7280'
icono                 varchar(50) NOT NULL DEFAULT '📦'
activa                smallint(16) NOT NULL DEFAULT 1
productos_count       integer(32) NOT NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **marcas** (44 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
nombre                varchar(120) NOT NULL
codigo                varchar(20) NULL
descripcion           text NULL
color                 varchar(7) NOT NULL DEFAULT '#6B7280'
icono                 varchar(50) NOT NULL DEFAULT '🏪'
activa                smallint(16) NOT NULL DEFAULT 1
productos_count       integer(32) NOT NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **unidades_medida** (47 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
nombre                varchar(80) NOT NULL
abreviacion           varchar(10) NOT NULL
tipo                  varchar(20) NOT NULL DEFAULT 'GENERAL'
icono                 varchar(50) NOT NULL DEFAULT '⚖️'
activa                smallint(16) NOT NULL DEFAULT 1
productos_count       integer(32) NOT NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **producto_stocks** (13 registros)
```sql
id                      bigint(64) NOT NULL
empresa_id              bigint(64) NOT NULL → empresas.id
producto_id             bigint(64) NOT NULL → productos.id
sucursal_id             bigint(64) NOT NULL → sucursales.id
cantidad_actual         numeric(10,2) NOT NULL DEFAULT 0
cantidad_reservada      numeric(10,2) NOT NULL DEFAULT 0
cantidad_disponible     numeric(10,2) NOT NULL DEFAULT 0
costo_promedio          numeric(10,4) NOT NULL DEFAULT 0
ultimo_movimiento       timestamp NULL
created_at              timestamp NULL
updated_at              timestamp NULL
alerta_vencimiento_dias integer(32) NOT NULL DEFAULT 30
maneja_lotes            boolean NOT NULL DEFAULT false
```

---

## 💰 VENTAS Y TRANSACCIONES

### **ventas** (206 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
sucursal_id           bigint(64) NOT NULL → sucursales.id
usuario_id            bigint(64) NOT NULL → users.id
cliente_id            bigint(64) NULL → clientes.id
codigo_venta          varchar(50) NOT NULL
fecha_venta           date NOT NULL
hora_venta            time NOT NULL
subtotal              numeric(10,2) NOT NULL DEFAULT 0
igv                   numeric(10,2) NOT NULL DEFAULT 0
descuento_total       numeric(10,2) NOT NULL DEFAULT 0
total                 numeric(10,2) NOT NULL DEFAULT 0
estado                varchar(20) NOT NULL DEFAULT 'completada'
tipo_venta            varchar(20) NOT NULL DEFAULT 'presencial'
metodo_pago_principal varchar(30) NOT NULL DEFAULT 'efectivo'
observaciones         text NULL
extras_json           json NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **venta_detalles** (632 registros)
```sql
id                    bigint(64) NOT NULL
venta_id              bigint(64) NOT NULL → ventas.id
producto_id           bigint(64) NOT NULL → productos.id
cantidad              numeric(10,2) NOT NULL
precio_unitario       numeric(10,4) NOT NULL
descuento_unitario    numeric(10,4) NOT NULL DEFAULT 0
subtotal              numeric(10,2) NOT NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **venta_pagos** (206 registros)
```sql
id                    bigint(64) NOT NULL
venta_id              bigint(64) NOT NULL → ventas.id
metodo_pago_id        bigint(64) NOT NULL → metodos_pago.id
monto                 numeric(10,2) NOT NULL
referencia            varchar(100) NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **metodos_pago** (6 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
nombre                varchar(80) NOT NULL
codigo                varchar(20) NOT NULL
tipo                  varchar(20) NOT NULL DEFAULT 'efectivo'
requiere_referencia   smallint(16) NOT NULL DEFAULT 0
activo                smallint(16) NOT NULL DEFAULT 1
created_at            timestamp NULL
updated_at            timestamp NULL
```

---

## 👥 CLIENTES Y USUARIOS

### **clientes** (3 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
nombre                varchar(120) NOT NULL
tipo_documento        varchar(10) NOT NULL DEFAULT 'DNI'
numero_documento      varchar(15) NULL
telefono              varchar(20) NULL
email                 varchar(150) NULL
direccion             text NULL
activo                smallint(16) NOT NULL DEFAULT 1
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **users** (2 registros)
```sql
id                    bigint(64) NOT NULL
name                  varchar(255) NOT NULL
email                 varchar(150) NOT NULL
email_verified_at     timestamp NULL
password_hash         varchar(255) NOT NULL
remember_token        varchar(100) NULL
created_at            timestamp NULL
updated_at            timestamp NULL
empresa_id            bigint(64) NOT NULL → empresas.id
sucursal_id           bigint(64) NULL → sucursales.id
rol_principal         varchar(30) NOT NULL DEFAULT 'staff'
activo                smallint(16) NOT NULL DEFAULT 1
last_login_at         timestamp NULL
```

---

## 🏢 ESTRUCTURA EMPRESARIAL

### **empresas** (2 registros)
```sql
id                    bigint(64) NOT NULL
nombre                varchar(120) NOT NULL
ruc                   varchar(15) NULL
tiene_ruc             smallint(16) NOT NULL DEFAULT 0
plan_id               bigint(64) NOT NULL → planes.id
features_json         json NULL
sucursales_enabled    smallint(16) NOT NULL DEFAULT 0
sucursales_count      integer(32) NOT NULL DEFAULT 1
allow_negative_stock  smallint(16) NOT NULL DEFAULT 0
precio_incluye_igv    smallint(16) NOT NULL DEFAULT 1
timezone              varchar(40) NOT NULL DEFAULT 'America/Lima'
connection_name       varchar(40) NULL
created_at            timestamp NULL
updated_at            timestamp NULL
activa                boolean NOT NULL DEFAULT true
logo                  varchar(255) NULL
```

### **sucursales** (2 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
nombre                varchar(120) NOT NULL
codigo                varchar(20) NULL
direccion             text NULL
telefono              varchar(20) NULL
email                 varchar(150) NULL
activa                smallint(16) NOT NULL DEFAULT 1
es_principal          smallint(16) NOT NULL DEFAULT 0
created_at            timestamp NULL
updated_at            timestamp NULL
```

---

## 📦 INVENTARIO Y MOVIMIENTOS

### **inventario_movimientos** (632 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
sucursal_id           bigint(64) NOT NULL → sucursales.id
producto_id           bigint(64) NOT NULL → productos.id
tipo_movimiento       varchar(20) NOT NULL
cantidad              numeric(10,2) NOT NULL
precio_unitario       numeric(10,4) NOT NULL DEFAULT 0
observaciones         text NULL
documento_tipo        varchar(30) NULL
documento_numero      varchar(50) NULL
usuario_id            bigint(64) NOT NULL → users.id
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **lotes** (0 registros)
```sql
id                      bigint(64) NOT NULL
empresa_id              bigint(64) NOT NULL → empresas.id
producto_id             bigint(64) NOT NULL → productos.id
codigo_lote             varchar(50) NOT NULL
fecha_produccion        date NULL
fecha_vencimiento       date NULL
cantidad_inicial        numeric(10,2) NOT NULL
cantidad_actual         numeric(10,2) NOT NULL
costo_unitario          numeric(10,4) NOT NULL DEFAULT 0
activo                  smallint(16) NOT NULL DEFAULT 1
created_at              timestamp NULL
updated_at              timestamp NULL
```

---

## 🛒 COMPRAS Y PROVEEDORES

### **compras** (0 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
sucursal_id           bigint(64) NOT NULL → sucursales.id
proveedor_id          bigint(64) NOT NULL → proveedores.id
numero_compra         varchar(50) NOT NULL
fecha_compra          date NOT NULL
subtotal              numeric(10,2) NOT NULL DEFAULT 0
igv                   numeric(10,2) NOT NULL DEFAULT 0
total                 numeric(10,2) NOT NULL DEFAULT 0
estado                varchar(20) NOT NULL DEFAULT 'pendiente'
observaciones         text NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **compra_items** (0 registros)  
```sql
id                    bigint(64) NOT NULL
compra_id             bigint(64) NOT NULL → compras.id
producto_id           bigint(64) NOT NULL → productos.id
cantidad              numeric(10,2) NOT NULL
precio_unitario       numeric(10,4) NOT NULL
subtotal              numeric(10,2) NOT NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **proveedores** (0 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
nombre                varchar(120) NOT NULL
ruc                   varchar(15) NULL
telefono              varchar(20) NULL
email                 varchar(150) NULL
direccion             text NULL
contacto_nombre       varchar(120) NULL
activo                smallint(16) NOT NULL DEFAULT 1
created_at            timestamp NULL
updated_at            timestamp NULL
```

---

## 💰 CAJA Y SESIONES

### **cajas** (0 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
sucursal_id           bigint(64) NOT NULL → sucursales.id
nombre                varchar(120) NOT NULL
codigo                varchar(20) NULL
activa                smallint(16) NOT NULL DEFAULT 1
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **caja_sesiones** (0 registros)
```sql
id                    bigint(64) NOT NULL
caja_id               bigint(64) NOT NULL → cajas.id
usuario_id            bigint(64) NOT NULL → users.id
fecha_apertura        timestamp NOT NULL
fecha_cierre          timestamp NULL
monto_inicial         numeric(10,2) NOT NULL DEFAULT 0
monto_final           numeric(10,2) NULL
estado                varchar(20) NOT NULL DEFAULT 'abierta'
observaciones         text NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **caja_movimientos** (0 registros)
```sql
id                    bigint(64) NOT NULL
caja_sesion_id        bigint(64) NOT NULL → caja_sesiones.id
tipo_movimiento       varchar(20) NOT NULL
monto                 numeric(10,2) NOT NULL
descripcion           text NULL
referencia_tipo       varchar(30) NULL
referencia_id         bigint(64) NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

---

## 📊 REPORTES Y ANALYTICS

### **reportes** (0 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
usuario_id            bigint(64) NOT NULL → users.id
nombre                varchar(120) NOT NULL
tipo                  varchar(30) NOT NULL
fecha_generacion      timestamp NOT NULL
parametros_json       json NULL
ruta_archivo          varchar(255) NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **reporte_templates** (0 registros)
```sql
id                    bigint(64) NOT NULL
nombre                varchar(120) NOT NULL
tipo                  varchar(30) NOT NULL
configuracion_json    json NOT NULL
activo                smallint(16) NOT NULL DEFAULT 1
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **analytics_eventos** (0 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
usuario_id            bigint(64) NULL → users.id
evento                varchar(50) NOT NULL
categoria             varchar(30) NOT NULL
datos_json            json NULL
ip_address            varchar(45) NULL
user_agent            text NULL
created_at            timestamp NULL
```

---

## ⚙️ CONFIGURACIÓN Y PLANES

### **planes** (5 registros)
```sql
id                    bigint(64) NOT NULL
nombre                varchar(80) NOT NULL
precio_mensual        numeric(8,2) NOT NULL
precio_anual          numeric(8,2) NOT NULL
limite_productos      integer(32) NOT NULL DEFAULT -1
limite_usuarios       integer(32) NOT NULL DEFAULT -1
limite_sucursales     integer(32) NOT NULL DEFAULT 1
features_incluidas    json NULL
activo                smallint(16) NOT NULL DEFAULT 1
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **feature_flags** (10 registros)
```sql
id                    bigint(64) NOT NULL
nombre                varchar(80) NOT NULL
descripcion           text NULL
activo                smallint(16) NOT NULL DEFAULT 0
configuracion_json    json NULL
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **empresa_addons** (0 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
plan_addon_id         bigint(64) NOT NULL → plan_addons.id
fecha_activacion      date NOT NULL
fecha_vencimiento     date NULL
activo                smallint(16) NOT NULL DEFAULT 1
created_at            timestamp NULL
updated_at            timestamp NULL
```

---

## 🏷️ CATEGORIZACIÓN Y RUBROS

### **rubros** (2 registros)
```sql
id                    bigint(64) NOT NULL
nombre                varchar(120) NOT NULL
descripcion           text NULL
activo                smallint(16) NOT NULL DEFAULT 1
created_at            timestamp NULL
updated_at            timestamp NULL
```

### **empresa_rubros** (2 registros)
```sql
id                    bigint(64) NOT NULL
empresa_id            bigint(64) NOT NULL → empresas.id
rubro_id              bigint(64) NOT NULL → rubros.id
created_at            timestamp NULL
updated_at            timestamp NULL
```

---

## 📝 CONVENCIONES IMPORTANTES

### **Tipos de Datos**
- `bigint(64)` = Primary Keys, Foreign Keys
- `smallint(16)` = Boolean (0/1)
- `numeric(10,2)` = Cantidades/Stock  
- `numeric(10,4)` = Precios precisos
- `varchar(N)` = Texto con límite específico
- `text` = Texto largo sin límite
- `json` = Datos estructurados
- `timestamp` = Fechas con hora
- `date` = Solo fechas
- `time` = Solo tiempo
- `boolean` = True/False nativo

### **Convenciones de Nombres**
- Tablas: `snake_case` plural
- Columnas: `snake_case` singular
- FK: `tabla_id` (ej: `empresa_id`)
- Booleans: `activo/activa`, `habilitado`, etc.
- Estados: varchar con valores específicos
- Contadores: `*_count` (ej: `productos_count`)

### **Multi-tenant**
Todas las tablas principales tienen `empresa_id`:
- `productos`, `categorias`, `marcas`, `unidades_medida`
- `ventas`, `clientes`, `usuarios`
- `inventario_movimientos`, `cajas`, etc.

---

> 📅 **Actualizado**: 8 de septiembre, 2025  
> 🎯 **Propósito**: Referencia rápida para desarrollo backend  
> ⚠️ **IMPORTANTE**: NO asumir nombres - siempre consultar este documento
