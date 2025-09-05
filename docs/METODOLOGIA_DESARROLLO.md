# 📋 METODOLOGÍA DE DESARROLLO - SmartKet v4

> **DOCUMENTO OBLIGATORIO DE CONSULTA**  
> Leer ANTES de cada implementación de módulo

## 🚨 PRINCIPIOS FUNDAMENTALES

### 1. **NO ASUMIR NADA**
- ❌ **NUNCA** asumir que existen rutas, funcionalidades o APIs sin verificar
- ✅ **SIEMPRE** verificar primero qué existe en el backend
- ✅ **SIEMPRE** usar `php artisan route:list` para ver rutas disponibles
- ✅ **SIEMPRE** revisar `routes/web.php` y `routes/api.php`

### 2. **TRABAJAR CON LO QUE EXISTE**
- ✅ Usar solo las rutas que están definidas
- ✅ Usar solo los modelos que están creados
- ✅ Usar solo las APIs que están implementadas
- ✅ Si algo no existe, usar placeholders temporales

### 3. **MANTENER LA SIMPLICIDAD**
- ✅ Preferir herramientas probadas y confiables
- ✅ No complicar el sistema innecesariamente
- ✅ Recordar: "Un sistema del gobierno usaba jQuery - antiguo pero cumplía necesidades"
- ✅ Funcionalidad sobre complejidad técnica

### 4. **COMPLETAR LO PLANIFICADO**
- ✅ Terminar lo que se empezó
- ✅ No agregar funcionalidades no solicitadas
- ✅ Mejorar después, no durante el desarrollo inicial

## 🔍 PROCESO OBLIGATORIO ANTES DE CADA MÓDULO

### PASO 1: Verificación de Backend
```bash
# Comandos obligatorios a ejecutar:
php artisan route:list                    # Ver todas las rutas
php artisan route:list --name=nombre     # Ver rutas específicas
php artisan tinker --execute="..."       # Verificar modelos y datos
```

### PASO 2: Revisión de Archivos
- [ ] Leer `routes/web.php` - ¿Qué rutas web existen?
- [ ] Leer `routes/api.php` - ¿Qué APIs existen?  
- [ ] Revisar `app/Models/` - ¿Qué modelos están disponibles?
- [ ] Verificar `database/migrations/` - ¿Qué tablas existen?

### PASO 3: Mapeo de Funcionalidades
- [ ] Listar funcionalidades REALES disponibles
- [ ] Identificar funcionalidades FALTANTES
- [ ] Decidir: ¿usar placeholder o implementar?
- [ ] Documentar decisiones tomadas

## ❌ ERRORES A EVITAR

### Referencias a Rutas Inexistentes
```php
// ❌ MALO - Asumir que existe
route('productos.show', $id)
route('clientes.edit', $id)

// ✅ BUENO - Verificar primero
// 1. php artisan route:list --name=productos
// 2. Usar solo rutas que existen:
route('productos.index')  // Si existe
```

### Asumir Funcionalidades de Modelos
```php
// ❌ MALO - Asumir métodos
$producto->getStockInfo()

// ✅ BUENO - Verificar en el modelo
// 1. Leer app/Models/Producto.php
// 2. Usar solo métodos que existen
$producto->stock_actual  // Si el campo existe
```

### Complicar sin Necesidad
```javascript
// ❌ MALO - Framework complejo para algo simple
import { createApp } from 'vue'
import { createRouter } from 'vue-router'

// ✅ BUENO - Herramienta simple que funciona
// Alpine.js + Livewire (ya configurado)
// jQuery si es necesario (probado y confiable)
```

## ✅ FLUJO DE TRABAJO CORRECTO

### Para Cada Módulo:

1. **Leer este documento** 📋
2. **Verificar backend disponible** 🔍
3. **Mapear funcionalidades reales** 📝
4. **Implementar con lo que existe** 🛠️
5. **Documentar limitaciones** 📚
6. **Probar con datos reales** 🧪

### Preguntas Obligatorias:
- ¿Esta ruta existe? → Verificar con `route:list`
- ¿Este modelo tiene este método? → Revisar el archivo
- ¿Esta API funciona? → Probar en Postman/curl
- ¿Estos datos existen? → Verificar en base de datos

## 🎯 OBJETIVO FINAL

**Completar los 7 módulos usando SOLO lo que existe**:
- ✅ Módulo 1: Layouts ✅ 
- ✅ Módulo 2: Dashboard ✅
- 🔄 Módulo 3: POS Interface  
- 🔄 Módulo 4: Gestión de Inventario
- 🔄 Módulo 5: Proveedores y Compras  
- 🔄 Módulo 6: Reportes y Analytics
- 🔄 Módulo 7: Administración

## 🚨 RECORDATORIO FINAL

**ANTES DE ESCRIBIR CUALQUIER CÓDIGO:**
1. ¿He verificado qué existe en el backend?
2. ¿Estoy usando solo funcionalidades confirmadas?
3. ¿He consultado este documento?
4. ¿Mantengo la simplicidad?

---

**Si tienes dudas, pregunta. Si no existe, usa placeholder. Si existe, úsalo tal como está.**

*Última actualización: Septiembre 4, 2025*
