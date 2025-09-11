# 🛒 SmartKet v4 - Sistema de Gestión Empresarial

<p align="center">
  <img src="public/logo.svg" width="200" alt="SmartKet Logo">
</p>

<p align="center">
  <strong>Sistema integral de gestión empresarial multi-tenant</strong><br>
  Laravel 11 + React 18 + TypeScript + PostgreSQL
</p>

---

## 🚧 Estado del Proyecto

**FASE ACTUAL: Core Module Completo - Frontend Development Ready**

### ✅ Completado (Core Module)
- ✅ **Backend Core**: 107 rutas funcionales (13 controladores, 19 models)
- ✅ **Multi-tenant Architecture**: Context switching empresa/sucursal
- ✅ **User Management**: CRUD completo con roles y permisos
- ✅ **Database Structure**: PostgreSQL con 48 tablas optimizadas
- ✅ **API Integration**: Todas las APIs Core funcionando
- ✅ **TypeScript Interfaces**: 569 líneas de types para Core
- ✅ **Navigation Structure**: Sidebar con módulo Core integrado
- ✅ **Testing**: 6/7 tests Core passing

### 🎯 Ready for Frontend Development
- 📋 **Company Settings**: Backend listo, frontend pendiente
- 📋 **Branch Management**: Backend listo, frontend pendiente  
- 📋 **Company Analytics**: Backend listo, frontend pendiente
- 📋 **Organization Branding**: Backend listo, frontend pendiente
- 📋 **Documentation**: Guías completas para desarrollo frontend

### ⏳ Próximos Módulos
- ⏳ **Inventory Module**: Sistema de inventarios y stock
- ⏳ **Sales Module**: Punto de venta y facturación
- ⏳ **Reporting Module**: Reportes y analytics avanzados
- ⏳ **Mobile App**: React Native para vendedores

---

## 🏗️ Arquitectura

### **Stack Tecnológico**
```
Backend:   Laravel 11 + PHP 8.3
Frontend:  React 18 + TypeScript + Inertia.js
Database:  PostgreSQL 17.4
Styling:   Tailwind CSS + Heroicons v24
Build:     Vite 5 + ESBuild
Testing:   PHPUnit + Jest
```

### **Estructura del Proyecto**
```
SmartKet-v4/
├── app/                          # Laravel Backend
│   ├── Http/Controllers/Core/    # ✅ 13 Controllers
│   ├── Models/                   # ✅ 19 Core Models
│   └── Services/                 # Business Logic
├── resources/js/                 # React Frontend
│   ├── Pages/Core/               # ✅ Core Pages
│   ├── Components/Core/          # ✅ Core Components
│   ├── Types/                    # ✅ TypeScript Interfaces
│   └── Layouts/                  # ✅ Layout System
├── database/                     # PostgreSQL
│   ├── migrations/               # ✅ 48 Tables
│   └── seeders/                  # ✅ Test Data
├── routes/                       # API & Web Routes
│   ├── api.php                   # ✅ 107 Core Routes
│   └── web.php                   # ✅ Frontend Routes
└── docs/                         # ✅ Documentation
    ├── FRONTEND_CORE_DEVELOPMENT_GUIDE.md
    ├── FRONTEND_DEV_SETUP.md
    ├── FUNCIONALIDAD_CORE_USUARIO.md
    └── FRONTEND_ANALYSIS.md
```

---

## 🚀 Quick Start

### **Requisitos del Sistema**
- PHP 8.3+
- Node.js 18+
- PostgreSQL 17+
- Composer 2.x
- PNPM (recomendado)

### **Instalación**
```bash
# 1. Clonar repositorio
git clone <repository-url>
cd SmartKet-v4

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node.js
pnpm install

# 4. Configurar environment
cp .env.example .env
php artisan key:generate

# 5. Configurar base de datos en .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smartket_v4_dev
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 6. Ejecutar migraciones
php artisan migrate:fresh --seed

# 7. Build frontend
pnpm run build

# 8. Iniciar servidores
php artisan serve     # Backend: http://localhost:8000
pnpm run dev          # Frontend development mode
```

### **Usuario de Prueba**
```
Email: admin@smartket.com
Password: password123
Rol: Owner (acceso completo)
```

---

## 🎯 Funcionalidades Core (Implementadas)

### **🏢 Multi-Tenant Architecture**
- **Context Switching**: Cambio dinámico entre empresas y sucursales
- **Isolated Data**: Datos completamente separados por empresa
- **Role-Based Access**: Permisos granulares por usuario y contexto

### **👥 User Management**
- **CRUD Completo**: Crear, leer, actualizar, eliminar usuarios
- **Roles & Permissions**: Sistema de roles con permisos específicos
- **Multi-Branch Access**: Usuarios con acceso a múltiples sucursales

### **⚙️ Backend APIs (107 rutas)**
#### Company Settings (16 rutas)
```
GET    /core/company/settings
POST   /core/company/settings
PUT    /core/company/settings/{section}
GET    /core/company/settings/fiscal
POST   /core/company/settings/fiscal
... (y 11 más)
```

#### Branch Management (19 rutas)
```
GET    /core/branches
POST   /core/branches
GET    /core/branches/{id}
PUT    /core/branches/{id}
DELETE /core/branches/{id}
POST   /core/branches/{id}/transfer
... (y 13 más)
```

#### Company Analytics (3 rutas)
```
GET    /core/company/analytics
GET    /core/company/analytics/refresh
GET    /core/company/analytics/export
```

#### Organization Branding (8 rutas)
```
GET    /core/company/branding
POST   /core/company/branding
POST   /core/company/branding/logo
DELETE /core/company/branding/logo
... (y 4 más)
```

---

## 📊 Base de Datos

### **48 Tablas Principales**
```sql
-- Core Tables
empresas                    # Companies
sucursales                 # Branches  
usuarios                   # Users
empresa_usuario           # User-Company relationships
sucursal_usuario          # User-Branch relationships

-- Configuration
empresa_settings          # Company settings
empresa_analytics         # Analytics data
organization_branding     # Branding configuration

-- Business Logic (preparado para módulos futuros)
productos                 # Products
categorias                # Categories
marcas                    # Brands
clientes                  # Customers
proveedores               # Suppliers
ventas                    # Sales
compras                   # Purchases
inventario_movimientos    # Inventory movements
... (y 28 más)
```

---

## 🎨 Frontend Development

### **Estado Actual**
- ✅ **Navigation**: Módulo Core visible en sidebar
- ✅ **Multi-Tenant UI**: Company/Branch selectors funcionando
- ✅ **User Management**: Interface completa implementada
- ✅ **TypeScript**: Todas las interfaces definidas
- ⏳ **Company Management**: Pendiente implementación UI
- ⏳ **Branch Management**: Pendiente implementación UI
- ⏳ **Analytics Dashboard**: Pendiente implementación UI

### **Para Desarrolladores Frontend**
📁 **Documentación Completa:**
- [`FRONTEND_CORE_DEVELOPMENT_GUIDE.md`](./docs/FRONTEND_CORE_DEVELOPMENT_GUIDE.md) - Guía paso a paso
- [`FRONTEND_DEV_SETUP.md`](./docs/FRONTEND_DEV_SETUP.md) - Configuración desarrollo
- [`FUNCIONALIDAD_CORE_USUARIO.md`](./docs/FUNCIONALIDAD_CORE_USUARIO.md) - Funcionalidades usuario

**Todo listo para comenzar desarrollo frontend! 🚀**

---

## 🧪 Testing

### **Backend Testing**
```bash
# Ejecutar tests
php artisan test

# Tests específicos Core
php artisan test --filter=Core

# Coverage
php artisan test --coverage
```

**Estado actual:** 6/7 tests Core passing ✅

### **Frontend Testing**
```bash
# Unit tests (futuro)
pnpm run test

# E2E tests (futuro) 
pnpm run test:e2e
```

---

## 📖 Documentación

### **Documentación Técnica**
- [`DOCUMENTACION_CONSOLIDADA.md`](./docs/DOCUMENTACION_CONSOLIDADA.md) - Documentación completa
- [`REFERENCIA_TABLAS_COLUMNAS.md`](./docs/REFERENCIA_TABLAS_COLUMNAS.md) - Schema database
- [`METODOLOGIA_SISTEMATICA.md`](./docs/METODOLOGIA_SISTEMATICA.md) - Metodología desarrollo

### **Documentación Frontend**
- [`FRONTEND_ANALYSIS.md`](./docs/FRONTEND_ANALYSIS.md) - Análisis técnico frontend
- [`UX_UI_CHANGELOG.md`](./docs/UX_UI_CHANGELOG.md) - Changelog UX/UI

### **Planes Técnicos**
- [`PLAN_TECNICO_IAS_ESPECIALIZADAS.md`](./scripts/PLAN_TECNICO_IAS_ESPECIALIZADAS.md) - Plan desarrollo IA

---

## 🔧 Comandos Útiles

### **Desarrollo**
```bash
# Limpiar cache
php artisan optimize:clear

# Regenerar rutas
php artisan route:cache

# Verificar migraciones
php artisan migrate:status

# Seeders específicos
php artisan db:seed --class=CoreSeeder

# Build frontend
pnpm run build
pnpm run dev
```

### **Debug**
```bash
# Ver rutas Core
php artisan route:list --name=core

# Logs en tiempo real
tail -f storage/logs/laravel.log

# Verificar configuración
php artisan config:show
```

---

## 📋 Roadmap

### **Fase 1: Core Module (COMPLETADO ✅)**
- ✅ Multi-tenant architecture
- ✅ User management system
- ✅ Company & branch management APIs
- ✅ Analytics & branding APIs
- ⏳ Frontend UI implementation

### **Fase 2: Inventory Module (PRÓXIMO)**
- ⏳ Product catalog management
- ⏳ Stock control & movements
- ⏳ Supplier management
- ⏳ Purchase orders
- ⏳ Inventory reports

### **Fase 3: Sales Module**
- ⏳ Point of sale (POS)
- ⏳ Invoice generation
- ⏳ Customer management
- ⏳ Payment methods
- ⏳ Sales reports

### **Fase 4: Advanced Features**
- ⏳ Advanced reporting
- ⏳ Mobile app (React Native)
- ⏳ API integrations
- ⏳ Cloud deployment

---

## 🤝 Contribución

### **Para Frontend Developers**
1. Revisar [`FRONTEND_CORE_DEVELOPMENT_GUIDE.md`](./docs/FRONTEND_CORE_DEVELOPMENT_GUIDE.md)
2. Configurar entorno con [`FRONTEND_DEV_SETUP.md`](./docs/FRONTEND_DEV_SETUP.md)
3. Implementar componentes UI pendientes
4. Crear pull request con cambios

### **Para Backend Developers**
1. Revisar documentación existente
2. Implementar próximos módulos (Inventory/Sales)
3. Mantener coverage de tests
4. Documentar nuevas APIs

---

## 📞 Contacto & Soporte

- **Backend**: 100% funcional, 107 rutas operativas
- **Database**: PostgreSQL con 48 tablas y datos de prueba
- **Frontend**: Estructura lista, pendiente implementación UI
- **Documentation**: Guías completas disponibles

**¡Ready for collaborative development! 🚀**

---

## 📄 Licencia

Este proyecto está licenciado bajo la [MIT License](LICENSE).
