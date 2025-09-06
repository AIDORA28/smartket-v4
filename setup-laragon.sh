#!/bin/bash
# 🚀 SCRIPT DE CONFIGURACIÓN AUTOMÁTICA LARAGON
# Ejecutar desde la raíz del proyecto SmartKet v4

echo "🏠 Configurando SmartKet v4 para desarrollo local con Laragon..."

# Verificar si estamos en Windows con PowerShell
if ! command -v powershell &> /dev/null; then
    echo "❌ Este script está diseñado para Windows con PowerShell"
    echo "   Ejecuta los comandos manualmente siguiendo CONFIGURACION_LARAGON.md"
    exit 1
fi

# Copiar configuración local
echo "📋 Copiando configuración de entorno local..."
if [ -f ".env.laragon" ]; then
    echo "   Hacer backup del .env actual? (y/n)"
    read -r backup
    if [ "$backup" = "y" ]; then
        cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
        echo "   ✅ Backup creado: .env.backup.$(date +%Y%m%d_%H%M%S)"
    fi
    cp .env.laragon .env
    echo "   ✅ Configuración local aplicada"
else
    echo "   ❌ Archivo .env.laragon no encontrado"
    exit 1
fi

# Instalar dependencias PHP
echo "📦 Instalando dependencias PHP..."
if composer install --no-interaction --optimize-autoloader; then
    echo "   ✅ Dependencias PHP instaladas"
else
    echo "   ❌ Error instalando dependencias PHP"
    exit 1
fi

# Instalar dependencias Node.js
echo "⚛️  Instalando dependencias Node.js con PNPM..."
if pnpm install; then
    echo "   ✅ Dependencias Node.js instaladas"
else
    echo "   ❌ Error instalando dependencias Node.js"
    echo "   Instala PNPM: npm install -g pnpm"
    exit 1
fi

# Verificar conexión a base de datos
echo "🐘 Verificando conexión a PostgreSQL..."
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Conexión exitosa';" 2>/dev/null
if [ $? -eq 0 ]; then
    echo "   ✅ Conexión a PostgreSQL exitosa"
else
    echo "   ⚠️  No se pudo conectar a PostgreSQL"
    echo "   Asegúrate de que PostgreSQL esté corriendo en Laragon"
    echo "   Y que la base de datos 'smartket_v4_local' exista"
fi

# Ejecutar migraciones (opcional)
echo "🔄 ¿Ejecutar migraciones de base de datos? (y/n)"
read -r migrate
if [ "$migrate" = "y" ]; then
    if php artisan migrate; then
        echo "   ✅ Migraciones ejecutadas exitosamente"
    else
        echo "   ❌ Error en migraciones"
    fi
fi

# Build inicial
echo "🏗️  Construyendo assets para desarrollo..."
if pnpm run build; then
    echo "   ✅ Build inicial completado"
else
    echo "   ❌ Error en build inicial"
fi

# Limpiar cache
echo "🧹 Limpiando cache de Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo "   ✅ Cache limpiado"

echo ""
echo "🎉 ¡CONFIGURACIÓN COMPLETADA!"
echo ""
echo "📋 PRÓXIMOS PASOS:"
echo "   1. Abrir 2 terminales:"
echo "      Terminal 1: pnpm run dev    (Frontend)"
echo "      Terminal 2: php artisan serve (Backend)"
echo ""
echo "   2. Abrir navegador: http://localhost:8000"
echo "      o http://smartket-v4.test (si configuraste virtual host)"
echo ""
echo "   3. Login con usuario de prueba o crear uno nuevo"
echo ""
echo "🚀 ¡A desarrollar!"
