# 🚀 SETUP SMARTKET V4 - LARAGON + POSTGRESQL
# Ejecutar en PowerShell desde la raíz del proyecto

Write-Host "🏠 Configurando SmartKet v4 para desarrollo local..." -ForegroundColor Cyan

# Verificar prerequisitos
Write-Host "📋 Verificando prerequisitos..." -ForegroundColor Yellow

# Verificar Composer
if (Get-Command composer -ErrorAction SilentlyContinue) {
    Write-Host "   ✅ Composer encontrado" -ForegroundColor Green
} else {
    Write-Host "   ❌ Composer no encontrado - Instálalo desde https://getcomposer.org" -ForegroundColor Red
    exit 1
}

# Verificar PNPM
if (Get-Command pnpm -ErrorAction SilentlyContinue) {
    Write-Host "   ✅ PNPM encontrado" -ForegroundColor Green
} else {
    Write-Host "   ❌ PNPM no encontrado - Instalando..." -ForegroundColor Yellow
    npm install -g pnpm
}

# Verificar PHP
if (Get-Command php -ErrorAction SilentlyContinue) {
    $phpVersion = php -v
    Write-Host "   ✅ PHP encontrado: $($phpVersion.Split([Environment]::NewLine)[0])" -ForegroundColor Green
} else {
    Write-Host "   ❌ PHP no encontrado - Asegúrate de que Laragon esté en PATH" -ForegroundColor Red
    exit 1
}

# Configurar entorno
Write-Host "`n📝 Configurando variables de entorno..." -ForegroundColor Yellow

if (Test-Path ".env.laragon") {
    $backup = Read-Host "¿Crear backup del .env actual? (y/n)"
    if ($backup -eq "y") {
        $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
        Copy-Item ".env" ".env.backup.$timestamp"
        Write-Host "   ✅ Backup creado: .env.backup.$timestamp" -ForegroundColor Green
    }
    Copy-Item ".env.laragon" ".env"
    Write-Host "   ✅ Configuración local aplicada" -ForegroundColor Green
} else {
    Write-Host "   ❌ Archivo .env.laragon no encontrado" -ForegroundColor Red
    exit 1
}

# Instalar dependencias
Write-Host "`n📦 Instalando dependencias..." -ForegroundColor Yellow

Write-Host "   Instalando dependencias PHP..." -ForegroundColor Cyan
composer install --no-interaction --optimize-autoloader
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✅ Dependencias PHP instaladas" -ForegroundColor Green
} else {
    Write-Host "   ❌ Error instalando dependencias PHP" -ForegroundColor Red
    exit 1
}

Write-Host "   Instalando dependencias Node.js..." -ForegroundColor Cyan
pnpm install
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✅ Dependencias Node.js instaladas" -ForegroundColor Green
} else {
    Write-Host "   ❌ Error instalando dependencias Node.js" -ForegroundColor Red
    exit 1
}

# Verificar base de datos
Write-Host "`n🐘 Verificando PostgreSQL..." -ForegroundColor Yellow
try {
    php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>$null | Out-Null
    Write-Host "   ✅ Conexión a PostgreSQL exitosa" -ForegroundColor Green
    
    $migrate = Read-Host "¿Ejecutar migraciones? (y/n)"
    if ($migrate -eq "y") {
        php artisan migrate
        if ($LASTEXITCODE -eq 0) {
            Write-Host "   ✅ Migraciones ejecutadas" -ForegroundColor Green
        }
    }
} catch {
    Write-Host "   ⚠️  No se pudo conectar a PostgreSQL" -ForegroundColor Yellow
    Write-Host "   Asegúrate de que PostgreSQL esté corriendo en Laragon" -ForegroundColor Yellow
}

# Build inicial
Write-Host "`n🏗️  Construyendo assets..." -ForegroundColor Yellow
pnpm run build
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✅ Build completado" -ForegroundColor Green
} else {
    Write-Host "   ❌ Error en build" -ForegroundColor Red
}

# Limpiar cache
Write-Host "`n🧹 Limpiando cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear
Write-Host "   ✅ Cache limpiado" -ForegroundColor Green

# Mensaje final
Write-Host "`n🎉 ¡CONFIGURACIÓN COMPLETADA!" -ForegroundColor Green
Write-Host "`n📋 COMANDOS PARA DESARROLLO:" -ForegroundColor Cyan
Write-Host "   Terminal 1: pnpm run dev" -ForegroundColor White
Write-Host "   Terminal 2: php artisan serve" -ForegroundColor White
Write-Host "`n🌐 URL: http://localhost:8000" -ForegroundColor Cyan
Write-Host "`n🚀 ¡A desarrollar!" -ForegroundColor Green
