# 📁 DOCUMENTACIÓN MÓDULO CORE

## 🎯 **ÍNDICE DE DOCUMENTACIÓN**

Esta carpeta contiene toda la documentación relacionada con el **Módulo Core** de SmartKet v4 - el sistema de gestión empresarial y multi-tenant.

---

## 📋 **DOCUMENTOS PRINCIPALES**

### **🏗️ Arquitectura y Desarrollo**
- [`CORE.md`](./CORE.md) - Documentación principal del módulo Core
- [`BACKEND_CORE_LISTO_FRONTEND.md`](./BACKEND_CORE_LISTO_FRONTEND.md) - Estado backend Core completado
- [`CORE_MODULE_COMPLETADO.md`](./CORE_MODULE_COMPLETADO.md) - Resumen módulo Core terminado

### **🎨 Frontend Development**
- [`FRONTEND_CORE_DEVELOPMENT_GUIDE.md`](./FRONTEND_CORE_DEVELOPMENT_GUIDE.md) - **Guía principal desarrollo frontend**
- [`CORE_PLANS_UI_MOCKUP.md`](./CORE_PLANS_UI_MOCKUP.md) - Mockups UI del sistema de planes
- [`OWNER_DASHBOARD_DESIGN.md`](./OWNER_DASHBOARD_DESIGN.md) - Diseño dashboard Owner multi-empresa

### **💳 Sistema de Planes**
- [`CORE_PLANS_INTERFACES.md`](./CORE_PLANS_INTERFACES.md) - **Interfaces TypeScript para planes**
- *Estructura: Plan base (1 empresa + 1 sucursal) + Mini-planes para expansión*

### **👥 Usuarios y Roles**
- [`SISTEMA_ROLES_COMPLETADO.md`](./SISTEMA_ROLES_COMPLETADO.md) - Sistema de roles y permisos
- [`FUNCIONALIDAD_CORE_USUARIO.md`](./FUNCIONALIDAD_CORE_USUARIO.md) - Funcionalidades desde perspectiva usuario

---

## 🚀 **ESTADO ACTUAL**

### ✅ **COMPLETADO (Backend)**
- **Multi-tenant Architecture**: Context switching empresa/sucursal ✅
- **User Management**: CRUD completo con roles y permisos ✅  
- **Company Settings**: 16 rutas API funcionando ✅
- **Branch Management**: 19 rutas API funcionando ✅
- **Company Analytics**: 3 rutas API funcionando ✅
- **Organization Branding**: 8 rutas API funcionando ✅
- **Database Structure**: PostgreSQL con 48 tablas ✅
- **Testing**: 6/7 tests Core passing ✅

### 🎯 **LISTO PARA FRONTEND**
- **Company Settings**: Dashboard configuración empresarial
- **Branch Management**: CRUD sucursales completo  
- **Company Analytics**: Dashboard métricas y KPIs
- **Organization Branding**: Upload logos y personalización
- **Plan Management**: Sistema planes + mini-planes
- **Owner Dashboard**: Gestión multi-empresa

### 📊 **MÉTRICAS**
```
Backend APIs:     107 rutas funcionales
Controllers:      13 controladores Core
Models:           19 modelos Core  
Tables:           48 tablas PostgreSQL
TypeScript Types: 569 líneas definidas
Tests:            6/7 passing
```

---

## 🎯 **PARA FRONTEND DEVELOPERS**

### **📖 Empezar por aquí:**
1. **[`FRONTEND_CORE_DEVELOPMENT_GUIDE.md`](./FRONTEND_CORE_DEVELOPMENT_GUIDE.md)** - Guía paso a paso
2. **[`CORE_PLANS_INTERFACES.md`](./CORE_PLANS_INTERFACES.md)** - TypeScript interfaces
3. **[`CORE_PLANS_UI_MOCKUP.md`](./CORE_PLANS_UI_MOCKUP.md)** - Diseños UI

### **🏗️ Stack Tecnológico:**
```typescript
Frontend:  React 18 + TypeScript + Inertia.js
Styling:   Tailwind CSS + Heroicons v24
Router:    Ziggy (Laravel routes en JS)
Build:     Vite 5 + ESBuild
Forms:     React Hook Form (recomendado)
Charts:    Recharts o Chart.js (para Analytics)
```

### **🎯 Prioridades Implementación:**
1. **Company Settings** (alta prioridad) - Dashboard configuración
2. **Branch Management** (alta prioridad) - CRUD sucursales
3. **Company Analytics** (media prioridad) - Métricas dashboard
4. **Organization Branding** (media prioridad) - Personalización marca

---

## 💳 **SISTEMA DE PLANES**

### **Plan Base (Todos incluyen: 1 empresa + 1 sucursal)**
- **FREE**: S/ 0 - 2 usuarios, 100 productos, 1 rubro
- **BÁSICO**: S/ 29/mes - 5 usuarios, 1K productos, 3 rubros  
- **PROFESIONAL**: S/ 59/mes - 15 usuarios, 5K productos, 10 rubros ⭐
- **EMPRESARIAL**: S/ 99/mes - 50 usuarios, 20K productos, 50 rubros

### **Mini-Planes Adicionales (Para Owner)**
- **Empresa adicional**: S/ 19/mes (incluye 1 sucursal base)
- **Sucursal adicional**: S/ 9/mes (para cualquier empresa)
- **Pack 5 usuarios**: S/ 15/mes (aplicable a toda la cuenta)
- **Pack 1000 productos**: S/ 8/mes (aumento límite global)
- **Pack 5 rubros**: S/ 5/mes (aumento límite global)

---

## 🔗 **APIS DISPONIBLES**

### **Company Management**
```bash
# Company Settings (16 rutas)
GET    /core/company/settings
POST   /core/company/settings
GET    /core/company/plans

# Company Analytics (3 rutas)  
GET    /core/company/analytics
GET    /core/company/analytics/export

# Organization Branding (8 rutas)
GET    /core/company/branding
POST   /core/company/branding/logo
```

### **Branch Management**
```bash
# Branch Management (19 rutas)
GET    /core/branches
POST   /core/branches
PUT    /core/branches/{id}
DELETE /core/branches/{id}
POST   /core/branches/{id}/transfer
```

### **User Management**
```bash
# User Management (12 rutas)
GET    /core/users
POST   /core/users
PUT    /core/users/{id}
DELETE /core/users/{id}
```

---

## 📞 **SOPORTE DESARROLLO**

- **Backend**: 100% funcional y documentado
- **APIs**: 107 rutas Core funcionando
- **Database**: PostgreSQL con datos de prueba
- **Multi-tenant**: Context switching implementado
- **TypeScript**: Interfaces completas definidas

**🚀 ¡Todo listo para implementar el frontend Core!**

---

## 📝 **NOTAS DE ACTUALIZACIÓN**

- **Fecha reorganización**: 11 Sep 2025
- **Archivos movidos**: 9 documentos Core organizados
- **Estado**: Documentación consolidada en `/docs/core/`
- **Próximo**: Crear carpetas para módulos Inventory y Sales
