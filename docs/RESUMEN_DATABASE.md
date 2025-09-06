# 📊 BASE DE DATOS - Esquema y Optimizaciones

## 🗄️ ESQUEMA PRINCIPAL

### **Arquitectura Multi-tenant:**
```sql
-- TABLA PRINCIPAL: Empresas (Tenants)
CREATE TABLE empresas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    ruc VARCHAR(11) UNIQUE,
    direccion TEXT,
    telefono VARCHAR(15),
    email VARCHAR(255),
    plan_activo VARCHAR(50) DEFAULT 'basico',
    fecha_registro TIMESTAMP DEFAULT NOW(),
    activo BOOLEAN DEFAULT true
);

-- USUARIOS Multi-empresa
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER REFERENCES empresas(id),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cajero', 'vendedor', 'supervisor') DEFAULT 'cajero',
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW()
);
```

### **Módulo Productos:**
```sql
-- CATEGORÍAS
CREATE TABLE categorias (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER REFERENCES empresas(id),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW()
);

-- PRODUCTOS
CREATE TABLE productos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER REFERENCES empresas(id),
    categoria_id INTEGER REFERENCES categorias(id),
    codigo_barras VARCHAR(50),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio_venta DECIMAL(10,2) NOT NULL,
    precio_costo DECIMAL(10,2),
    stock_actual INTEGER DEFAULT 0,
    stock_minimo INTEGER DEFAULT 1,
    imagen VARCHAR(500),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

### **Módulo Ventas (POS):**
```sql
-- VENTAS (Cabecera)
CREATE TABLE ventas (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER REFERENCES empresas(id),
    user_id INTEGER REFERENCES users(id),
    cliente_id INTEGER REFERENCES clientes(id) NULL,
    fecha_venta TIMESTAMP DEFAULT NOW(),
    subtotal DECIMAL(10,2) NOT NULL,
    igv DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') DEFAULT 'efectivo',
    estado ENUM('completada', 'anulada') DEFAULT 'completada',
    observaciones TEXT
);

-- DETALLE VENTAS (Items)
CREATE TABLE detalle_ventas (
    id SERIAL PRIMARY KEY,
    venta_id INTEGER REFERENCES ventas(id) ON DELETE CASCADE,
    producto_id INTEGER REFERENCES productos(id),
    cantidad INTEGER NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL
);
```

### **Módulo Inventario:**
```sql
-- MOVIMIENTOS DE STOCK
CREATE TABLE stock_movimientos (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER REFERENCES empresas(id),
    producto_id INTEGER REFERENCES productos(id),
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    cantidad INTEGER NOT NULL,
    stock_anterior INTEGER NOT NULL,
    stock_nuevo INTEGER NOT NULL,
    motivo VARCHAR(255),
    user_id INTEGER REFERENCES users(id),
    fecha_movimiento TIMESTAMP DEFAULT NOW()
);
```

### **Módulo Clientes (CRM):**
```sql
-- CLIENTES
CREATE TABLE clientes (
    id SERIAL PRIMARY KEY,
    empresa_id INTEGER REFERENCES empresas(id),
    tipo_documento ENUM('dni', 'ruc', 'pasaporte') DEFAULT 'dni',
    numero_documento VARCHAR(20),
    nombre VARCHAR(255) NOT NULL,
    apellido VARCHAR(255),
    email VARCHAR(255),
    telefono VARCHAR(15),
    direccion TEXT,
    fecha_nacimiento DATE,
    puntos_acumulados INTEGER DEFAULT 0,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW()
);
```

## 🔒 ROW LEVEL SECURITY (Multi-tenant)

### **Políticas de Seguridad:**
```sql
-- Habilitar RLS en todas las tablas
ALTER TABLE productos ENABLE ROW LEVEL SECURITY;
ALTER TABLE ventas ENABLE ROW LEVEL SECURITY;
ALTER TABLE clientes ENABLE ROW LEVEL SECURITY;
ALTER TABLE categorias ENABLE ROW LEVEL SECURITY;

-- Políticas automáticas por empresa
CREATE POLICY tenant_productos ON productos
FOR ALL USING (empresa_id = current_setting('app.current_tenant')::int);

CREATE POLICY tenant_ventas ON ventas
FOR ALL USING (empresa_id = current_setting('app.current_tenant')::int);

CREATE POLICY tenant_clientes ON clientes
FOR ALL USING (empresa_id = current_setting('app.current_tenant')::int);

-- Función para establecer tenant actual
CREATE OR REPLACE FUNCTION set_current_tenant(tenant_id int)
RETURNS void AS $$
BEGIN
    PERFORM set_config('app.current_tenant', tenant_id::text, true);
END;
$$ LANGUAGE plpgsql;
```

## ⚡ ÍNDICES DE PERFORMANCE

### **Índices Principales:**
```sql
-- Productos: Búsquedas frecuentes
CREATE INDEX idx_productos_empresa ON productos(empresa_id);
CREATE INDEX idx_productos_categoria ON productos(categoria_id);
CREATE INDEX idx_productos_codigo ON productos(codigo_barras);
CREATE INDEX idx_productos_nombre ON productos USING gin(to_tsvector('spanish', nombre));

-- Ventas: Reportes y consultas
CREATE INDEX idx_ventas_empresa_fecha ON ventas(empresa_id, fecha_venta DESC);
CREATE INDEX idx_ventas_usuario ON ventas(user_id);
CREATE INDEX idx_ventas_cliente ON ventas(cliente_id);

-- Detalle ventas: Join frecuente
CREATE INDEX idx_detalle_venta ON detalle_ventas(venta_id);
CREATE INDEX idx_detalle_producto ON detalle_ventas(producto_id);

-- Stock: Trazabilidad
CREATE INDEX idx_stock_producto ON stock_movimientos(producto_id);
CREATE INDEX idx_stock_fecha ON stock_movimientos(fecha_movimiento DESC);

-- Clientes: CRM
CREATE INDEX idx_clientes_empresa ON clientes(empresa_id);
CREATE INDEX idx_clientes_documento ON clientes(numero_documento);
```

### **Índices Compuestos Optimizados:**
```sql
-- Para dashboard de ventas diarias
CREATE INDEX idx_ventas_empresa_fecha_estado 
ON ventas(empresa_id, DATE(fecha_venta), estado) 
WHERE estado = 'completada';

-- Para productos activos por categoría
CREATE INDEX idx_productos_empresa_categoria_activo 
ON productos(empresa_id, categoria_id, activo) 
WHERE activo = true;

-- Para stock bajo (alertas)
CREATE INDEX idx_productos_stock_bajo 
ON productos(empresa_id, stock_actual) 
WHERE stock_actual <= stock_minimo AND activo = true;
```

## 📊 VISTAS MATERIALIZADAS (Reportes)

### **Analytics Pre-calculados:**
```sql
-- Reporte ventas mensual
CREATE MATERIALIZED VIEW reporte_ventas_mensual AS
SELECT 
    empresa_id,
    DATE_TRUNC('month', fecha_venta) as mes,
    COUNT(*) as total_transacciones,
    SUM(total) as total_ventas,
    AVG(total) as venta_promedio
FROM ventas 
WHERE estado = 'completada'
GROUP BY empresa_id, DATE_TRUNC('month', fecha_venta);

-- Top productos más vendidos
CREATE MATERIALIZED VIEW top_productos_vendidos AS
SELECT 
    p.empresa_id,
    p.id as producto_id,
    p.nombre,
    SUM(dv.cantidad) as total_vendido,
    SUM(dv.subtotal) as total_ingresos
FROM productos p
JOIN detalle_ventas dv ON p.id = dv.producto_id
JOIN ventas v ON dv.venta_id = v.id
WHERE v.estado = 'completada'
  AND v.fecha_venta >= DATE_TRUNC('month', CURRENT_DATE)
GROUP BY p.empresa_id, p.id, p.nombre
ORDER BY total_vendido DESC;

-- Stock crítico por empresa
CREATE MATERIALIZED VIEW stock_critico AS
SELECT 
    empresa_id,
    id as producto_id,
    nombre,
    stock_actual,
    stock_minimo,
    (stock_actual::float / stock_minimo::float) as ratio_stock
FROM productos
WHERE stock_actual <= stock_minimo 
  AND activo = true
ORDER BY ratio_stock ASC;

-- Refresh automático (cron job)
-- */15 * * * * psql -c "REFRESH MATERIALIZED VIEW reporte_ventas_mensual;"
```

## 🔄 TRIGGERS Y PROCEDIMIENTOS

### **Actualización Automática de Stock:**
```sql
-- Función para actualizar stock en ventas
CREATE OR REPLACE FUNCTION actualizar_stock_venta()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        -- Reducir stock al vender
        UPDATE productos 
        SET stock_actual = stock_actual - NEW.cantidad
        WHERE id = NEW.producto_id;
        
        -- Registrar movimiento
        INSERT INTO stock_movimientos (
            empresa_id, producto_id, tipo_movimiento, 
            cantidad, stock_anterior, stock_nuevo, motivo
        )
        SELECT 
            p.empresa_id, p.id, 'salida',
            NEW.cantidad, 
            p.stock_actual + NEW.cantidad,
            p.stock_actual,
            'Venta #' || NEW.venta_id
        FROM productos p WHERE p.id = NEW.producto_id;
        
        RETURN NEW;
    END IF;
    
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- Trigger en detalle_ventas
CREATE TRIGGER trigger_actualizar_stock
    AFTER INSERT ON detalle_ventas
    FOR EACH ROW
    EXECUTE FUNCTION actualizar_stock_venta();
```

### **Alertas de Stock Bajo:**
```sql
-- Función de alerta stock bajo
CREATE OR REPLACE FUNCTION check_stock_bajo()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.stock_actual <= NEW.stock_minimo THEN
        INSERT INTO notificaciones (
            empresa_id, tipo, titulo, mensaje, fecha_creacion
        ) VALUES (
            NEW.empresa_id,
            'stock_bajo',
            'Stock Crítico',
            'El producto ' || NEW.nombre || ' tiene stock bajo: ' || NEW.stock_actual,
            NOW()
        );
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger en productos
CREATE TRIGGER trigger_stock_bajo
    AFTER UPDATE OF stock_actual ON productos
    FOR EACH ROW
    EXECUTE FUNCTION check_stock_bajo();
```

## 📈 OPTIMIZACIONES AVANZADAS

### **Particionamiento (Para empresas grandes):**
```sql
-- Particionamiento por fecha en ventas
CREATE TABLE ventas_2025 PARTITION OF ventas
FOR VALUES FROM ('2025-01-01') TO ('2026-01-01');

CREATE TABLE ventas_2024 PARTITION OF ventas
FOR VALUES FROM ('2024-01-01') TO ('2025-01-01');

-- Índices automáticos por partición
CREATE INDEX idx_ventas_2025_empresa_fecha 
ON ventas_2025(empresa_id, fecha_venta DESC);
```

### **Estadísticas Automáticas:**
```sql
-- Auto-analyze para mejores query plans
ALTER TABLE productos SET (autovacuum_analyze_scale_factor = 0.02);
ALTER TABLE ventas SET (autovacuum_analyze_scale_factor = 0.02);

-- Estadísticas extendidas para queries complejas
CREATE STATISTICS stat_ventas_empresa_fecha 
ON empresa_id, fecha_venta FROM ventas;
```

## 💾 BACKUP Y RECOVERY

### **Estrategia de Backup:**
```bash
# Backup diario automático (Supabase)
# Point-in-time recovery hasta 30 días
# Cross-region replication automática

# Backup manual para migraciones
pg_dump --host=db.mklfolbageroutbquwqx.supabase.co \
        --username=postgres \
        --dbname=postgres \
        --format=custom \
        --compress=9 \
        --file=smartket_backup_$(date +%Y%m%d).dump
```

---

**Base de datos optimizada** para **performance**, **seguridad** y **escalabilidad** empresarial con PostgreSQL + Supabase.
