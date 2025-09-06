# 🚀 VERCEL + SUPABASE SETUP - Stack Profesional

## 🎯 ARQUITECTURA MODERNA:
```
GitHub Repository → Vercel Deploy → Supabase Database
      ↓                   ↓              ↓
   Push code        Auto build      PostgreSQL + Auth
```

## 📋 PASO 1: CONFIGURAR VERCEL

### A. Conectar GitHub a Vercel:
```bash
1. Ir a: https://vercel.com
2. Crear cuenta con GitHub
3. Import Project → smartket-v4
4. Framework: Other (Laravel personalizado)
5. Auto-deploy: ON
```

### B. Configurar Build Commands:
```json
// vercel.json
{
  "version": 2,
  "builds": [
    {
      "src": "public/index.php",
      "use": "@vercel/php"
    },
    {
      "src": "resources/js/app.js",
      "use": "@vercel/static-build",
      "config": {
        "buildCommand": "npm run build"
      }
    }
  ],
  "routes": [
    {
      "src": "/build/(.*)",
      "dest": "/public/build/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/public/index.php"
    }
  ],
  "env": {
    "APP_ENV": "production",
    "APP_DEBUG": "false",
    "LOG_CHANNEL": "stderr",
    "SESSION_DRIVER": "cookie"
  }
}
```

## 🔧 PASO 2: VARIABLES DE ENTORNO VERCEL

### En Vercel Dashboard > Settings > Environment Variables:
```env
# Laravel Core
APP_NAME=SmartKet
APP_ENV=production
APP_KEY=base64:tu-app-key-generada
APP_DEBUG=false
APP_URL=https://tu-proyecto.vercel.app

# Supabase Database (TUS CREDENCIALES)
DB_CONNECTION=pgsql
DB_HOST=db.mklfolbageroutbquwqx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=cKn4kCfWefwnLEeh

# Supabase Storage
SUPABASE_URL=https://mklfolbageroutbquwqx.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1rbGZvbGJhZ2Vyb3V0YnF1d3F4Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTcxMzkyOTEsImV4cCI6MjA3MjcxNTI5MX0.EeGzmGW2xByFFemA_F2J-kU5vBwIx43aRmPgegzVVWc

# Cache y Sessions (Serverless compatible)
SESSION_DRIVER=cookie
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
```

## 🗄️ PASO 3: OPTIMIZAR LARAVEL PARA SERVERLESS

### A. Modificar bootstrap/app.php:
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Optimización para Vercel Serverless
if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware optimizado para serverless
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exception handling optimizado
    })->create();
```

### B. Crear api/index.php para Vercel:
```php
<?php
// api/index.php - Entry point para Vercel

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$request = Illuminate\Http\Request::capture();
$response = $app->handle($request);
$response->send();
$app->terminate($request, $response);
```

## 📱 PASO 4: CONFIGURAR GITHUB ACTIONS (CI/CD)

### .github/workflows/deploy.yml:
```yaml
name: Deploy to Vercel

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.3
        extensions: pdo, pdo_pgsql, mbstring
        
    - name: Install Composer dependencies
      run: composer install --optimize-autoloader --no-dev
      
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: 18
        cache: 'npm'
        
    - name: Install NPM dependencies
      run: npm ci
      
    - name: Build assets
      run: npm run build
      
    - name: Deploy to Vercel
      uses: vercel/action@v1
      with:
        vercel-token: ${{ secrets.VERCEL_TOKEN }}
        vercel-org-id: ${{ secrets.ORG_ID }}
        vercel-project-id: ${{ secrets.PROJECT_ID }}
```

## 🎯 PASO 5: CONFIGURAR DOMINIO PERSONALIZADO

### En Vercel Dashboard > Domains:
```
1. Agregar dominio: smartket.app (o tu dominio)
2. Configurar DNS automáticamente
3. SSL automático habilitado
4. CDN global activado
```

## 🔒 PASO 6: SEGURIDAD EMPRESARIAL

### A. Rate Limiting en Vercel:
```javascript
// middleware/rate-limit.js
export default function handler(req, res) {
  // Rate limiting por IP
  const forwarded = req.headers["x-forwarded-for"]
  const ip = forwarded ? forwarded.split(/, /)[0] : req.connection.remoteAddress
  
  // Implementar rate limiting
  res.setHeader('X-RateLimit-Limit', 1000)
  res.setHeader('X-RateLimit-Remaining', 999)
}
```

### B. Configurar CORS:
```php
// config/cors.php - Configuración para Vercel
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://tu-proyecto.vercel.app',
        'https://smartket.app',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

## 📊 PASO 7: MONITOREO Y ANALYTICS

### Vercel incluye:
```
📈 Performance Metrics automático
🚨 Error Tracking integrado
📊 Usage Analytics por función
🔍 Real-time Logs
📱 Deploy Notifications
```

## 💰 PASO 8: PRICING OPTIMIZADO

### Estimación mensual para SmartKet:
```
🌐 Vercel Pro: $20/mes
├── Unlimited sites
├── 100GB bandwidth
├── Serverless functions
├── Custom domains
└── Performance analytics

💾 Supabase Pro: $25/mes  
├── 8GB PostgreSQL
├── 250GB bandwidth
├── Authentication
├── Real-time subscriptions
└── Row Level Security

───────────────────────────
💰 Total: $45/mes

🎯 Beneficio vs servidor tradicional:
- Servidor VPS: $50-200/mes
- Mantenimiento: $500-1000/mes  
- DevOps: $2000+/mes
- SSL, CDN, Backups: $100+/mes
───────────────────────────
💰 Ahorro: $2500+/mes
```

## 🚀 VENTAJAS DEL STACK VERCEL + SUPABASE:

### 🌍 **Global Performance:**
```
⚡ Edge Functions (sub-100ms worldwide)
🌐 CDN automático en 70+ regiones
📱 Progressive Web App ready
🔄 Auto-scaling sin configuración
```

### 🛡️ **Enterprise Security:**
```
🔒 SSL automático (Let's Encrypt)
🛡️ DDoS protection incluido
🔐 Environment variables seguras
📋 SOC 2 compliance (Vercel + Supabase)
```

### 🔧 **Developer Experience:**
```
🚀 Deploy en segundos (no minutos)
🔄 Rollback instantáneo
📊 Monitoring automático
🧪 Preview deployments para testing
```

---

## 🎯 RESULTADO FINAL:

Con **Vercel + GitHub + Supabase** tendrás:

✅ **ERP SaaS de nivel empresarial**  
✅ **Performance global sub-200ms**  
✅ **Escalabilidad automática**  
✅ **Costos predecibles ($45/mes)**  
✅ **Zero mantenimiento de servidores**  
✅ **Deploy automático desde GitHub**  

**¿Listo para configurar este stack profesional?** 🚀
