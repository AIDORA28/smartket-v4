# OPTIMIZACIÓN DE DESARROLLO LOCAL

## 🚀 Configuración Development vs Production

### 📊 PROBLEMA ACTUAL:
- Laragon (local) + Supabase (remoto) = Latencia alta
- Cada query SQL: ~200-500ms de latencia de red
- Login/Dashboard: 8-10 segundos

### ⚡ SOLUCIÓN 1: PostgreSQL Local para Development
```bash
# 1. Instalar PostgreSQL local en Laragon
# Descargar: https://www.postgresql.org/download/windows/

# 2. Configurar .env para desarrollo local
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smartket_dev
DB_USERNAME=postgres
DB_PASSWORD=password

# 3. Migrar esquema localmente
php artisan migrate:fresh --seed
```

### 🌍 SOLUCIÓN 2: Mantener Supabase Solo para Production
```bash
# .env.local (desarrollo)
DB_HOST=127.0.0.1

# .env.production (Vercel)
DB_HOST=db.mklfolbageroutbquwqx.supabase.co
```

### 📈 BENEFICIOS:
- Development: <50ms por query (20x más rápido)
- Production: Mantiene Supabase para escalabilidad
- Best of both worlds

### 🎯 RENDIMIENTO ESPERADO:
- Login: 8s → 0.5s (16x mejora)
- Dashboard: 5s → 0.2s (25x mejora)
- POS: Tiempo real sin latencia
