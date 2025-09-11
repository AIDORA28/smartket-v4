# ⚙️ CONFIGURACIÓN DESARROLLO FRONTEND - Core Module

## 🚀 **QUICK START**

### **1. Setup Inicial**
```bash
# Instalar dependencias
cd d:\VS Code\SmartKet-v4
pnpm install

# Verificar compilación actual
pnpm run build

# Modo desarrollo
pnpm run dev
```

### **2. Verificar Backend**
```bash
# Verificar servidor Laravel
php artisan serve

# Verificar rutas Core
php artisan route:list --name=core

# Verificar base datos
php artisan migrate:status
```

---

## 🎯 **DESARROLLO LOCAL**

### **Variables de Entorno**
```bash
# .env (verificar estas configuraciones)
APP_URL=http://localhost:8000
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smartket_v4_dev
```

### **Commands Útiles**
```bash
# Frontend development
pnpm run dev                 # Hot reload Vite
pnpm run build              # Build producción
pnpm run type-check         # Verificar TypeScript

# Backend commands
php artisan serve           # Laravel server
php artisan migrate:fresh --seed  # Reset DB
php artisan route:cache     # Cache rutas
```

---

## 📁 **ESTRUCTURA PROYECTO**

### **Frontend Core Files**
```
resources/js/
├── Pages/Core/                    ← TRABAJAR AQUÍ
│   ├── CompanyManagement/
│   │   ├── Settings/Index.tsx     ← IMPLEMENTAR
│   │   ├── Analytics/Index.tsx    ← IMPLEMENTAR
│   │   └── Branding/Index.tsx     ← IMPLEMENTAR
│   ├── BranchManagement/
│   │   └── Index.tsx              ← IMPLEMENTAR
│   └── UserManagement/
│       └── Index.tsx              ← ✅ YA HECHO
├── Components/Core/               ← COMPONENTES REUTILIZABLES
│   ├── MultiTenant/               ← ✅ YA HECHO
│   ├── Company/                   ← CREAR
│   └── Branch/                    ← CREAR
├── Types/
│   └── core.ts                    ← ✅ INTERFACES LISTAS
└── Layouts/
    └── AuthenticatedLayout.tsx    ← ✅ NAVIGATION LISTA
```

### **Backend APIs**
```
app/Http/Controllers/Core/
├── CompanySettingsController.php      ← ✅ 16 rutas
├── CompanyAnalyticsController.php     ← ✅ 3 rutas  
├── BranchManagementController.php     ← ✅ 19 rutas
├── OrganizationBrandingController.php ← ✅ 8 rutas
└── UserManagementController.php       ← ✅ 12 rutas
```

---

## 🔧 **CONFIGURACIÓN VS CODE**

### **Extensions Recomendadas**
```json
{
  "recommendations": [
    "bradlc.vscode-tailwindcss",
    "ms-vscode.vscode-typescript-next",
    "esbenp.prettier-vscode",
    "formulahendry.auto-rename-tag",
    "ms-vscode.vscode-json"
  ]
}
```

### **Settings.json**
```json
{
  "typescript.preferences.importModuleSpecifier": "relative",
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "emmet.includeLanguages": {
    "typescript": "html",
    "typescriptreact": "html"
  }
}
```

---

## 🎨 **ESTÁNDARES CÓDIGO**

### **TypeScript**
```typescript
// Uso correcto de types
import { CompanySettingsIndexProps } from '@/Types/core';

export default function CompanySettings({ 
  empresa, 
  configuracion 
}: CompanySettingsIndexProps) {
  // Implementación...
}
```

### **Tailwind CSS**
```typescript
// Clases consistentes
const cardClasses = "bg-white overflow-hidden shadow-sm sm:rounded-lg p-6";
const buttonClasses = "bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded";
```

### **Inertia.js**
```typescript
import { Head, useForm, usePage, router } from '@inertiajs/react';
import { route } from 'ziggy-js';

// Forms
const { data, setData, post, processing, errors } = useForm({
  nombre: '',
});

// Navigation
router.get(route('core.company.settings'));
```

---

## 🧪 **TESTING DATOS**

### **Usuario de Prueba**
```
Email: admin@smartket.com
Password: password123
Rol: Owner (acceso completo Core)
```

### **Empresa de Prueba**
```
ID: 1
Nombre: "Tienda Don José"
RUC: "20123456789"
Plan: Premium (ID: 3)
Sucursales: 3 activas
```

### **Sucursales Disponibles**
```
1. Sucursal Principal (PRIN) - San Isidro
2. Sucursal Norte (NORTE) - Los Olivos  
3. Sucursal Sur (SUR) - Chorrillos
4. Sucursal Este (ESTE) - Ate (inactiva)
```

---

## 🔗 **RUTAS IMPORTANTES**

### **Frontend Routes**
```
/core/users                    ← ✅ User Management (hecho)
/core/company/settings         ← ⏳ Company Settings (pendiente)
/core/company/analytics        ← ⏳ Company Analytics (pendiente)
/core/company/branding         ← ⏳ Organization Branding (pendiente)
/core/branches                 ← ⏳ Branch Management (pendiente)
```

### **API Endpoints (todas funcionando)**
```
GET  /core/company/settings           ← Datos configuración
POST /core/company/settings           ← Guardar configuración
GET  /core/company/analytics          ← Datos analytics
GET  /core/company/branding           ← Datos branding
GET  /core/branches                   ← Listado sucursales
POST /core/branches                   ← Crear sucursal
PUT  /core/branches/{id}              ← Actualizar sucursal
```

---

## 🎯 **PRIORIDADES DESARROLLO**

### **ALTA PRIORIDAD (Semana 1)**
1. **Company Settings** - Dashboard configuración empresarial
2. **Branch Management** - CRUD sucursales completo

### **MEDIA PRIORIDAD (Semana 2)**  
3. **Company Analytics** - Dashboard métricas y KPIs
4. **Organization Branding** - Upload logos y personalización

---

## 🐛 **DEBUGGING**

### **Verificar APIs Funcionando**
```bash
# Listar rutas Core
php artisan route:list --name=core

# Verificar controladores
ls app/Http/Controllers/Core/

# Ver logs Laravel
tail -f storage/logs/laravel.log
```

### **Verificar Frontend Compilation**
```bash
# Ver errores TypeScript
pnpm run type-check

# Ver errores Vite
pnpm run dev

# Limpiar cache
rm -rf node_modules/.vite
pnpm run build
```

### **Common Issues & Solutions**

**Problema:** Error "route not found"
```bash
# Solución: Clear route cache
php artisan route:clear
php artisan route:cache
```

**Problema:** TypeScript errors
```bash
# Solución: Verificar imports
import { route } from 'ziggy-js';
import { CompanySettingsIndexProps } from '@/Types/core';
```

**Problema:** Tailwind classes not working
```bash
# Solución: Restart Vite
pnpm run dev
```

---

## 📦 **DEPENDENCIAS ADICIONALES**

### **Para Forms Avanzados**
```bash
pnpm add react-hook-form
```

### **Para Charts (elegir una)**
```bash
# Opción 1: Recharts (más React-friendly)
pnpm add recharts

# Opción 2: Chart.js (más potente)
pnpm add chart.js react-chartjs-2
```

### **Para File Upload**
```bash
pnpm add react-dropzone
```

### **Para Color Picker**
```bash
pnpm add react-color
```

---

## 📞 **CONTACTO & SOPORTE**

- **Backend APIs**: 100% functional, 107 routes working
- **Database**: PostgreSQL with 48 tables, test data loaded
- **Multi-tenant**: Company/Branch context switching working
- **Navigation**: Core module visible in sidebar
- **TypeScript**: All interfaces ready in `Types/core.ts`

**Ready to start frontend development! 🚀**
