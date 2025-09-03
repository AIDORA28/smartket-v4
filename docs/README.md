# 📚 SmartKet ERP - Documentación Técnica

**Fecha:** 30 Agosto 2025  
**Estado:** 🔄 EN MIGRACIÓN A NUEVA METODOLOGÍA  
**Versión:** 1.0 Inicial  

---

## 📋 **METODOLOGÍA DE DOCUMENTACIÓN**

### 🎯 **REGLAS DE DOCUMENTACIÓN (NO NEGOCIABLES)**

1. **📁 ORGANIZACIÓN:**
   - TODA documentación debe estar en `/docs`
   - NO más archivos `.md` sueltos en la raíz
   - Cada archivo tiene un propósito específico y claro

2. **🔄 CONSISTENCY FIRST:**
   - Cualquier cambio de arquitectura SE DOCUMENTA PRIMERO
   - Base de datos y documentación deben estar 100% alineadas
   - NO se cambia nada sin actualizar la documentación correspondiente

3. **📖 SINGLE SOURCE OF TRUTH:**
   - `MASTER_SPEC.md` = Especificación completa del proyecto
   - `DATABASE_SCHEMA.md` = Esquema de base de datos actualizado
   - `ARQUITECTURA.md` = Decisiones arquitectónicas finales

4. **🚫 PROHIBICIONES:**
   - NO cambiar stack tecnológico sin documentar razones
   - NO crear migraciones sin verificar contra el esquema documentado
   - NO implementar features sin definir en la documentación

---

## 📁 **ESTRUCTURA DE DOCUMENTACIÓN**

```
docs/
├── README.md                    ← Este archivo (índice general)
├── MASTER_SPEC.md              ← 🎯 FUENTE ÚNICA DE VERDAD
├── ARQUITECTURA.md             ← Decisiones arquitectónicas
├── DATABASE_SCHEMA.md          ← Esquema base de datos
├── BACKEND_SPEC.md             ← Especificación backend
├── FRONTEND_SPEC.md            ← Especificación frontend
├── API_REFERENCE.md            ← Documentación API endpoints
├── FEATURE_FLAGS.md            ← Gestión de features por plan
├── DEPLOYMENT_GUIDE.md         ← Guía de deployment
├── TESTING_STRATEGY.md         ← Estrategia de testing
├── SECURITY_COMPLIANCE.md     ← Seguridad y compliance
├── ROADMAP.md                  ← Roadmap de desarrollo
├── MIGRATION_FIXES.md          ← Correcciones de migraciones
└── DICCIONARIO_NEGOCIO.md     ← Términos y roles del negocio
```

---

## 🔍 **ÍNDICE DE DOCUMENTACIÓN**

### **📖 Documentos Principales**
- **[MASTER_SPEC.md](./MASTER_SPEC.md)** - Especificación maestra completa del proyecto
- **[ARQUITECTURA.md](./ARQUITECTURA.md)** - Stack tecnológico y decisiones arquitectónicas
- **[DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)** - Esquema completo de base de datos

### **🛠️ Documentos Técnicos**
- **[BACKEND_SPEC.md](./BACKEND_SPEC.md)** - Laravel controllers, services, models
- **[FRONTEND_SPEC.md](./FRONTEND_SPEC.md)** - Livewire components y blade views
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Endpoints y documentación API

### **🎯 Gestión de Proyecto**
- **[FEATURE_FLAGS.md](./FEATURE_FLAGS.md)** - Features por plan y rubro
- **[ROADMAP.md](./ROADMAP.md)** - Fases de implementación
- **[MIGRATION_FIXES.md](./MIGRATION_FIXES.md)** - Correcciones de migraciones

### **🚀 Operaciones**
- **[DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)** - Guía de despliegue
- **[TESTING_STRATEGY.md](./TESTING_STRATEGY.md)** - Estrategia de pruebas
- **[SECURITY_COMPLIANCE.md](./SECURITY_COMPLIANCE.md)** - Seguridad y compliance

### **📝 Negocio**
- **[DICCIONARIO_NEGOCIO.md](./DICCIONARIO_NEGOCIO.md)** - Roles, términos y conceptos

---

## 🎯 **WORKFLOW DE DOCUMENTACIÓN**

### **📝 Para Cambios de Arquitectura:**
1. Actualizar `ARQUITECTURA.md` PRIMERO
2. Justificar el cambio con razones técnicas
3. Actualizar `MASTER_SPEC.md` si afecta la especificación general
4. Implementar los cambios en código
5. Verificar que todo esté alineado

### **🗄️ Para Cambios de Base de Datos:**
1. Actualizar `DATABASE_SCHEMA.md` PRIMERO
2. Crear las migraciones siguiendo el esquema documentado
3. Verificar que no hay conflicts con el estado actual
4. Ejecutar las migraciones
5. Validar que la implementación coincide con la documentación

### **🔧 Para Nuevas Features:**
1. Documentar en `FEATURE_FLAGS.md` si requiere feature flag
2. Actualizar `BACKEND_SPEC.md` o `FRONTEND_SPEC.md` según corresponda
3. Actualizar `API_REFERENCE.md` si hay nuevos endpoints
4. Implementar la feature
5. Actualizar `ROADMAP.md` con el progreso

---

## 🚨 **ALERTAS DE CONSISTENCIA**

### **❌ NUNCA MÁS HACER:**
- Cambiar el stack tecnológico por quinta vez
- Crear migraciones que no coincidan con la documentación
- Implementar features sin documentar primero
- Tener archivos `.md` dispersos en la raíz del proyecto

### **✅ SIEMPRE HACER:**
- Verificar que la documentación esté actualizada antes de cualquier cambio
- Usar `MASTER_SPEC.md` como referencia única
- Mantener alineados documentación y código
- Documentar decisiones y razones

---

## 📊 **ESTADO ACTUAL**

### **✅ Completado:**
- [x] Creación de la carpeta `/docs`
- [x] Migración de `MASTER_SPEC.md` (actualizado con Laravel+Livewire)
- [x] Metodología de documentación establecida

### **🔄 En Progreso:**
- [ ] Migración de todos los archivos `.md` desde la raíz
- [ ] Creación de documentos específicos (DATABASE_SCHEMA, ARQUITECTURA, etc.)
- [ ] Verificación de consistencia entre documentación y código

### **❌ Pendiente:**
- [ ] Corrección de migraciones según esquema documentado
- [ ] Implementación de features documentadas
- [ ] Testing de la documentación vs implementación real

---

## 🔗 **LINKS RÁPIDOS**

- **Repositorio:** https://github.com/AIDORA28/SmartKet-V2
- **Issue Tracker:** (Pendiente)
- **Demo:** (Pendiente)
- **Documentación API:** (Pendiente - se generará desde API_REFERENCE.md)

---

**🎯 ESTE DIRECTORIO ES LA NUEVA FUENTE ÚNICA DE VERDAD PARA TODA LA DOCUMENTACIÓN**

*Creado: 30 Agosto 2025*  
*Metodología: Documentation-Driven Development*  
*Estado: 🔄 EN MIGRACIÓN ACTIVA*
