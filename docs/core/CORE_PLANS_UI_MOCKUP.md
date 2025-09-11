# 🎨 MOCKUP UI - COMPANY SETTINGS CON SISTEMA DE PLANES

## 🎯 **DISEÑO PROPUESTO**
Mockup visual del dashboard Company Settings con el sistema de planes integrado.

---

## 🏗️ **LAYOUT PRINCIPAL**

```
┌─────────────────────────────────────────────────────────────────┐
│                    CONFIGURACIÓN EMPRESA                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                   PLAN ACTUAL                           │    │
│  │                                                         │    │
│  │  🏆 PLAN PROFESIONAL             ⭐ Más Popular        │    │
│  │  S/ 59.00/mes                                          │    │
│  │                                                         │    │
│  │  Base incluido: 1 empresa, 1 sucursal                  │    │
│  │                                                         │    │
│  │  👥 Usuarios:     [████████████░░░] 8/15 (53%)         │    │
│  │  📦 Productos:    [████░░░░░░░░░░░░░] 1,250/5,000 (25%)│    │
│  │  🏷️ Rubros:       [████████████░░░░] 5/10 (50%)        │    │
│  │                                                         │    │
│  │  💼 Expansiones Contratadas:                           │    │
│  │  + 2 Empresas adicionales     (S/ 38.00/mes)          │    │
│  │  + 3 Sucursales adicionales   (S/ 27.00/mes)          │    │
│  │  + 1 Pack usuarios (5)        (S/ 15.00/mes)          │    │
│  │                                                         │    │
│  │  💰 Facturación Total: S/ 139.00/mes                   │    │
│  │                                                         │    │
│  │        [🚀 Upgrade Plan]    [➕ Mini-Planes]           │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │  🏢 EMPRESAS    │  │  📊 ANALYTICS   │  │  ⚙️ CONFIGURAR │  │
│  │                 │  │                 │  │                 │  │
│  │  Gestionar      │  │  Ver métricas   │  │  Datos empresa  │  │
│  │  3 empresas     │  │  de rendimiento │  │  y configuración│  │
│  │  totales        │  │                 │  │                 │  │
│  │  [Gestionar]    │  │  [Ver Dashboard]│  │  [Configurar]   │  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │  🏪 SUCURSALES  │  │  👥 USUARIOS    │  │  🎨 BRANDING   │  │
│  │                 │  │                 │  │                 │  │
│  │  Gestionar      │  │  Administrar    │  │  Logo & colores │  │
│  │  5 sucursales   │  │  usuarios y     │  │  corporativos   │  │
│  │  totales        │  │  permisos       │  │                 │  │
│  │  [Gestionar]    │  │  [Administrar]  │  │  [Personalizar] │  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🚨 **ALERTAS DE LÍMITES**

### **Alerta Amarilla (80%+)**
```
┌─────────────────────────────────────────────────────────────────┐
│  ⚠️  Te estás acercando al límite de usuarios                   │
│      Tienes 12 de 15 usuarios (80%) - Considera hacer upgrade  │
│                                        [Ver Planes] [Descartar] │
└─────────────────────────────────────────────────────────────────┘
```

### **Alerta Naranja (90%+)**
```
┌─────────────────────────────────────────────────────────────────┐
│  🟠  Límite de sucursales casi alcanzado                        │
│      Tienes 4 de 5 sucursales (90%) - Haz upgrade ahora        │
│                                        [Upgrade] [Ver Planes]   │
└─────────────────────────────────────────────────────────────────┘
```

### **Alerta Roja (95%+)**
```
┌─────────────────────────────────────────────────────────────────┐
│  🔴  ¡Límite crítico! Solo 250 productos disponibles            │
│      Tienes 4,750 de 5,000 productos (95%) - ¡Actúa ya!        │
│                                               [UPGRADE AHORA]   │
└─────────────────────────────────────────────────────────────────┘
```

### **Límite Alcanzado (100%)**
```
┌─────────────────────────────────────────────────────────────────┐
│  🚫  Límite de rubros alcanzado                                 │
│      No puedes crear más rubros en tu plan actual              │
│      Haz upgrade para obtener hasta 50 rubros                  │
│                                               [UPGRADE PLAN]    │
└─────────────────────────────────────────────────────────────────┘
```

---

## 💳 **MODAL MINI-PLANES**

```
                    ┌─────────────────────────────────────────┐
                    │            CONTRATAR MINI-PLANES        │
                    ├─────────────────────────────────────────┤
                    │                                         │
                    │  Tu Plan: PROFESIONAL (S/ 59/mes)      │
                    │  Base: 1 empresa, 1 sucursal           │
                    │                                         │
┌─────────────────┬─│──────────────────┬──────────────────────┤─────────────────┐
│ 🏢 EMPRESA      │ │  🏪 SUCURSAL     │    👥 USUARIOS       │                 │
│ ADICIONAL       │ │  ADICIONAL       │    PACK +5           │                 │
│                 │ │                  │                      │                 │
│  S/ 19/mes      │ │   S/ 9/mes       │     S/ 15/mes        │                 │
│                 │ │                  │                      │                 │
│ Agrega otra     │ │  Agrega más      │    Agrega 5          │                 │
│ empresa         │ │  sucursales      │    usuarios más      │                 │
│ completa        │ │  a cualquier     │    a tu cuenta       │                 │
│                 │ │  empresa         │                      │                 │
│                 │ │                  │                      │                 │
│ Cantidad: [2] ↕ │ │  Cantidad: [3] ↕ │    Cantidad: [1] ↕   │                 │
│                 │ │                  │                      │                 │
│  S/ 38/mes      │ │   S/ 27/mes      │     S/ 15/mes        │                 │
│                 │ │                  │                      │                 │
│ [Contratar]     │ │  [Contratar]     │    [Contratar]       │                 │
└─────────────────┘ │                  └──────────────────────┘                 │
                    │                                         │
┌─────────────────────────────────────────────────────────────────────────────┐
│                       RESUMEN DE FACTURACIÓN                               │
│                                                                             │
│  Plan PROFESIONAL                                    S/ 59.00/mes          │
│  + 2 Empresas adicionales                           S/ 38.00/mes          │
│  + 3 Sucursales adicionales                         S/ 27.00/mes          │
│  + 1 Pack usuarios (5)                              S/ 15.00/mes          │
│  ─────────────────────────────────────────────────────────────────        │
│  TOTAL MENSUAL:                                      S/ 139.00/mes         │
│                                                                             │
│                    [Cancelar]           [CONFIRMAR COMPRA]                 │
└─────────────────────────────────────────────────────────────────────────────┘
                    │                                         │
                    └─────────────────────────────────────────┘
```

---

## 📱 **RESPONSIVE MOBILE**

### **Widget Plan (Mobile)**
```
┌─────────────────────────────────┐
│         PLAN PROFESIONAL        │
│         S/ 59.00/mes            │
├─────────────────────────────────┤
│                                 │
│ 👥 Usuarios                     │
│ [████████████░░░] 8/15          │
│                                 │
│ 🏪 Sucursales                   │
│ [████████████████░] 3/5         │
│                                 │
│ 📦 Productos                    │
│ [████░░░░░░░░░░░] 1.2K/5K       │
│                                 │
│ 🏷️ Rubros                       │
│ [████████████░░░] 5/10          │
│                                 │
│      [🚀 Upgrade Plan]          │
└─────────────────────────────────┘
```

### **Cards Grid (Mobile)**
```
┌─────────────────────────────────┐
│          📊 ANALYTICS           │
│                                 │
│    Ver métricas y KPIs          │
│                                 │
│         [Ver Dashboard]         │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│         ⚙️ CONFIGURAR           │
│                                 │
│   Datos empresa y config        │
│                                 │
│         [Configurar]            │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│          🎨 BRANDING           │
│                                 │
│    Logo y colores corp.         │
│                                 │
│        [Personalizar]           │
└─────────────────────────────────┘
```

---

## 🎨 **COMPONENTES ESPECÍFICOS**

### **PlanUsageWidget**
```typescript
// Código de ejemplo para el widget
<div className="bg-white rounded-lg shadow-lg p-6 mb-6">
  <div className="flex justify-between items-center mb-4">
    <div>
      <h3 className="text-lg font-semibold text-gray-900">
        🏆 Plan {plan.nombre}
      </h3>
      <p className="text-sm text-gray-600">
        {PlanUtils.formatPrice(plan.precio, plan.moneda)}/mes
      </p>
    </div>
    {plan.popular && (
      <span className="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
        ⭐ Más Popular
      </span>
    )}
  </div>
  
  <div className="space-y-3">
    <UsageBar 
      label="👥 Usuarios"
      current={uso.usuarios_count}
      max={plan.limites.usuarios_max}
      percentage={uso.usuarios_porcentaje}
      alertLevel={PlanUtils.getAlertLevel(uso.usuarios_porcentaje)}
    />
    
    <UsageBar 
      label="🏪 Sucursales"
      current={uso.sucursales_count}
      max={plan.limites.sucursales_max}
      percentage={uso.sucursales_porcentaje}
      alertLevel={PlanUtils.getAlertLevel(uso.sucursales_porcentaje)}
    />
    
    <UsageBar 
      label="📦 Productos"
      current={uso.productos_count}
      max={plan.limites.productos_max}
      percentage={uso.productos_porcentaje}
      alertLevel={PlanUtils.getAlertLevel(uso.productos_porcentaje)}
    />
    
    <UsageBar 
      label="🏷️ Rubros"
      current={uso.rubros_count}
      max={plan.limites.rubros_max}
      percentage={uso.rubros_porcentaje}
      alertLevel={PlanUtils.getAlertLevel(uso.rubros_porcentaje)}
    />
  </div>
  
  {shouldShowUpgrade && (
    <button
      onClick={onUpgradeClick}
      className="w-full mt-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white py-2 px-4 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all"
    >
      🚀 Upgrade Plan
    </button>
  )}
</div>
```

### **UsageBar Component**
```typescript
interface UsageBarProps {
  label: string;
  current: number;
  max: number;
  percentage: number;
  alertLevel: AlertLevel;
}

const UsageBar = ({ label, current, max, percentage, alertLevel }: UsageBarProps) => {
  const colors = ALERT_COLORS[alertLevel];
  
  return (
    <div className="flex items-center justify-between">
      <span className="text-sm font-medium text-gray-700 min-w-0 flex-1">
        {label}
      </span>
      
      <div className="flex items-center space-x-2 min-w-0 flex-1">
        <div className="w-full bg-gray-200 rounded-full h-2">
          <div
            className={`h-2 rounded-full transition-all ${
              alertLevel === 'safe' ? 'bg-green-500' :
              alertLevel === 'warning' ? 'bg-yellow-500' :
              alertLevel === 'danger' ? 'bg-orange-500' :
              'bg-red-500'
            }`}
            style={{ width: `${Math.min(percentage, 100)}%` }}
          />
        </div>
        
        <span className={`text-xs font-medium ${colors.text}`}>
          {current}/{max}
        </span>
      </div>
    </div>
  );
};
```

---

## 🎯 **FLUJO DE USUARIO**

### **1. Usuario ve su plan actual**
- Dashboard muestra widget con plan actual
- Barras de progreso muestran uso de límites
- Colores indican nivel de alerta

### **2. Se acerca a límite**
- Aparece alerta amarilla/naranja/roja
- Botón "Upgrade Plan" se hace más prominente
- Sugerencias automáticas de upgrade

### **3. Inicia proceso de upgrade**
- Modal muestra comparativa de planes
- Destaca plan recomendado según uso
- Opción de trial gratuito

### **4. Confirma upgrade**
- Proceso de pago (futuro)
- Actualización inmediata de límites
- Confirmación y bienvenida al nuevo plan

---

**🎯 Este diseño integra perfectamente el sistema de planes con la UX del Company Settings!**
