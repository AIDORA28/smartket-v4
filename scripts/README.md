# Scripts Directory

Esta carpeta contiene scripts de diagnóstico y mantenimiento para SmartKet v4.

## 📁 Estructura

```
scripts/
├── database/           # Scripts de diagnóstico de base de datos
│   ├── table_info.php  # Información de estructura de tablas
│   └── user_check.php  # Verificación de usuarios y login
└── README.md          # Este archivo
```

## 📋 Uso

Los scripts son para **diagnóstico y debugging únicamente**, no para uso en producción.

### Ejecutar scripts de base de datos:
```bash
cd scripts/database
php table_info.php
php user_check.php
```

## ⚠️ Importante

- No ejecutar scripts en producción
- Solo para desarrollo y debugging
- Todos los scripts requieren que Laravel esté configurado correctamente
