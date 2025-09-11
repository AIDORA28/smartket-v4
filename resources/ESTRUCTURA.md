# Estructura de la Base de Datos SmartKet v4

## 📊 Tablas Principales

### 1. Productos
```sql
Tabla: productos
- id: bigint (PK)
- empresa_id: bigint (FK)
- categoria_id: bigint (FK)
- marca_id: bigint (FK)
- unidad_medida_id: bigint (FK)
- nombre: varchar(150)
- codigo_interno: varchar(30)
- codigo_barra: varchar(50)
- descripcion: text
- precio_costo: numeric(10,4)
- precio_venta: numeric(10,4)
- margen_ganancia: numeric(5,2)
- incluye_igv: smallint
- maneja_stock: smallint
- stock_minimo: numeric(10,2)
- stock_maximo: numeric(10,2)
- activo: smallint
- imagen_url: varchar(255)
- extras_json: json
```

### 2. Ventas
```sql
Tabla: ventas
- id: bigint (PK)
- empresa_id: bigint (FK)
- sucursal_id: bigint (FK)
- usuario_id: bigint (FK)
- cliente_id: bigint (FK)
- numero_venta: varchar(50)
- tipo_comprobante: varchar(255)
- estado: varchar(255)
- fecha_venta: timestamp
- subtotal: numeric(10,2)
- descuento_porcentaje: numeric(5,2)
- descuento_monto: numeric(10,2)
- impuesto_porcentaje: numeric(5,2)
- impuesto_monto: numeric(10,2)
- total: numeric(10,2)
- total_pagado: numeric(10,2)
- vuelto: numeric(10,2)
```

## 🔄 Flujos Principales

### POS (Punto de Venta)
1. **Componentes Necesarios:**
   - Búsqueda de productos
   - Carrito de compra
   - Selección de cliente
   - Proceso de pago
   - Impresión de comprobante

2. **Mock Data Inicial:**
```typescript
interface Producto {
  id: number;
  nombre: string;
  codigo: string;
  precio: number;
  stock: number;
}

interface Venta {
  id: number;
  numero: string;
  fecha: Date;
  cliente: string;
  items: VentaItem[];
  total: number;
  estado: 'pendiente' | 'pagada' | 'anulada';
}
```

### Inventario
1. **Componentes Necesarios:**
   - Lista de productos
   - Movimientos de stock
   - Alertas de stock bajo
   - Gestión de categorías
   - Gestión de marcas

### CRM
1. **Componentes Necesarios:**
   - Registro de clientes
   - Historial de compras
   - Estadísticas por cliente
   - Seguimiento de deudas

## 🎨 UI/UX Guidelines

### Colores
```css
/* Primarios */
--color-primary: #4F46E5;  /* Indigo */
--color-success: #059669;  /* Emerald */
--color-warning: #D97706;  /* Amber */
--color-danger: #DC2626;   /* Red */

/* Gradientes */
--gradient-primary: linear-gradient(to right, #4F46E5, #7C3AED);
--gradient-success: linear-gradient(to right, #059669, #047857);
```

### Componentes UI
1. **Botones:**
   - Primary: Acciones principales
   - Secondary: Acciones secundarias
   - Ghost: Navegación y acciones terciarias

2. **Cards:**
   - Shadow y bordes suaves
   - Hover effects sutiles
   - Gradientes para destacar

3. **Tablas:**
   - Responsive
   - Paginación
   - Ordenamiento
   - Filtros
