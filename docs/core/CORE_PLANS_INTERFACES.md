# 💳 INTERFACES TYPESCRIPT - SISTEMA DE PLANES CORE

## 🎯 **OBJETIVO**
Definir todas las interfaces TypeScript necesarias para implementar el sistema de planes y límites en el frontend Core.

---

## 📋 **INTERFACES PRINCIPALES**

### **1. Plan de Suscripción**
```typescript
interface PlanSuscripcion {
  id: number;
  nombre: 'FREE' | 'BÁSICO' | 'PROFESIONAL' | 'EMPRESARIAL';
  precio: number;
  moneda: 'PEN' | 'USD';
  periodo: 'lifetime' | 'monthly' | 'yearly';
  trial_dias: number;
  popular?: boolean;
  activo: boolean;
  
  // Límites del plan (todos los planes: 1 empresa, 1 sucursal)
  limites: PlanLimites;
  
  // Features incluidas
  features: PlanFeature[];
  
  // Metadata
  descripcion?: string;
  color_hex?: string;
  orden_display: number;
  created_at: string;
  updated_at: string;
}
```

### **2. Límites por Plan (Base: 1 empresa, 1 sucursal)**
```typescript
interface PlanLimites {
  // Límites base (todos los planes)
  empresas_incluidas: 1;  // FIJO: 1 empresa por plan
  sucursales_incluidas: 1; // FIJO: 1 sucursal por plan
  
  // Límites variables por plan
  usuarios_max: number;
  productos_max: number;
  rubros_max: number;
  
  // Límites adicionales (futuro)
  clientes_max?: number;
  proveedores_max?: number;
  storage_mb?: number;
  api_calls_mes?: number;
}
```

### **3. Mini-Planes Adicionales**
```typescript
interface MiniPlan {
  id: number;
  tipo: 'empresa_adicional' | 'sucursal_adicional' | 'usuarios_adicionales' | 'productos_adicionales' | 'rubros_adicionales';
  nombre: string;
  descripcion: string;
  precio_mensual: number;
  unidades_incluidas: number;
  
  // Para qué plan está disponible
  planes_compatibles: number[]; // IDs de planes principales
  
  // Límites
  max_cantidad?: number; // Máximo que se puede contratar
  
  activo: boolean;
  created_at: string;
  updated_at: string;
}

// Ejemplos de mini-planes
interface EjemplosMiniPlanes {
  empresa_adicional: {
    nombre: "Empresa Adicional";
    descripcion: "Agrega otra empresa a tu cuenta";
    precio_mensual: 19.00;
    unidades_incluidas: 1;
    max_cantidad: 10;
  };
  
  sucursal_adicional: {
    nombre: "Sucursal Adicional";
    descripcion: "Agrega sucursales a tus empresas";
    precio_mensual: 9.00;
    unidades_incluidas: 1;
    max_cantidad: 20;
  };
  
  usuarios_pack: {
    nombre: "Pack 5 Usuarios";
    descripcion: "Agrega 5 usuarios adicionales";
    precio_mensual: 15.00;
    unidades_incluidas: 5;
    max_cantidad: 50;
  };
  
  productos_pack: {
    nombre: "Pack 1000 Productos";
    descripcion: "Aumenta límite en 1000 productos";
    precio_mensual: 8.00;
    unidades_incluidas: 1000;
    max_cantidad: 100;
  };
  
  rubros_pack: {
    nombre: "Pack 5 Rubros";
    descripcion: "Agrega 5 rubros adicionales";
    precio_mensual: 5.00;
    unidades_incluidas: 5;
    max_cantidad: 20;
  };
}
```

### **3. Features por Plan**
```typescript
type PlanFeature = 
  | 'reportes_basicos'
  | 'reportes_avanzados'
  | 'todos_reportes'
  | 'control_inventario'
  | 'control_inventario_avanzado'
  | 'gestion_clientes'
  | 'gestion_proveedores'
  | 'integraciones_basicas'
  | 'integraciones_avanzadas'
  | 'api_consulta'
  | 'api_completa'
  | 'dashboard_ejecutivo'
  | 'analisis_predictivo'
  | 'soporte_email'
  | 'soporte_chat'
  | 'soporte_prioritario'
  | 'soporte_dedicado'
  | 'capacitacion_incluida'
  | 'todas_funcionalidades';

interface PlanFeatureDetail {
  feature: PlanFeature;
  incluido: boolean;
  descripcion?: string;
}
```

### **4. Uso Actual de la Empresa (Incluyendo Mini-Planes)**
```typescript
interface EmpresaUsoActual {
  // Uso base del plan principal
  usuarios_count: number;
  productos_count: number;
  rubros_count: number;
  
  // Uso multi-tenant (con mini-planes)
  empresas_count: number;  // Total empresas en la cuenta
  sucursales_count: number; // Total sucursales en todas las empresas
  
  // Límites totales (plan + mini-planes)
  usuarios_limite_total: number;
  productos_limite_total: number;
  rubros_limite_total: number;
  empresas_limite_total: number;
  sucursales_limite_total: number;
  
  // Porcentajes calculados
  usuarios_porcentaje: number;
  productos_porcentaje: number;
  rubros_porcentaje: number;
  empresas_porcentaje: number;
  sucursales_porcentaje: number;
  
  // Status de límites
  usuarios_limite_alcanzado: boolean;
  productos_limite_alcanzado: boolean;
  rubros_limite_alcanzado: boolean;
  empresas_limite_alcanzado: boolean;
  sucursales_limite_alcanzado: boolean;
  
  // Nivel de alerta (0-100)
  usuarios_nivel_alerta: 'safe' | 'warning' | 'danger' | 'critical';
  productos_nivel_alerta: 'safe' | 'warning' | 'danger' | 'critical';
  rubros_nivel_alerta: 'safe' | 'warning' | 'danger' | 'critical';
  empresas_nivel_alerta: 'safe' | 'warning' | 'danger' | 'critical';
  sucursales_nivel_alerta: 'safe' | 'warning' | 'danger' | 'critical';
}

### **5. Mini-Planes Contratados**
```typescript
interface MiniPlanContratado {
  id: number;
  usuario_id: number;
  mini_plan_id: number;
  mini_plan: MiniPlan;
  cantidad_contratada: number;
  precio_total_mensual: number;
  fecha_inicio: string;
  fecha_fin?: string;
  activo: boolean;
  
  // Calculated fields
  unidades_totales: number; // cantidad_contratada * mini_plan.unidades_incluidas
}

interface ResumenMiniPlanes {
  empresas_adicionales: number;
  sucursales_adicionales: number;
  usuarios_adicionales: number;
  productos_adicionales: number;
  rubros_adicionales: number;
  costo_total_mensual: number;
}
```

### **6. Response Company Settings (Actualizado)**
```typescript
interface CompanySettingsIndexProps {
  empresa: Empresa;
  configuracion: EmpresaSettings;
  
  // Plan principal (1 empresa, 1 sucursal base)
  plan_actual: PlanSuscripcion;
  planes_disponibles: PlanSuscripcion[];
  
  // Mini-planes
  mini_planes_disponibles: MiniPlan[];
  mini_planes_contratados: MiniPlanContratado[];
  resumen_mini_planes: ResumenMiniPlanes;
  
  // Uso actual (plan + mini-planes)
  uso_actual: EmpresaUsoActual;
  
  // Para Owner: gestión de todas las empresas
  todas_las_empresas?: Empresa[]; // Solo para Owner
  empresa_actual_context: Empresa; // Empresa en contexto actual
  
  // Para comparativa de planes
  plan_comparativa: PlanComparativa[];
  
  // Settings adicionales
  configuracion_fiscal: ConfiguracionFiscal;
  configuracion_regional: ConfiguracionRegional;
}
```

### **7. Props para Owner Dashboard**
```typescript
interface OwnerDashboardProps {
  // Cuenta principal del owner
  plan_principal: PlanSuscripcion;
  
  // Todas las empresas del owner
  empresas: Empresa[];
  empresa_actual: Empresa;
  
  // Mini-planes
  mini_planes_disponibles: MiniPlan[];
  mini_planes_contratados: MiniPlanContratado[];
  resumen_mini_planes: ResumenMiniPlanes;
  
  // Uso total de la cuenta
  uso_total_cuenta: {
    empresas_count: number;
    sucursales_count: number;
    usuarios_count: number;
    productos_count: number;
    rubros_count: number;
  };
  
  // Límites totales
  limites_totales: {
    empresas_max: number;
    sucursales_max: number;
    usuarios_max: number;
    productos_max: number;
    rubros_max: number;
  };
  
  // Facturación
  facturacion_mensual: {
    plan_principal: number;
    mini_planes_total: number;
    total: number;
  };
}
```

### **6. Comparativa de Planes**
```typescript
interface PlanComparativa {
  plan: PlanSuscripcion;
  diferencias: {
    usuarios_adicionales: number;
    sucursales_adicionales: number;
    productos_adicionales: number;
    rubros_adicionales: number;
    features_nuevas: PlanFeature[];
    ahorro_anual?: number;
  };
  recomendado: boolean;
  razon_recomendacion?: string;
}
```

---

## 🎨 **PROPS PARA COMPONENTES UI**

### **PlanUsageWidget.tsx**
```typescript
interface PlanUsageWidgetProps {
  plan_actual: PlanSuscripcion;
  uso_actual: EmpresaUsoActual;
  onUpgradeClick: () => void;
  showUpgradeButton?: boolean;
  compact?: boolean;
}
```

### **UpgradeModal.tsx**
```typescript
interface UpgradeModalProps {
  isOpen: boolean;
  onClose: () => void;
  plan_actual: PlanSuscripcion;
  planes_disponibles: PlanSuscripcion[];
  plan_comparativa: PlanComparativa[];
  onSelectPlan: (planId: number) => Promise<void>;
  loading?: boolean;
}
```

### **LimitAlert.tsx**
```typescript
interface LimitAlertProps {
  tipo: keyof PlanLimites;
  uso_actual: number;
  limite_max: number;
  nivel_alerta: 'safe' | 'warning' | 'danger' | 'critical';
  onUpgradeClick?: () => void;
  showUpgradeButton?: boolean;
  className?: string;
}
```

### **PlanCard.tsx**
```typescript
interface PlanCardProps {
  plan: PlanSuscripcion;
  comparativa?: PlanComparativa;
  is_current?: boolean;
  is_recommended?: boolean;
  onSelect: (planId: number) => void;
  loading?: boolean;
  trial_available?: boolean;
}
```

### **BranchForm.tsx (con límites)**
```typescript
interface BranchFormProps {
  sucursal?: Sucursal;
  empresa: Empresa;
  plan_actual: PlanSuscripcion;
  uso_actual: EmpresaUsoActual;
  onSubmit: (data: SucursalFormData) => Promise<void>;
  onCancel: () => void;
  loading?: boolean;
}
```

### **BranchLimitCounter.tsx**
```typescript
interface BranchLimitCounterProps {
  sucursales_count: number;
  sucursales_max: number;
  nivel_alerta: 'safe' | 'warning' | 'danger' | 'critical';
  onUpgradeClick?: () => void;
  className?: string;
}
```

---

## 🔧 **ENUMS Y CONSTANTES**

### **Plan Names**
```typescript
export const PLAN_NAMES = {
  FREE: 'FREE',
  BASICO: 'BÁSICO', 
  PROFESIONAL: 'PROFESIONAL',
  EMPRESARIAL: 'EMPRESARIAL'
} as const;

export type PlanName = keyof typeof PLAN_NAMES;
```

### **Alert Levels**
```typescript
export const ALERT_LEVELS = {
  SAFE: 'safe',
  WARNING: 'warning', 
  DANGER: 'danger',
  CRITICAL: 'critical'
} as const;

export type AlertLevel = typeof ALERT_LEVELS[keyof typeof ALERT_LEVELS];

// Thresholds
export const ALERT_THRESHOLDS = {
  WARNING: 70,  // 70%
  DANGER: 85,   // 85% 
  CRITICAL: 95  // 95%
} as const;
```

### **Feature Categories**
```typescript
export const FEATURE_CATEGORIES = {
  REPORTES: 'Reportes',
  INVENTARIO: 'Inventario',
  GESTION: 'Gestión',
  INTEGRACIONES: 'Integraciones',
  SOPORTE: 'Soporte',
  ANALYTICS: 'Analytics'
} as const;

export const FEATURE_DESCRIPTIONS: Record<PlanFeature, string> = {
  reportes_basicos: 'Reportes de ventas básicos',
  reportes_avanzados: 'Reportes detallados con filtros',
  todos_reportes: 'Acceso a todos los reportes disponibles',
  control_inventario: 'Control básico de stock',
  control_inventario_avanzado: 'Control avanzado con alertas automáticas',
  gestion_clientes: 'Base de datos de clientes',
  gestion_proveedores: 'Gestión completa de proveedores',
  integraciones_basicas: 'Conectores básicos con terceros',
  integraciones_avanzadas: 'API completa para integraciones',
  api_consulta: 'API de solo lectura',
  api_completa: 'API completa con escritura',
  dashboard_ejecutivo: 'Dashboard con KPIs ejecutivos',
  analisis_predictivo: 'IA para predicción de ventas',
  soporte_email: 'Soporte vía email',
  soporte_chat: 'Chat en tiempo real',
  soporte_prioritario: 'Soporte con prioridad',
  soporte_dedicado: 'Account manager dedicado',
  capacitacion_incluida: 'Capacitación y onboarding',
  todas_funcionalidades: 'Acceso completo a todas las features'
};
```

---

## 🎯 **UTILITIES Y HELPERS**

### **Plan Utils**
```typescript
export class PlanUtils {
  static isFeatureIncluded(plan: PlanSuscripcion, feature: PlanFeature): boolean {
    return plan.features.includes(feature);
  }
  
  static getAlertLevel(porcentaje: number): AlertLevel {
    if (porcentaje >= ALERT_THRESHOLDS.CRITICAL) return ALERT_LEVELS.CRITICAL;
    if (porcentaje >= ALERT_THRESHOLDS.DANGER) return ALERT_LEVELS.DANGER;
    if (porcentaje >= ALERT_THRESHOLDS.WARNING) return ALERT_LEVELS.WARNING;
    return ALERT_LEVELS.SAFE;
  }
  
  static canCreateMore(tipo: keyof PlanLimites, uso: EmpresaUsoActual, plan: PlanSuscripcion): boolean {
    switch (tipo) {
      case 'usuarios_max':
        return uso.usuarios_count < plan.limites.usuarios_max;
      case 'sucursales_max':
        return uso.sucursales_count < plan.limites.sucursales_max;
      case 'productos_max':
        return uso.productos_count < plan.limites.productos_max;
      case 'rubros_max':
        return uso.rubros_count < plan.limites.rubros_max;
      default:
        return false;
    }
  }
  
  static getUpgradeRecommendation(uso: EmpresaUsoActual, planes: PlanSuscripcion[]): PlanSuscripcion | null {
    // Lógica para recomendar upgrade basado en uso actual
    return null;
  }
  
  static formatPrice(precio: number, moneda: string): string {
    return moneda === 'PEN' ? `S/ ${precio.toFixed(2)}` : `$${precio.toFixed(2)}`;
  }
}
```

### **Alert Colors**
```typescript
export const ALERT_COLORS = {
  safe: {
    bg: 'bg-green-50',
    border: 'border-green-200',
    text: 'text-green-800',
    icon: 'text-green-500'
  },
  warning: {
    bg: 'bg-yellow-50',
    border: 'border-yellow-200', 
    text: 'text-yellow-800',
    icon: 'text-yellow-500'
  },
  danger: {
    bg: 'bg-orange-50',
    border: 'border-orange-200',
    text: 'text-orange-800', 
    icon: 'text-orange-500'
  },
  critical: {
    bg: 'bg-red-50',
    border: 'border-red-200',
    text: 'text-red-800',
    icon: 'text-red-500'
  }
} as const;
```

---

## 📋 **VALIDATION SCHEMAS**

### **Plan Upgrade Validation**
```typescript
import { z } from 'zod';

export const planUpgradeSchema = z.object({
  plan_id: z.number().min(1, 'Plan requerido'),
  periodo: z.enum(['monthly', 'yearly']).optional(),
  usar_trial: z.boolean().optional(),
  metodo_pago: z.string().optional(),
  acepta_terminos: z.boolean().refine(val => val === true, {
    message: 'Debe aceptar términos y condiciones'
  })
});

export type PlanUpgradeFormData = z.infer<typeof planUpgradeSchema>;
```

---

**🎯 Todas las interfaces listas para implementar el sistema de planes en el frontend Core!**
