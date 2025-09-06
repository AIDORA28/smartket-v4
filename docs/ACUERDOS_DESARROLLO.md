# 📋 ACUERDOS DE DESARROLLO - SmartKet v4

## 🎯 **FILOSOFÍA DE DESARROLLO**

### **Principios Fundamentales:**
- **Pragmatismo sobre hype**: No usar tecnologías nuevas sin beneficio real
- **Funcionalidad sobre modernidad**: Preferir soluciones que funcionen bien y sean mantenibles
- **Menos es más**: Evitar complejidad innecesaria y código "spaghetti"
- **Escalabilidad sin ruptura**: El sistema debe crecer sin volverse un laberinto técnico

### **Stack Tecnológico Acordado:**
- **Backend**: Laravel 12 + PHP 8.3 (ESTRUCTURA INTACTA - NO TOCAR)
- **Frontend**: React 18 + TypeScript + Inertia.js (migración desde Livewire por rendimiento)
- **Base de datos**: PostgreSQL con Supabase (35 tablas migradas)
- **Package Manager**: PNPM (3x más rápido que NPM)
- **Deploy**: Vercel con PNPM oficial
- **Desarrollo local**: Laragon

---

## 🧹 **ORDEN Y LIMPIEZA**

### **Repositorio Limpio:**
- ✅ Eliminados archivos temporales (`test_*.php`, `check_*.php`, `crear_*.php`)
- ✅ Sin archivos NPM innecesarios (usamos PNPM)
- ✅ Documentación organizada en `/docs/`
- ✅ Pruebas organizadas en `/tests/` únicamente

### **Estructura de Archivos:**
- **Backend**: `app/`, `config/`, `routes/`, `database/` → **NO TOCAR**
- **Frontend**: `resources/js/` → Migrar a React + Inertia.js
- **Configuración**: Todos los archivos config son necesarios y funcionales

---

## 🚀 **PLAN DE MIGRACIÓN ORDENADA**

### **FASE 1: LIMPIEZA** ✅ COMPLETADA
- Repositorio limpio y ordenado
- Archivos innecesarios eliminados
- Configuración TypeScript corregida

### **FASE 2: MIGRACIÓN REACT + INERTIA.JS** 🔄 EN PROGRESO
- Finalizar migración desde Livewire
- Componentes React organizados y funcionales
- Sistema más fluido y rápido

### **FASE 3: DEPLOY A VERCEL**
- Configuración con PNPM oficial
- Optimización para producción
- CI/CD automático

### **FASE 4: DESARROLLO MODULAR**
- **Módulo A**: Sistema POS (Point of Sale)
- **Módulo B**: Gestión de Productos
- **Módulo C**: Control de Inventario  
- **Módulo D**: Reportes y Analytics

---

## ⚡ **OPTIMIZACIÓN DE RENDIMIENTO**

### **Justificación Técnica:**
- **Livewire → React**: Eliminar lentitud del sistema actual
- **PNPM**: 3x más rápido en desarrollo
- **Inertia.js**: SPA fluido sin API compleja
- **Vercel**: Edge functions y CDN global

### **Métricas Objetivo:**
- Tiempo de carga: <2s (vs 8-10s actual)
- Bundle size optimizado
- Developer Experience mejorado

---

## 📝 **DESARROLLO INCREMENTAL**

### **Metodología:**
1. **Iterativo**: Un módulo a la vez
2. **Testeable**: Cada feature probada antes de merge
3. **Documentado**: Cambios explicados y justificados
4. **Escalable**: Preparado para crecimiento futuro

### **Control de Calidad:**
- Código limpio y comentado
- TypeScript para type safety
- Pruebas organizadas en `/tests/`
- Deploy automático desde main

---

## 🔒 **COMPROMISOS**

### **Lo que NO haremos:**
- ❌ Experimentar con frameworks sin justificación
- ❌ Crear código "spaghetti"
- ❌ Tocar la estructura del backend
- ❌ Agregar complejidad innecesaria

### **Lo que SÍ haremos:**
- ✅ Mantener código limpio y mantenible
- ✅ Justificar cada decisión técnica
- ✅ Priorizar funcionalidad sobre modernidad
- ✅ Desarrollar de forma ordenada y progresiva

---

*Documento creado: 6 de septiembre de 2025*  
*Proyecto: SmartKet v4 ERP - Migración React + Inertia.js*
