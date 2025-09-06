# 🏠 GUÍA: CONFIGURACIÓN LARAGON + POSTGRESQL PARA SMARTKET V4

## 📋 PREREQUISITOS
- ✅ Laragon instalado y funcionando
- ✅ PostgreSQL habilitado en Laragon
- ✅ Node.js 18+ (para PNPM)

---

## 🛠️ PASOS DE CONFIGURACIÓN

### **PASO 1: Configurar PostgreSQL en Laragon**

1. **Abrir Laragon** → Click derecho → `PostgreSQL` → `Enable`
2. **Verificar que PostgreSQL esté corriendo** → Icono verde en Laragon
3. **Acceder a pgAdmin** (opcional): `http://localhost/pgAdmin`

### **PASO 2: Crear Base de Datos Local**

```sql
-- Conectar a PostgreSQL (usuario: postgres, sin contraseña por defecto)
CREATE DATABASE smartket_v4_local;
CREATE USER smartket_admin WITH PASSWORD 'smartket2024';
GRANT ALL PRIVILEGES ON DATABASE smartket_v4_local TO smartket_admin;
```

### **PASO 3: Configurar Variables de Entorno**

```bash
# Copiar configuración local
cp .env.laragon .env

# O manualmente actualizar tu .env actual:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smartket_v4_local  
DB_USERNAME=postgres
DB_PASSWORD=
APP_URL=http://smartket-v4.test
```

### **PASO 4: Configurar Virtual Host en Laragon**

1. **Laragon** → `Apache` → `sites-enabled` → Crear `smartket-v4.test.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "D:/laragon/www/SmartKet-v4/public"
    ServerName smartket-v4.test
    ServerAlias www.smartket-v4.test
    
    <Directory "D:/laragon/www/SmartKet-v4/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    # PHP Configuration
    <FilesMatch \.php$>
        SetHandler "proxy:fcgi://127.0.0.1:9000"
    </FilesMatch>
</VirtualHost>
```

2. **Reiniciar Laragon** para aplicar configuración

### **PASO 5: Migrar y Seed Database**

```bash
# Instalar dependencias PHP
composer install

# Ejecutar migraciones
php artisan migrate

# Seed con datos de prueba (opcional)
php artisan db:seed

# Optimizar para desarrollo
php artisan config:cache
php artisan route:cache
```

### **PASO 6: Configurar Frontend (PNPM + Vite)**

```bash
# Instalar dependencias Node.js
pnpm install

# Iniciar servidor de desarrollo
pnpm run dev

# En otra terminal, iniciar Laravel
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 🚀 COMANDOS DE DESARROLLO DIARIO

### **Iniciar entorno completo:**
```bash
# Terminal 1: Frontend (Vite)
pnpm run dev

# Terminal 2: Backend (Laravel)
php artisan serve

# Terminal 3: Queue worker (opcional)
php artisan queue:work
```

### **Build para producción:**
```bash
# Build optimizado
pnpm run build

# Verificar build
php artisan optimize
```

---

## ✅ VERIFICAR INSTALACIÓN

### **Checklist de funcionamiento:**
- [ ] Laragon con PostgreSQL verde
- [ ] Base de datos `smartket_v4_local` creada
- [ ] `http://smartket-v4.test` o `http://localhost:8000` funciona
- [ ] Dashboard React carga correctamente
- [ ] `pnpm run dev` sin errores
- [ ] Build `pnpm run build` exitoso

---

## 🐛 TROUBLESHOOTING COMÚN

### **Error: "could not connect to database"**
```bash
# Verificar PostgreSQL en Laragon
# Reiniciar Laragon
# Verificar puerto 5432 libre: netstat -an | findstr 5432
```

### **Error: "Vite dev server not found"**
```bash
# Reinstalar dependencias
rm -rf node_modules
pnpm install
```

### **Error: "Class not found"**
```bash
# Limpiar cache Laravel
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

---

## 📊 WORKFLOW DESARROLLO → PRODUCCIÓN

1. **Desarrollo Local**: Laragon + PostgreSQL local
2. **Testing**: `pnpm run build` + validación
3. **Commit**: `git commit -m "feature: nueva funcionalidad"`
4. **Deploy**: Push a repo → Vercel automático
5. **Production**: Supabase PostgreSQL + Vercel

---

*Configuración creada: 6 de septiembre de 2025*  
*SmartKet v4 - Laravel 12 + React 18 + Inertia.js*
