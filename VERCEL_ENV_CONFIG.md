# 🔑 VARIABLES DE ENTORNO PARA VERCEL DEPLOYMENT

## 🚀 CONFIGURACIÓN COMPLETA VERCEL

### 📋 En la sección "Environment Variables" de Vercel, agregar:

```env
# App Configuration
APP_NAME=SmartKet v4
APP_ENV=production
APP_KEY=base64:qKpDGAUCCuVYXWypzltxDDoyXl2PgHZ1DkQPelrx44o=
APP_DEBUG=false
APP_URL=https://smartket-v4.vercel.app

# Database (Supabase) - YA CONFIGURADO
DB_CONNECTION=pgsql
DB_HOST=db.mklfolbageroutbquwqx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=cKn4kCfWefwnLEeh

# Supabase Configuration
SUPABASE_URL=https://mklfolbageroutbquwqx.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1rbGZvbGJhZ2Vyb3V0YnF1d3F4Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTcxMzkyOTEsImV4cCI6MjA3MjcxNTI5MX0.EeGzmGW2xByFFemA_F2J-kU5vBwIx43aRmPgegzVVWc

# Session & Cache
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
CACHE_DRIVER=array

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=error

# Broadcasting (Optional)
BROADCAST_DRIVER=log
QUEUE_CONNECTION=sync

# Mail (Optional)
MAIL_MAILER=log

# Optional optimizations
VITE_APP_NAME="SmartKet v4"
VITE_APP_URL=https://smartket-v4.vercel.app
```

---

## 🔧 CONFIGURACIONES ESPECÍFICAS PARA LARAVEL + REACT

### Build Command optimizado:
```bash
composer install --no-dev --optimize-autoloader && pnpm run build
```

### Output Directory:
```
public
```

### Root Directory:
```
./
```

---

## ⚡ OPTIMIZACIONES ADICIONALES

### En package.json, el build script debe ser:
```json
{
  "scripts": {
    "build": "vite build && vite build --ssr",
    "build:ssr": "vite build --ssr"
  }
}
```

### Crear vercel.json en la raíz:
```json
{
  "framework": "vite",
  "buildCommand": "composer install --no-dev --optimize-autoloader && pnpm run build",
  "outputDirectory": "public",
  "installCommand": "pnpm install",
  "devCommand": "pnpm run dev",
  "env": {
    "APP_ENV": "production",
    "APP_DEBUG": "false"
  },
  "functions": {
    "app/index.php": {
      "runtime": "vercel-php@0.6.0"
    }
  },
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/index.php"
    }
  ]
}
```

---

## 🎯 RESULTADO ESPERADO

Una vez deployado tendrás:
- ✅ URL: https://smartket-v4.vercel.app
- ✅ Performance: <1s de carga
- ✅ Auto-deployment en cada push
- ✅ HTTPS automático
- ✅ CDN global para assets React
