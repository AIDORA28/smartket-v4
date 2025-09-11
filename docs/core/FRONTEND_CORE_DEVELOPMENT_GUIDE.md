# 🎨 GUÍA DESARROLLO FRONTEND CORE - SmartKet v4

## 🎯 **OBJETIVO**
Implementar las interfaces de usuario para el **módulo Core** (Company & Branch Management) con **backend 100% funcional**.

---

## 📋 **ESTADO ACTUAL**

### ✅ **COMPLETADO**
- **Backend Core**: 107 rutas funcionando
- **Multi-tenant UI**: Context switching implementado
- **User Management**: CRUD completo
- **TypeScript interfaces**: 569 líneas listas
- **Navigation**: Módulo Core visible en sidebar

### ⏳ **PENDIENTE DE IMPLEMENTAR**
1. **Company Settings** - Dashboard configuración empresarial
2. **Company Analytics** - Métricas y KPIs empresariales  
3. **Branch Management** - CRUD sucursales + transferencias
4. **Organization Branding** - Upload logos y personalización

---

## 🏗️ **ARQUITECTURA FRONTEND**

### **Stack Tecnológico**
```bash
Frontend:  React 18 + TypeScript + Inertia.js
Styling:   Tailwind CSS + Heroicons v24
Router:    Ziggy (Laravel routes en JS)
Build:     Vite 5 + ESBuild
Forms:     React Hook Form (recomendado)
Charts:    Recharts o Chart.js (para Analytics)
```

### **Estructura de Archivos**
```
resources/js/
├── Pages/Core/
│   ├── CompanyManagement/
│   │   ├── Settings/
│   │   │   └── Index.tsx          ← PENDIENTE ⏳
│   │   ├── Analytics/
│   │   │   └── Index.tsx          ← PENDIENTE ⏳
│   │   └── Branding/
│   │       └── Index.tsx          ← PENDIENTE ⏳
│   ├── BranchManagement/
│   │   ├── Index.tsx              ← PENDIENTE ⏳
│   │   ├── Create.tsx             ← PENDIENTE ⏳
│   │   ├── Edit.tsx               ← PENDIENTE ⏳
│   │   └── Performance.tsx        ← PENDIENTE ⏳
│   └── UserManagement/
│       └── Index.tsx              ← ✅ COMPLETADO
├── Components/Core/
│   ├── MultiTenant/               ← ✅ COMPLETADO
│   │   ├── CompanySelector.tsx
│   │   ├── BranchSelector.tsx
│   │   └── MultiTenantHeader.tsx
│   ├── Company/                   ← PENDIENTE ⏳
│   │   ├── SettingsCard.tsx
│   │   ├── MetricCard.tsx
│   │   └── BrandingForm.tsx
│   └── Branch/                    ← PENDIENTE ⏳
│       ├── BranchCard.tsx
│       ├── BranchForm.tsx
│       └── TransferForm.tsx
└── Types/
    └── core.ts                    ← ✅ COMPLETADO (569 líneas)
```

---

## 🔧 **APIs DISPONIBLES**

### **1. Company Settings**
```typescript
// GET /core/company/settings
interface CompanySettingsResponse {
  empresa: Empresa;
  configuracion: EmpresaSettings;
  plan_actual: PlanSuscripcion;
  estadisticas: {
    usuarios_count: number;
    sucursales_count: number;
    productos_count: number;
    rubros_count: number;
  };
  limites: {
    usuarios_max: number;
    sucursales_max: number;
    productos_max: number;
    rubros_max: number;
    features_disponibles: string[];
  };
  uso_actual: {
    usuarios_porcentaje: number;
    sucursales_porcentaje: number;
    productos_porcentaje: number;
    rubros_porcentaje: number;
  };
}

// POST /core/company/settings
// PUT /core/company/settings/{section}
// GET /core/company/plans (para mostrar opciones de upgrade)
```

### **2. Company Analytics**
```typescript
// GET /core/company/analytics
interface CompanyAnalyticsResponse {
  empresa: Empresa;
  analytics: EmpresaAnalytics;
  metricas: {
    ventas_mes: number;
    clientes_activos: number;
    productos_stock: number;
    margen_promedio: number;
  };
  comparativas: {
    mes_anterior: number;
    ano_anterior: number;
  };
}
```

### **3. Branch Management**
```typescript
// GET /core/branches
interface BranchManagementResponse {
  sucursales: Sucursal[];
  empresa: Empresa;
  performance: SucursalPerformance[];
  transferencias_pendientes: number;
}

// POST /core/branches (crear)
// PUT /core/branches/{id} (actualizar)
// DELETE /core/branches/{id} (eliminar)
```

### **4. Organization Branding**
```typescript
// GET /core/company/branding
interface OrganizationBrandingResponse {
  empresa: Empresa;
  branding: OrganizationBranding;
  configuracion: {
    logo_url?: string;
    colores_personalizados: boolean;
    tema_activo: string;
  };
}
```

---

## 🎨 **COMPONENTES A IMPLEMENTAR**

### **1. Company Settings (Prioridad Alta)**
**Archivo:** `resources/js/Pages/Core/CompanyManagement/Settings/Index.tsx`

**Funcionalidad:**
```typescript
import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CompanySettingsIndexProps } from '@/Types/core';

export default function CompanySettings({ 
  empresa, 
  configuracion, 
  plan_actual,
  estadisticas,
  limites,
  uso_actual
}: CompanySettingsIndexProps) {
  // Implementar dashboard de configuración empresarial
  // - Tarjetas de configuración por sección
  // - Widget de plan actual con límites y uso
  // - Indicadores de progreso para cada límite
  // - Botón "Upgrade Plan" cuando se acerca a límites
  // - Formularios para settings generales
  // - Información fiscal (RUC, régimen)
  // - Configuraciones regionales
  // - Settings de seguridad
}
```

**UI Components necesarios:**
- `SettingsCard.tsx` - Tarjeta de configuración por sección
- `PlanUsageWidget.tsx` - Widget plan actual con límites y barras progreso
- `UpgradeButton.tsx` - Botón upgrade plan con modal pricing
- `CompanyInfoForm.tsx` - Formulario datos empresa
- `FiscalConfigForm.tsx` - Configuración fiscal
- `RegionalSettingsForm.tsx` - Settings regionales

### **2. Branch Management (Prioridad Alta)**
**Archivo:** `resources/js/Pages/Core/BranchManagement/Index.tsx`

**Funcionalidad:**
```typescript
import React, { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { BranchManagementIndexProps } from '@/Types/core';

export default function BranchManagement({ 
  sucursales, 
  empresa, 
  performance,
  plan_actual,
  limites
}: BranchManagementIndexProps) {
  // Implementar gestión completa de sucursales
  // - Listado con tabla responsive
  // - Verificar límites de plan antes de crear sucursal
  // - Mostrar contador "X de Y sucursales" según plan
  // - Deshabilitar botón "Crear" si llegó al límite
  // - Botones CRUD (crear, editar, eliminar)
  // - Performance indicators por sucursal
  // - Transferencias entre sucursales
  // - Configuraciones específicas por sucursal
}
```

**UI Components necesarios:**
- `BranchTable.tsx` - Tabla listado sucursales
- `BranchCard.tsx` - Tarjeta individual sucursal
- `BranchForm.tsx` - Formulario crear/editar con validación límites
- `BranchLimitCounter.tsx` - Contador "X de Y sucursales" 
- `PerformanceIndicator.tsx` - Métricas por sucursal
- `TransferManager.tsx` - Gestión transferencias

### **3. Company Analytics (Prioridad Media)**
**Archivo:** `resources/js/Pages/Core/CompanyManagement/Analytics/Index.tsx`

**Funcionalidad:**
```typescript
import React, { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CompanyAnalyticsIndexProps } from '@/Types/core';

export default function CompanyAnalytics({ 
  empresa, 
  analytics, 
  metricas 
}: CompanyAnalyticsIndexProps) {
  // Implementar dashboard de analytics
  // - KPIs principales con comparativas
  // - Gráficos de tendencias
  // - Métricas por sucursal
  // - Export de reportes
}
```

**UI Components necesarios:**
- `KPICard.tsx` - Tarjeta métrica con comparativa
- `TrendChart.tsx` - Gráfico de tendencias
- `BranchComparison.tsx` - Comparativa sucursales
- `ExportButton.tsx` - Botón exportar reportes

### **4. Organization Branding (Prioridad Media)**
**Archivo:** `resources/js/Pages/Core/CompanyManagement/Branding/Index.tsx`

**Funcionalidad:**
```typescript
import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { OrganizationBrandingIndexProps } from '@/Types/core';

export default function OrganizationBranding({ 
  empresa, 
  branding 
}: OrganizationBrandingIndexProps) {
  // Implementar personalización de marca
  // - Upload logo empresa
  // - Selector colores corporativos
  // - Preview cambios en tiempo real
  // - Configuración tema UI
}
```

**UI Components necesarios:**
- `LogoUploader.tsx` - Upload archivo logo
- `ColorPicker.tsx` - Selector colores corporativos
- `BrandingPreview.tsx` - Preview cambios
- `ThemeSelector.tsx` - Selector tema UI

---

## 💳 **SISTEMA DE PLANES Y LÍMITES**

### **Estructura de Planes SmartKet**

#### **1. Plan FREE (Gratuito)**
```typescript
interface PlanFree {
  id: 1;
  nombre: "FREE";
  precio: 0;
  moneda: "PEN";
  periodo: "lifetime";
  trial_dias: 0;
  limites: {
    empresas_incluidas: 1;    // FIJO: 1 empresa
    sucursales_incluidas: 1;  // FIJO: 1 sucursal
    usuarios_max: 2;
    rubros_max: 1;
    productos_max: 100;
  };
  features: [
    "reportes_basicos",
    "soporte_email"
  ];
}
```
**Características:**
- ✅ 1 empresa incluida
- ✅ 1 sucursal incluida
- ✅ Hasta 2 usuarios
- ✅ Hasta 100 productos  
- ✅ 1 rubro
- ✅ Reportes básicos
- ✅ Soporte por email

#### **2. Plan BÁSICO (S/ 29/mes)**
```typescript
interface PlanBasico {
  id: 2;
  nombre: "BÁSICO";
  precio: 29.00;
  moneda: "PEN";
  periodo: "monthly";
  trial_dias: 15;
  limites: {
    empresas_incluidas: 1;    // FIJO: 1 empresa
    sucursales_incluidas: 1;  // FIJO: 1 sucursal
    usuarios_max: 5;
    rubros_max: 3;
    productos_max: 1000;
  };
  features: [
    "reportes_basicos",
    "reportes_avanzados", 
    "control_inventario",
    "gestion_clientes",
    "soporte_email",
    "soporte_chat"
  ];
}
```
**Características:**
- ✅ 1 empresa incluida
- ✅ 1 sucursal incluida
- ✅ Hasta 5 usuarios
- ✅ Hasta 1000 productos
- ✅ Hasta 3 rubros
- ✅ Reportes básicos y avanzados
- ✅ Control de inventario
- ✅ Gestión de clientes
- ✅ Soporte por email y chat

#### **3. Plan PROFESIONAL (S/ 59/mes) ⭐ Más Popular**
```typescript
interface PlanProfesional {
  id: 3;
  nombre: "PROFESIONAL";
  precio: 59.00;
  moneda: "PEN";
  periodo: "monthly";
  trial_dias: 30;
  popular: true;
  limites: {
    empresas_incluidas: 1;    // FIJO: 1 empresa
    sucursales_incluidas: 1;  // FIJO: 1 sucursal
    usuarios_max: 15;
    rubros_max: 10;
    productos_max: 5000;
  };
  features: [
    "todos_reportes",
    "integraciones_basicas",
    "api_consulta",
    "control_inventario_avanzado",
    "gestion_proveedores",
    "soporte_prioritario"
  ];
}
```
**Características:**
- ✅ 1 empresa incluida
- ✅ 1 sucursal incluida
- ✅ Hasta 15 usuarios
- ✅ Hasta 5000 productos
- ✅ Hasta 10 rubros
- ✅ Todos los reportes
- ✅ Integraciones básicas
- ✅ API de consulta
- ✅ Control avanzado de inventario
- ✅ Gestión de proveedores
- ✅ Soporte prioritario

#### **4. Plan EMPRESARIAL (S/ 99/mes)**
```typescript
interface PlanEmpresarial {
  id: 4;
  nombre: "EMPRESARIAL";
  precio: 99.00;
  moneda: "PEN";
  periodo: "monthly";
  trial_dias: 30;
  limites: {
    empresas_incluidas: 1;    // FIJO: 1 empresa
    sucursales_incluidas: 1;  // FIJO: 1 sucursal
    usuarios_max: 50;
    rubros_max: 50;
    productos_max: 20000;
  };
  features: [
    "todas_funcionalidades",
    "integraciones_avanzadas",
    "api_completa",
    "dashboard_ejecutivo",
    "analisis_predictivo",
    "soporte_dedicado",
    "capacitacion_incluida"
  ];
}
```
**Características:**
- ✅ 1 empresa incluida
- ✅ 1 sucursal incluida
- ✅ Hasta 50 usuarios
- ✅ Hasta 20,000 productos
- ✅ Hasta 50 rubros
- ✅ Todas las funcionalidades
- ✅ Integraciones avanzadas
- ✅ API completa
- ✅ Dashboard ejecutivo
- ✅ Análisis predictivo
- ✅ Soporte dedicado
- ✅ Capacitación incluida

### **Mini-Planes Adicionales**

#### **Para Owner: Expansión de Capacidad**
```typescript
interface MiniPlanesDisponibles {
  empresa_adicional: {
    nombre: "Empresa Adicional";
    precio: 19.00; // S/ 19/mes por empresa adicional
    descripcion: "Agrega otra empresa a tu cuenta";
    incluye: "1 empresa + 1 sucursal base";
  };
  
  sucursal_adicional: {
    nombre: "Sucursal Adicional";
    precio: 9.00;  // S/ 9/mes por sucursal adicional
    descripcion: "Agrega sucursales a cualquier empresa";
    incluye: "1 sucursal adicional";
  };
  
  usuarios_pack: {
    nombre: "Pack 5 Usuarios";
    precio: 15.00; // S/ 15/mes por pack de 5 usuarios
    descripcion: "Aumenta límite de usuarios";
    incluye: "5 usuarios adicionales";
  };
  
  productos_pack: {
    nombre: "Pack 1000 Productos";
    precio: 8.00;  // S/ 8/mes por pack de 1000 productos
    descripcion: "Aumenta límite de productos";
    incluye: "1000 productos adicionales";
  };
  
  rubros_pack: {
    nombre: "Pack 5 Rubros";
    precio: 5.00;  // S/ 5/mes por pack de 5 rubros
    descripcion: "Aumenta límite de rubros";
    incluye: "5 rubros adicionales";
  };
}
```

### **UI Components para Planes**

#### **PlanUsageWidget.tsx (Actualizado)**
```typescript
interface PlanUsageWidgetProps {
  plan_actual: PlanSuscripcion;
  uso_actual: EmpresaUsoActual;
  mini_planes_contratados: MiniPlanContratado[];
  resumen_mini_planes: ResumenMiniPlanes;
}

// Mostrar:
// - Plan principal (nombre y precio)
// - Barras de progreso para límites del plan
// - Sección "Expansiones contratadas" con mini-planes
// - Facturación total (plan + mini-planes)
// - Botón "Contratar Mini-Planes" para expansión
```

#### **MiniPlanesModal.tsx (Nuevo)**
```typescript
interface MiniPlanesModalProps {
  isOpen: boolean;
  onClose: () => void;
  mini_planes_disponibles: MiniPlan[];
  mini_planes_contratados: MiniPlanContratado[];
  onContratarMiniPlan: (miniPlanId: number, cantidad: number) => Promise<void>;
  onCancelarMiniPlan: (contratadoId: number) => Promise<void>;
}

// Mostrar:
// - Grid de mini-planes disponibles
// - Calculadora de precios en tiempo real
// - Mini-planes ya contratados con opción cancelar
// - Resumen de facturación nueva
```

#### **OwnerDashboard.tsx (Nuevo)**
```typescript
interface OwnerDashboardProps {
  empresas: Empresa[];
  empresa_actual: Empresa;
  plan_principal: PlanSuscripcion;
  mini_planes_contratados: MiniPlanContratado[];
  uso_total_cuenta: {
    empresas_count: number;
    sucursales_count: number;
    usuarios_count: number;
    productos_count: number;
    rubros_count: number;
  };
  facturacion_mensual: {
    plan_principal: number;
    mini_planes_total: number;
    total: number;
  };
}

// Mostrar:
// - Selector de empresa (multi-tenant)
// - Resumen plan principal + mini-planes
// - Uso total de todas las empresas
// - Facturación consolidada
// - Botón "Agregar Empresa/Sucursal/Usuarios"
```

#### **EmpresaCard.tsx (Nuevo)**
```typescript
interface EmpresaCardProps {
  empresa: Empresa;
  is_current: boolean;
  uso_empresa: {
    sucursales_count: number;
    usuarios_count: number;
    productos_count: number;
  };
  onSwitchEmpresa: (empresaId: number) => void;
  onConfigureEmpresa: (empresaId: number) => void;
}

// Mostrar:
// - Nombre y logo de la empresa
// - Uso actual de la empresa específica
// - Indicador si es empresa actual
// - Botones: "Cambiar a esta empresa", "Configurar"
```

#### **UpgradeModal.tsx**
```typescript
interface UpgradeModalProps {
  plan_actual: PlanSuscripcion;
  planes_disponibles: PlanSuscripcion[];
  onSelectPlan: (planId: number) => void;
}

// Mostrar:
// - Comparativa de planes
// - Destacar diferencias con plan actual
// - Botón CTA para cada plan superior
// - Información de trial disponible
```

#### **LimitAlert.tsx**
```typescript
interface LimitAlertProps {
  tipo: 'usuarios' | 'sucursales' | 'productos' | 'rubros';
  porcentaje_usado: number;
  limite_max: number;
  usado_actual: number;
}

// Mostrar alertas cuando:
// - 80%: Advertencia amarilla
// - 90%: Alerta naranja  
// - 95%: Crítica roja
// - 100%: Límite alcanzado
```

---

## 🎨 **DESIGN PATTERNS**

### **Layout Pattern**
```typescript
export default function CorePage({ data }: Props) {
  return (
    <AuthenticatedLayout
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          Configuración Empresa
        </h2>
      }
    >
      <Head title="Configuración Empresa" />
      
      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          {/* Contenido principal */}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
```

### **Form Pattern**
```typescript
import { useForm } from '@inertiajs/react';

const { data, setData, post, processing, errors } = useForm({
  nombre: empresa.nombre || '',
  ruc: empresa.ruc || '',
  direccion: empresa.direccion || '',
});

const submit = (e: FormEvent) => {
  e.preventDefault();
  post(route('core.company.settings.store'));
};
```

### **API Call Pattern**
```typescript
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';

// Para refreshes
const refreshData = () => {
  router.get(route('core.company.analytics.refresh'));
};

// Para exportes
const exportData = () => {
  window.open(route('core.company.analytics.export'));
};
```

---

## 🎯 **PLAN DE DESARROLLO**

### **Sprint 1: Company Settings (3-4 días)**
**Día 1-2:**
- ✅ Crear estructura base `CompanyManagement/Settings/Index.tsx`
- ✅ Implementar layout y navegación
- ✅ Crear componentes base (SettingsCard, CompanyInfoForm)

**Día 3-4:**
- ✅ Integrar formularios con APIs
- ✅ Implementar validaciones
- ✅ Testing funcional básico

### **Sprint 2: Branch Management (4-5 días)**
**Día 1-2:**
- ✅ Crear `BranchManagement/Index.tsx`
- ✅ Implementar tabla listado sucursales
- ✅ Crear formularios CRUD

**Día 3-4:**
- ✅ Integrar performance indicators
- ✅ Implementar transferencias
- ✅ Configuraciones específicas

**Día 5:**
- ✅ Testing e integración

### **Sprint 3: Analytics (3-4 días)**
**Día 1-2:**
- ✅ Setup gráficos (Recharts/Chart.js)
- ✅ Crear KPI cards con comparativas
- ✅ Implementar tendencias

**Día 3-4:**
- ✅ Comparativas por sucursal
- ✅ Export funcionalidad
- ✅ Testing y optimización

### **Sprint 4: Branding (2-3 días)**
**Día 1-2:**
- ✅ Implementar upload logos
- ✅ Selector colores corporativos
- ✅ Preview en tiempo real

**Día 3:**
- ✅ Testing final
- ✅ Documentación

---

## 🛠️ **HERRAMIENTAS Y LIBRERÍAS**

### **Recomendadas para Instalar:**
```bash
# Para formularios avanzados
pnpm install react-hook-form

# Para gráficos (elegir una)
pnpm install recharts          # Más React-friendly
pnpm install chart.js react-chartjs-2  # Más potente

# Para upload archivos
pnpm install react-dropzone   # Optional

# Para colores
pnpm install react-color      # Color picker
```

### **Ya Disponibles:**
- ✅ **React 18** + TypeScript
- ✅ **Tailwind CSS** + Heroicons v24
- ✅ **Inertia.js** para SPA
- ✅ **Ziggy** route helper
- ✅ **Vite** para build

---

## 📊 **DATOS DE PRUEBA DISPONIBLES**

### **Empresas (2 registros)**
```json
{
  "id": 1,
  "nombre": "Tienda Don José",
  "ruc": "20123456789",
  "plan_id": 3,
  "plan_nombre": "PROFESIONAL",
  "plan_precio": 59.00,
  "activa": true,
  "sucursales_count": 3,
  "usuarios_count": 8,
  "productos_count": 1250,
  "rubros_count": 5,
  "limites": {
    "usuarios_max": 15,
    "sucursales_max": 5,
    "productos_max": 5000,
    "rubros_max": 10
  },
  "uso_porcentaje": {
    "usuarios": 53.3,
    "sucursales": 60.0,
    "productos": 25.0,
    "rubros": 50.0
  }
}
```

### **Planes Disponibles (4 registros)**
```json
[
  {
    "id": 1,
    "nombre": "FREE",
    "precio": 0,
    "popular": false,
    "trial_dias": 0
  },
  {
    "id": 2,
    "nombre": "BÁSICO", 
    "precio": 29.00,
    "popular": false,
    "trial_dias": 15
  },
  {
    "id": 3,
    "nombre": "PROFESIONAL",
    "precio": 59.00,
    "popular": true,
    "trial_dias": 30
  },
  {
    "id": 4,
    "nombre": "EMPRESARIAL",
    "precio": 99.00,
    "popular": false,
    "trial_dias": 30
  }
]
```

### **Sucursales (4 registros)**
```json
{
  "id": 1,
  "empresa_id": 1,
  "nombre": "Sucursal Principal",
  "codigo_interno": "PRIN",
  "direccion": "Av. Los Alamos 123, San Isidro",
  "activa": true
}
```

### **Usuarios (5 registros)**
- Disponibles para testing roles y permisos

---

## 🔍 **TESTING STRATEGY**

### **Testing Manual**
1. **Login** como Owner (disponible en datos prueba)
2. **Navegar** al módulo Core en sidebar
3. **Probar** cada funcionalidad implementada
4. **Verificar** context switching empresa/sucursal
5. **Validar** responsive design

### **APIs Testing**
```bash
# Verificar APIs disponibles
php artisan route:list --name=core.company
php artisan route:list --name=core.branches

# Test manual APIs
GET  /core/company/settings
POST /core/company/settings
GET  /core/branches
POST /core/branches
```

---

## 🚀 **DEPLOYMENT**

### **Build Frontend**
```bash
pnpm run build    # Compila React + TypeScript
```

### **Verificar Compilación**
```bash
# Debe compilar sin errores TypeScript
# Debe generar manifest.json
# Debe crear archivos SSR
```

---

## 📋 **CHECKLIST IMPLEMENTACIÓN**

### **Company Settings**
- [ ] Página base `Settings/Index.tsx`
- [ ] Componente `SettingsCard.tsx`
- [ ] Widget `PlanUsageWidget.tsx` con barras progreso
- [ ] Componente `UpgradeButton.tsx` y modal pricing
- [ ] Componente `LimitAlert.tsx` para alertas límites
- [ ] Formulario `CompanyInfoForm.tsx`
- [ ] Integración API `POST /core/company/settings`
- [ ] API planes `GET /core/company/plans`
- [ ] Validaciones frontend
- [ ] Testing funcional
- [ ] Sistema upgrade plans

### **Branch Management**
- [ ] Página base `BranchManagement/Index.tsx`
- [ ] Componente `BranchTable.tsx`
- [ ] Formulario `BranchForm.tsx`
- [ ] CRUD completo sucursales
- [ ] Performance indicators
- [ ] Sistema transferencias

### **Company Analytics**
- [ ] Página base `Analytics/Index.tsx`
- [ ] Componente `KPICard.tsx`
- [ ] Gráficos con Recharts/Chart.js
- [ ] Export funcionalidad
- [ ] Responsive design

### **Organization Branding**
- [ ] Página base `Branding/Index.tsx`
- [ ] Upload logos
- [ ] Color picker
- [ ] Preview tiempo real
- [ ] Integración API branding

---

**🎯 Objetivo: Implementar frontend Core completo en 10-14 días de desarrollo**

**📞 Soporte: Backend 100% listo, APIs funcionando, datos de prueba disponibles**
