# 👑 OWNER DASHBOARD - GESTIÓN MULTI-EMPRESA

## 🎯 **OBJETIVO**
Diseñar el dashboard especial para usuarios Owner que gestionen múltiples empresas con mini-planes.

---

## 🏗️ **LAYOUT OWNER DASHBOARD**

```
┌─────────────────────────────────────────────────────────────────┐
│                      DASHBOARD OWNER                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  👑 Bienvenido, Juan Carlos (Owner)                             │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                   MI CUENTA                             │    │
│  │                                                         │    │
│  │  🏆 Plan Principal: PROFESIONAL                        │    │
│  │  💰 Facturación: S/ 139.00/mes                         │    │
│  │                                                         │    │
│  │  📊 Uso Total de la Cuenta:                            │    │
│  │  🏢 Empresas:    [████████████░░░] 3/5                 │    │
│  │  🏪 Sucursales:  [████████░░░░░░░] 5/8                 │    │
│  │  👥 Usuarios:    [████████████████] 18/20              │    │
│  │  📦 Productos:   [████░░░░░░░░░░░░░] 2,150/5,000       │    │
│  │                                                         │    │
│  │           [➕ Agregar Empresa] [🛒 Mini-Planes]         │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                MIS EMPRESAS                             │    │
│  │                                                         │    │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐     │    │
│  │  │ 🏢 TIENDA   │  │ 🏪 MARKET   │  │ 🍕 PIZZA    │     │    │
│  │  │ DON JOSÉ    │  │ CENTRAL     │  │ EXPRESS     │     │    │
│  │  │             │  │             │  │             │     │    │
│  │  │ ✅ Actual   │  │             │  │             │     │    │
│  │  │             │  │             │  │             │     │    │
│  │  │ 👥 8 users  │  │ 👥 6 users  │  │ 👥 4 users  │     │    │
│  │  │ 🏪 2 suc.   │  │ 🏪 2 suc.   │  │ 🏪 1 suc.   │     │    │
│  │  │ 📦 1.2K     │  │ 📦 650      │  │ 📦 300      │     │    │
│  │  │             │  │             │  │             │     │    │
│  │  │[Configurar] │  │[Cambiar a]  │  │[Cambiar a]  │     │    │
│  │  └─────────────┘  └─────────────┘  └─────────────┘     │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │  📊 ANALYTICS   │  │  💳 BILLING     │  │  ⚙️ CONFIG     │  │
│  │  CONSOLIDADO    │  │                 │  │  CUENTA        │  │
│  │                 │  │  Facturación    │  │                 │  │
│  │  Métricas de    │  │  y mini-planes  │  │  Configuración  │  │
│  │  todas empresas │  │                 │  │  global         │  │
│  │  [Ver Todo]     │  │  [Gestionar]    │  │  [Configurar]   │  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🏢 **GESTIÓN DE EMPRESAS**

### **Modal Agregar Empresa**
```
┌─────────────────────────────────────────┐
│           AGREGAR NUEVA EMPRESA         │
├─────────────────────────────────────────┤
│                                         │
│  💼 ¿Cómo quieres agregar la empresa?   │
│                                         │
│  ┌─────────────────────────────────┐     │
│  │  🆕 CREAR NUEVA EMPRESA         │     │
│  │                                 │     │
│  │  • Crear empresa desde cero     │     │
│  │  • Incluye 1 sucursal base      │     │
│  │  • S/ 19.00/mes adicional       │     │
│  │                                 │     │
│  │  Nombre: [________________]      │     │
│  │  RUC:    [________________]      │     │
│  │  Rubro:  [Retail ▼]             │     │
│  │                                 │     │
│  │           [Crear Empresa]        │     │
│  └─────────────────────────────────┘     │
│                                         │
│  ┌─────────────────────────────────┐     │
│  │  📋 IMPORTAR DATOS EXISTENTES   │     │
│  │                                 │     │
│  │  • Migrar de otro sistema       │     │
│  │  • Incluye asistente setup      │     │
│  │  • S/ 19.00/mes + setup fee     │     │
│  │                                 │     │
│  │       [Iniciar Importación]      │     │
│  └─────────────────────────────────┘     │
│                                         │
│          [Cancelar] [Cerrar]            │
└─────────────────────────────────────────┘
```

### **Tarjeta Empresa (Detallada)**
```
┌─────────────────────────────────────────┐
│  🏢 TIENDA DON JOSÉ                     │
│  ✅ Empresa Actual                      │
├─────────────────────────────────────────┤
│                                         │
│  📋 Información:                        │
│  • RUC: 20123456789                     │
│  • Rubro: Retail - Abarrotes            │
│  • Plan: Incluido en PROFESIONAL        │
│                                         │
│  📊 Uso Actual:                         │
│  👥 Usuarios:   [████████░░] 8/15       │
│  🏪 Sucursales: [████████░░] 2/5        │ 
│  📦 Productos:  [████░░░░░░] 1,200/5K   │
│  🏷️ Rubros:     [████░░░░░░] 2/10       │
│                                         │
│  🎯 Acciones Rápidas:                   │
│  [➕ Sucursal] [👥 Usuarios] [📊 Analytics] │
│                                         │
│  [🔄 Cambiar a esta] [⚙️ Configurar]    │
└─────────────────────────────────────────┘
```

---

## 💳 **BILLING & MINI-PLANES**

### **Dashboard Facturación**
```
┌─────────────────────────────────────────────────────────────────┐
│                      BILLING DASHBOARD                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  💰 Facturación Actual: S/ 139.00/mes                          │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                DESGLOSE MENSUAL                         │    │
│  │                                                         │    │
│  │  🏆 Plan PROFESIONAL (base)            S/ 59.00        │    │
│  │                                                         │    │
│  │  📦 Mini-Planes Contratados:                           │    │
│  │  ├─ 2x Empresa adicional               S/ 38.00        │    │
│  │  ├─ 3x Sucursal adicional              S/ 27.00        │    │
│  │  └─ 1x Pack usuarios (+5)              S/ 15.00        │    │
│  │                                                         │    │
│  │  ─────────────────────────────────────────────────      │    │
│  │  💵 TOTAL MENSUAL:                     S/ 139.00       │    │
│  │                                                         │    │
│  │  📅 Próximo cobro: 15 de Octubre       💳 Visa ***1234 │    │
│  │                                                         │    │
│  │         [🛒 Gestionar Mini-Planes] [💳 Pago]           │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │               HISTORIAL DE FACTURAS                     │    │
│  │                                                         │    │
│  │  📄 Sep 2025  S/ 139.00  [Pagada]     [Ver PDF]        │    │
│  │  📄 Ago 2025  S/ 120.00  [Pagada]     [Ver PDF]        │    │
│  │  📄 Jul 2025  S/ 59.00   [Pagada]     [Ver PDF]        │    │
│  │                                                         │    │
│  │                              [Ver Todo el Historial]   │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### **Gestión Mini-Planes**
```
┌─────────────────────────────────────────────────────────────────┐
│                   GESTIONAR MINI-PLANES                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  🛒 Mini-Planes Contratados:                                   │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │  🏢 EMPRESAS ADICIONALES                               │    │
│  │                                                         │    │
│  │  📊 Contratado: 2 empresas                             │    │
│  │  💰 Costo: S/ 38.00/mes                                │    │
│  │                                                         │    │
│  │  🏢 Market Central     [Configurar] [❌ Eliminar]      │    │
│  │  🏢 Pizza Express      [Configurar] [❌ Eliminar]      │    │
│  │                                                         │    │
│  │                               [➕ Agregar Empresa]     │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │  🏪 SUCURSALES ADICIONALES                             │    │
│  │                                                         │    │
│  │  📊 Contratado: 3 sucursales                           │    │
│  │  💰 Costo: S/ 27.00/mes                                │    │
│  │                                                         │    │
│  │  🏪 Don José - Norte    [Ver] [❌]                      │    │
│  │  🏪 Market - Sur        [Ver] [❌]                      │    │
│  │  🏪 Pizza - Express 2   [Ver] [❌]                      │    │
│  │                                                         │    │
│  │                              [➕ Agregar Sucursal]     │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │  👥 PACKS DE USUARIOS                                   │    │
│  │                                                         │    │
│  │  📊 Contratado: 1 pack (+5 usuarios)                   │    │
│  │  💰 Costo: S/ 15.00/mes                                │    │
│  │  ✅ Usuarios disponibles: 20 total                     │    │
│  │                                                         │    │
│  │                         [➕ Agregar Pack] [➖ Quitar]   │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│                                   [Guardar] [Cancelar]          │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📊 **ANALYTICS CONSOLIDADO**

### **Dashboard Multi-Empresa**
```
┌─────────────────────────────────────────────────────────────────┐
│                   ANALYTICS CONSOLIDADO                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  📊 Resumen de Todas las Empresas                              │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │  💰 VENTAS TOT. │  │  👥 USUARIOS    │  │  🏪 SUCURSALES  │  │
│  │                 │  │                 │  │                 │  │
│  │   S/ 125,450    │  │       18        │  │        5        │  │
│  │   ↗️ +12.5%     │  │   ↗️ +3 nuevos  │  │   ↗️ +1 nueva   │  │
│  │                 │  │                 │  │                 │  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │              RANKING POR EMPRESA                        │    │
│  │                                                         │    │
│  │  🥇 Tienda Don José      S/ 85,200  (68%)              │    │
│  │  🥈 Market Central       S/ 28,150  (23%)              │    │
│  │  🥉 Pizza Express        S/ 12,100  (9%)               │    │
│  │                                                         │    │
│  │                                [Ver Detalle Completo]  │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                GRÁFICO VENTAS (6 MESES)                │    │
│  │                                                         │    │
│  │     125K ┤                                      ●       │    │
│  │          │                               ●    ●         │    │
│  │     100K ┤                        ●   ●               │    │
│  │          │                  ●   ●                     │    │
│  │      75K ┤           ●   ●                           │    │
│  │          │     ●   ●                                 │    │
│  │      50K ┤  ●                                        │    │
│  │          └─────────────────────────────────────────   │    │
│  │           Abr  May  Jun  Jul  Ago  Sep              │    │
│  │                                                         │    │
│  │  📈 Tendencia: +15% crecimiento mensual                │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔧 **COMPONENTES TÉCNICOS**

### **OwnerMultiEmpresaSelector.tsx**
```typescript
interface OwnerMultiEmpresaSelectorProps {
  empresas: Empresa[];
  empresa_actual: Empresa;
  onSwitchEmpresa: (empresaId: number) => void;
  onAddEmpresa: () => void;
}

// Features:
// - Dropdown con todas las empresas
// - Indicador empresa actual
// - Búsqueda rápida por nombre
// - Botón "Agregar empresa"
// - Switch rápido entre contextos
```

### **MiniPlanManager.tsx**
```typescript
interface MiniPlanManagerProps {
  mini_planes_contratados: MiniPlanContratado[];
  mini_planes_disponibles: MiniPlan[];
  facturacion_actual: number;
  onContratarMiniPlan: (tipo: string, cantidad: number) => Promise<void>;
  onCancelarMiniPlan: (id: number) => Promise<void>;
}

// Features:
// - Lista mini-planes contratados
// - Calculadora costos en tiempo real
// - Validación límites antes de contratar
// - Confirmación antes de cancelar
```

### **FacturacionWidget.tsx**
```typescript
interface FacturacionWidgetProps {
  plan_principal: PlanSuscripcion;
  mini_planes_cost: number;
  total_mensual: number;
  proxima_fecha_cobro: string;
  metodo_pago: MetodoPago;
  historial_facturas: Factura[];
}

// Features:
// - Desglose completo de costos
// - Próximo cobro y método pago
// - Historial facturas con PDFs
// - Alertas de pagos pendientes
```

---

**🎯 Este diseño permite al Owner gestionar múltiples empresas de manera eficiente con el sistema de mini-planes!**
