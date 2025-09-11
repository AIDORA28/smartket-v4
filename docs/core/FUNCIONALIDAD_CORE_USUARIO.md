# 🏢 GUÍA FUNCIONAL COMPLETA DEL CORE - Perspectiva del Usuario

## 👑 **¿QUE HACE UN USUARIO OWNER?**

### 🎯 **ESCENARIO: Te registras como Owner de tu negocio**

Cuando un **Owner** se registra y entra al sistema, tiene acceso completo a las funcionalidades del Core para **administrar su empresa** como un verdadero **"Administrador de Negocio"**.

---

## 🏢 **1. GESTIÓN DE EMPRESA**

### ✅ **Configuración General de la Empresa**
**Ubicación**: `/core/company/settings`

**¿Qué puede hacer el Owner?**
- **Editar datos de la empresa**: Nombre, RUC, dirección, teléfono
- **Configurar settings empresariales**:
  - 📧 Notificaciones y alertas
  - 💰 Configuración de facturación
  - 📦 Settings de inventario (stock mínimo, alertas)
  - 🌍 Configuraciones regionales (timezone, moneda)
  - 🔒 Configuraciones de seguridad
- **Gestionar información fiscal**: RUC, régimen tributario

### ✅ **Branding y Personalización**
**Ubicación**: `/core/company/branding`

**¿Qué puede hacer el Owner?**
- **Upload del logo** de la empresa
- **Configurar colores** corporativos
- **Personalizar la identidad visual** del sistema
- **Configurar datos de contacto** públicos

### ✅ **Analytics Empresarial**
**Ubicación**: `/core/company/analytics`

**¿Qué ve el Owner?**
- **KPIs principales**:
  - 💰 Ventas del mes vs mes anterior
  - 👥 Número de clientes activos
  - 📦 Productos en stock vs vendidos
  - 📈 Márgenes de ganancia
- **Métricas de performance**:
  - 📊 Comparativas por sucursal
  - 📈 Tendencias de ventas
  - ⭐ Indicadores de satisfacción
- **Exportar reportes** para análisis externo

---

## 🏪 **2. GESTIÓN DE SUCURSALES**

### ✅ **Administración Multi-Sucursal**
**Ubicación**: `/core/branches`

**¿Qué puede hacer el Owner?**

#### **CRUD Completo de Sucursales**:
- ➕ **Crear nuevas sucursales**
- ✏️ **Editar información**: nombre, dirección, teléfono
- 🗑️ **Eliminar sucursales** (con validaciones)
- 👁️ **Ver detalles** de cada sucursal

#### **Performance por Sucursal**:
- 📊 **Ver métricas individuales** de cada sucursal
- 💰 **Comparar ventas** entre sucursales
- 📈 **Analizar tendencias** por ubicación
- 🎯 **Identificar sucursales** más/menos rentables

#### **Configuraciones Específicas**:
- ⚙️ **Settings únicos** por sucursal
- 👥 **Asignar usuarios** específicos
- 📦 **Configurar inventario** independiente
- 🕐 **Horarios de operación** por sucursal

#### **Transferencias entre Sucursales**:
- 📦 **Mover inventario** entre sucursales
- 📋 **Crear solicitudes** de transferencia
- ✅ **Aprobar/rechazar** transferencias
- 📊 **Tracking de inventario** en tránsito

---

## 👥 **3. GESTIÓN DE USUARIOS**

### ✅ **Administración Completa de Usuarios**
**Ubicación**: `/core/users`

**¿Qué puede hacer el Owner?**

#### **CRUD de Usuarios**:
- ➕ **Crear nuevos usuarios** para su empresa
- ✏️ **Editar información**: datos personales, contacto
- 🗑️ **Eliminar usuarios** (con validaciones)
- 👁️ **Ver perfiles** completos

#### **Gestión de Roles y Permisos**:
- 👑 **Asignar roles**: admin, vendedor, cajero, almacenero
- 🔐 **Definir permisos** específicos por usuario
- 🏪 **Asignar sucursales** específicas a cada usuario
- 🎯 **Controlar acceso** a módulos del sistema

#### **Multi-tenant Control**:
- 🏢 **Usuarios solo ven datos** de su empresa
- 🏪 **Acceso limitado** a sucursales asignadas
- 🔒 **Seguridad automática** por contexto

---

## 🔄 **4. CAMBIO DE CONTEXTO (MULTI-TENANT)**

### ✅ **Selector de Empresa/Sucursal**
**Ubicación**: Header superior (siempre visible)

**¿Cómo funciona para el Owner?**

#### **Cambio de Empresa** (si tiene acceso a múltiples):
- 🏢 **ComboBox de empresas** en el header
- 🔄 **Cambio instantáneo** de contexto
- 📊 **Datos filtrados** automáticamente
- 🎯 **Todo el sistema** se adapta a la empresa seleccionada

#### **Cambio de Sucursal**:
- 🏪 **Dropdown de sucursales** disponibles
- 📍 **Selección rápida** con un clic
- 📊 **Dashboards actualizados** en tiempo real
- 🎯 **Inventario y ventas** filtradas por sucursal

#### **Indicador Visual**:
```
👑 Juan Pérez (Owner)
🏢 Tienda Don José  [▼]
📍 Sucursal Norte   [▼]
```

---

## 🎛️ **5. CONTROL DE PLANES Y FEATURES**

### ✅ **Gestión de Plan Empresarial**
**¿Qué controla el Owner según su plan?**

#### **Plan Básico**:
- ✅ Una sola sucursal
- ✅ Hasta 3 usuarios
- ✅ POS básico
- ❌ No multi-sucursal
- ❌ Sin reportes avanzados

#### **Plan Profesional**:
- ✅ Hasta 5 sucursales
- ✅ Hasta 15 usuarios  
- ✅ POS completo
- ✅ Multi-sucursal
- ✅ Reportes avanzados
- ✅ Transferencias entre sucursales

#### **Plan Empresarial**:
- ✅ Sucursales ilimitadas
- ✅ Usuarios ilimitados
- ✅ Todas las funcionalidades
- ✅ API access
- ✅ Soporte prioritario

### ✅ **Feature Flags Dinámicos**:
El sistema automáticamente **habilita/deshabilita** funcionalidades según el plan:
- 🔒 Botones bloqueados si no tienes el feature
- 💎 Upgrades sugeridos en tiempo real
- ⚡ Cambios instantáneos al upgradeear plan

---

## 🔐 **6. CONTROL DE ACCESO Y SEGURIDAD**

### ✅ **Qué controla el Owner**:

#### **Acceso por Rol**:
- 👑 **Owner**: Acceso total al Core
- 👨‍💼 **Admin**: Acceso limitado al Core  
- 👨‍💻 **Vendedor**: Solo POS y ventas
- 👨‍💰 **Cajero**: Solo caja y cobros
- 📦 **Almacenero**: Solo inventario

#### **Restricciones Automáticas**:
- 🏢 **Usuarios solo ven** datos de su empresa
- 🏪 **Sucursales asignadas** únicamente
- 📊 **Reportes filtrados** por permisos
- 🔒 **APIs protegidas** por contexto

---

## 🎯 **FLUJO DE TRABAJO TÍPICO DEL OWNER**

### **📅 Rutina Diaria**:
1. **Login** → Sistema carga automáticamente su empresa
2. **Dashboard** → Ve métricas generales de todas sus sucursales
3. **Cambio de sucursal** → Revisa performance específica
4. **Core Analytics** → Analiza tendencias del negocio
5. **Gestión usuarios** → Ajusta permisos según necesidad
6. **Configuraciones** → Actualiza settings empresariales

### **📊 Rutina Semanal**:
1. **Reportes comparativos** entre sucursales
2. **Performance review** de usuarios
3. **Configuración de transferencias** de inventario
4. **Análisis de plan** y features necesarias

### **🚀 Rutina Mensual**:
1. **Review completo** de analytics empresarial
2. **Optimización de sucursales** menos rentables
3. **Evaluación de upgrade** de plan
4. **Configuración de nuevas sucursales** (crecimiento)

---

## ✅ **RESPUESTA DIRECTA A TUS PREGUNTAS**:

### **❓ ¿El Owner puede ver gestión de empresas?**
**✅ SÍ** - Puede gestionar completamente SU empresa y todas sus configuraciones

### **❓ ¿Puede configurar sucursales?**
**✅ SÍ** - CRUD completo de sucursales, transferencias, performance, settings específicos

### **❓ ¿Puede gestionar usuarios?**
**✅ SÍ** - Crear, editar, eliminar usuarios con roles y permisos específicos

### **❓ ¿Depende del plan que tenga?**
**✅ SÍ** - Las funcionalidades se habilitan/deshabilitan según su plan:
- Plan Básico: 1 sucursal, 3 usuarios
- Plan Profesional: 5 sucursales, 15 usuarios  
- Plan Empresarial: Sin límites

### **❓ ¿Puede cambiar de sucursal/negocio con botones?**
**✅ SÍ** - ComboBox en el header superior:
- **Dropdown de empresa** (si tiene acceso a múltiples)
- **Dropdown de sucursal** con cambio instantáneo
- **Contexto automático** en todo el sistema

---

## 🎉 **CONCLUSIÓN**

**El Core convierte al Owner en un verdadero "Super Administrador" de su negocio**, con control total sobre:

- 🏢 **Su empresa** (configuración, branding, analytics)
- 🏪 **Sus sucursales** (CRUD, performance, transferencias)  
- 👥 **Sus usuarios** (roles, permisos, accesos)
- 🔄 **Contexto multi-tenant** (cambio fluido empresa/sucursal)
- 🎛️ **Features** (según plan contratado)

**Es como tener un "Panel de Control Empresarial" completo para gestionar todo su negocio desde una sola plataforma.**

---
*SmartKet v4.0.0 - Sistema Multi-tenant Empresarial*
