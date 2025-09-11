# 📦 MÓDULO INVENTORY - SmartKet v4

## 🎯 **OBJETIVO**
Implementar el sistema completo de gestión de inventarios, productos y catálogos base para el ERP.

---

## 📋 **ESTADO ACTUAL**

### ⏳ **EN DESARROLLO**
- **Backend**: Iniciando desarrollo
- **Frontend**: Pendiente
- **Database**: Migraciones a crear
- **APIs**: A implementar

---

## 🏗️ **ARQUITECTURA MÓDULO INVENTORY**

### **📊 Entidades Principales**
```
Inventory Module:
├── Productos (corazón del inventario)
├── Categorías (organización productos)  
├── Marcas (fabricantes/proveedores)
├── Unidades de Medida (kg, lt, unidad, etc.)
├── Stock (cantidad disponible por sucursal)
└── Movimientos (entradas/salidas historial)
```

### **🔗 Relaciones Clave**
```sql
productos
├── categoría_id → categorias
├── marca_id → marcas  
├── unidad_medida_id → unidades_medida
├── empresa_id → empresas (multi-tenant)
└── stocks[] → product_stocks (por sucursal)

stocks
├── producto_id → productos
├── sucursal_id → sucursales
└── movimientos[] → inventario_movimientos

movimientos_inventario
├── producto_id → productos
├── sucursal_id → sucursales
├── usuario_id → usuarios
└── tipo: 'entrada' | 'salida' | 'ajuste' | 'transferencia'
```

---

## 🗄️ **ESTRUCTURA BASE DE DATOS**

### **1. Categorías**
```sql
CREATE TABLE categorias (
    id BIGSERIAL PRIMARY KEY,
    empresa_id BIGINT NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    categoria_padre_id BIGINT REFERENCES categorias(id), -- subcategorías
    activa BOOLEAN DEFAULT true,
    orden_display INTEGER DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(empresa_id, nombre)
);
```

### **2. Marcas**  
```sql
CREATE TABLE marcas (
    id BIGSERIAL PRIMARY KEY,
    empresa_id BIGINT NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    logo_url VARCHAR(500),
    sitio_web VARCHAR(500),
    activa BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(empresa_id, nombre)
);
```

### **3. Unidades de Medida**
```sql
CREATE TABLE unidades_medida (
    id BIGSERIAL PRIMARY KEY,
    empresa_id BIGINT NOT NULL REFERENCES empresas(id),
    nombre VARCHAR(100) NOT NULL, -- 'Kilogramo', 'Litro', 'Unidad'
    abreviatura VARCHAR(10) NOT NULL, -- 'kg', 'lt', 'ud'
    tipo ENUM('peso', 'volumen', 'longitud', 'unidad') NOT NULL,
    activa BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(empresa_id, nombre),
    UNIQUE(empresa_id, abreviatura)
);
```

### **4. Productos (Principal)**
```sql
CREATE TABLE productos (
    id BIGSERIAL PRIMARY KEY,
    empresa_id BIGINT NOT NULL REFERENCES empresas(id),
    categoria_id BIGINT REFERENCES categorias(id),
    marca_id BIGINT REFERENCES marcas(id),
    unidad_medida_id BIGINT NOT NULL REFERENCES unidades_medida(id),
    
    -- Información básica
    codigo VARCHAR(100), -- SKU/código interno
    codigo_barras VARCHAR(100), -- EAN/UPC
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    
    -- Precios
    precio_compra DECIMAL(10,2) DEFAULT 0,
    precio_venta DECIMAL(10,2) NOT NULL,
    margen_porcentaje DECIMAL(5,2),
    
    -- Control inventario
    maneja_stock BOOLEAN DEFAULT true,
    stock_minimo INTEGER DEFAULT 0,
    stock_maximo INTEGER,
    punto_reorden INTEGER,
    
    -- Estados
    activo BOOLEAN DEFAULT true,
    es_producto_compuesto BOOLEAN DEFAULT false, -- para combos/kits
    
    -- Metadata
    imagen_url VARCHAR(500),
    peso DECIMAL(8,3), -- en gramos
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(empresa_id, codigo),
    UNIQUE(empresa_id, codigo_barras)
);
```

### **5. Stock por Sucursal**
```sql
CREATE TABLE product_stocks (
    id BIGSERIAL PRIMARY KEY,
    producto_id BIGINT NOT NULL REFERENCES productos(id),
    sucursal_id BIGINT NOT NULL REFERENCES sucursales(id),
    
    -- Stock actual
    cantidad_actual DECIMAL(10,3) DEFAULT 0,
    cantidad_reservada DECIMAL(10,3) DEFAULT 0, -- pendiente de entrega
    cantidad_disponible DECIMAL(10,3) GENERATED ALWAYS AS (cantidad_actual - cantidad_reservada) STORED,
    
    -- Ubicación en almacén
    ubicacion VARCHAR(100), -- 'Estante A-1', 'Refrigerador B'
    
    -- Fechas importantes
    ultima_entrada TIMESTAMP,
    ultima_salida TIMESTAMP,
    ultima_actualizacion TIMESTAMP DEFAULT NOW(),
    
    UNIQUE(producto_id, sucursal_id)
);
```

### **6. Movimientos de Inventario**
```sql
CREATE TABLE inventario_movimientos (
    id BIGSERIAL PRIMARY KEY,
    producto_id BIGINT NOT NULL REFERENCES productos(id),
    sucursal_id BIGINT NOT NULL REFERENCES sucursales(id),
    usuario_id BIGINT NOT NULL REFERENCES usuarios(id),
    
    -- Tipo de movimiento
    tipo ENUM('entrada', 'salida', 'ajuste', 'transferencia') NOT NULL,
    concepto VARCHAR(255) NOT NULL, -- 'Compra', 'Venta', 'Merma', 'Ajuste inventario'
    
    -- Cantidades
    cantidad DECIMAL(10,3) NOT NULL,
    stock_anterior DECIMAL(10,3) NOT NULL,
    stock_actual DECIMAL(10,3) NOT NULL,
    
    -- Costo
    costo_unitario DECIMAL(10,2),
    costo_total DECIMAL(12,2),
    
    -- Referencias
    documento_referencia VARCHAR(100), -- número factura, guía, etc.
    movimiento_relacionado_id BIGINT REFERENCES inventario_movimientos(id), -- para transferencias
    
    -- Metadata
    observaciones TEXT,
    fecha_movimiento TIMESTAMP DEFAULT NOW(),
    created_at TIMESTAMP,
    
    INDEX(producto_id, fecha_movimiento),
    INDEX(sucursal_id, fecha_movimiento),
    INDEX(tipo, fecha_movimiento)
);
```

---

## 🎯 **PLAN DE DESARROLLO**

### **Fase 1: Backend Foundation (5-7 días)**
1. **Migraciones** → Crear tablas base
2. **Modelos** → Eloquent con relaciones
3. **Seeders** → Datos de prueba
4. **Controllers** → CRUD básico
5. **APIs** → Endpoints funcionales

### **Fase 2: Business Logic (3-4 días)**  
1. **Services** → Lógica de negocio
2. **Middleware** → Permisos y validaciones
3. **Events/Listeners** → Automatizaciones
4. **Validation** → Form Requests

### **Fase 3: Frontend Components (4-5 días)**
1. **Types** → Interfaces TypeScript  
2. **Components** → Tablas, forms, modals
3. **Pages** → CRUD completo
4. **Integration** → APIs conectadas

### **Fase 4: Advanced Features (2-3 días)**
1. **Import/Export** → CSV, Excel
2. **Reports** → Stock reports
3. **Testing** → Unit + Integration tests
4. **Documentation** → Guides

---

## 🔧 **APIS A IMPLEMENTAR**

### **Categorías (8 rutas)**
```bash
GET    /inventory/categories           # Listar categorías
POST   /inventory/categories           # Crear categoría
GET    /inventory/categories/{id}      # Ver categoría
PUT    /inventory/categories/{id}      # Actualizar categoría
DELETE /inventory/categories/{id}      # Eliminar categoría
GET    /inventory/categories/tree      # Árbol de categorías
POST   /inventory/categories/reorder   # Reordenar categorías
GET    /inventory/categories/{id}/products # Productos por categoría
```

### **Marcas (6 rutas)**
```bash
GET    /inventory/brands               # Listar marcas
POST   /inventory/brands               # Crear marca
GET    /inventory/brands/{id}          # Ver marca
PUT    /inventory/brands/{id}          # Actualizar marca
DELETE /inventory/brands/{id}          # Eliminar marca
GET    /inventory/brands/{id}/products # Productos por marca
```

### **Unidades de Medida (5 rutas)**
```bash
GET    /inventory/units                # Listar unidades
POST   /inventory/units                # Crear unidad
GET    /inventory/units/{id}           # Ver unidad
PUT    /inventory/units/{id}           # Actualizar unidad
DELETE /inventory/units/{id}           # Eliminar unidad
```

### **Productos (12 rutas)**
```bash
GET    /inventory/products             # Listar productos con filtros
POST   /inventory/products             # Crear producto
GET    /inventory/products/{id}        # Ver producto detallado
PUT    /inventory/products/{id}        # Actualizar producto
DELETE /inventory/products/{id}        # Eliminar producto
GET    /inventory/products/search      # Búsqueda productos
POST   /inventory/products/bulk-import # Importar productos CSV
GET    /inventory/products/export      # Exportar productos
POST   /inventory/products/{id}/duplicate # Duplicar producto
GET    /inventory/products/low-stock   # Productos con stock bajo
POST   /inventory/products/{id}/image  # Subir imagen producto
DELETE /inventory/products/{id}/image  # Eliminar imagen
```

### **Stock Management (8 rutas)**
```bash
GET    /inventory/stock                # Vista general stock
GET    /inventory/stock/{product_id}   # Stock por producto
PUT    /inventory/stock/{product_id}   # Ajustar stock
POST   /inventory/stock/transfer       # Transferir entre sucursales
GET    /inventory/stock/movements      # Historial movimientos
POST   /inventory/stock/adjustment     # Ajuste de inventario
GET    /inventory/stock/report         # Reporte valorizado
GET    /inventory/stock/alerts         # Alertas stock bajo
```

**Total: ~39 rutas Inventory**

---

## 📱 **FRONTEND COMPONENTS**

### **Categorías**
- `CategoryList.tsx` - Lista con árbol de categorías
- `CategoryForm.tsx` - Crear/editar categoría
- `CategoryTree.tsx` - Componente árbol navegable

### **Marcas**
- `BrandList.tsx` - Lista de marcas con logos
- `BrandForm.tsx` - Crear/editar marca
- `BrandCard.tsx` - Tarjeta marca individual

### **Productos**
- `ProductList.tsx` - Lista productos con filtros
- `ProductForm.tsx` - Formulario crear/editar
- `ProductCard.tsx` - Tarjeta producto
- `ProductDetail.tsx` - Vista detallada producto
- `ProductSearch.tsx` - Búsqueda avanzada
- `ProductImport.tsx` - Importar CSV

### **Stock**
- `StockOverview.tsx` - Vista general stock
- `StockMovements.tsx` - Historial movimientos
- `StockAdjustment.tsx` - Ajustar inventario
- `StockTransfer.tsx` - Transferir sucursales
- `StockAlerts.tsx` - Alertas stock bajo

---

## 🎯 **PRIORIDADES DESARROLLO**

### **🥇 ALTA PRIORIDAD (Semana 1)**
1. **Categorías** - Organizar productos
2. **Marcas** - Identificar fabricantes  
3. **Unidades** - Medidas básicas
4. **Productos** - CRUD básico

### **🥈 MEDIA PRIORIDAD (Semana 2)**
1. **Stock Management** - Control inventario
2. **Movimientos** - Historial cambios
3. **Import/Export** - Carga masiva datos
4. **Búsqueda** - Filtros avanzados

### **🥉 BAJA PRIORIDAD (Semana 3)**
1. **Reports** - Reportes stock
2. **Alerts** - Notificaciones automáticas  
3. **Advanced Features** - Funciones extra
4. **Testing** - Pruebas completas

---

## 📞 **SIGUIENTE PASO**

¿Quieres que comience implementando el **Módulo Inventory** completo? 

El orden sería:
1. **Migraciones** → Crear tablas
2. **Modelos** → Eloquent con relaciones  
3. **Controllers** → APIs básicas
4. **Seeders** → Datos de prueba
5. **Frontend** → Componentes React

**¡Listo para empezar el desarrollo del módulo más importante del ERP! 🚀**
