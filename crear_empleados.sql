-- SCRIPT PARA CREAR USUARIOS EMPLEADOS DE PRUEBA
-- Ejecutar este script en la base de datos PostgreSQL

-- Primero verificar usuarios existentes
SELECT name, email, rol_principal, empresa_id FROM users WHERE empresa_id = 1;

-- Crear empleados solo si no existen
INSERT INTO users (empresa_id, sucursal_id, name, email, password_hash, rol_principal, activo, email_verified_at, created_at, updated_at)
SELECT 1, 1, 'Ana Martínez', 'cajero@donj.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero', true, NOW(), NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'cajero@donj.com');

INSERT INTO users (empresa_id, sucursal_id, name, email, password_hash, rol_principal, activo, email_verified_at, created_at, updated_at)
SELECT 1, 1, 'Carlos Ruiz', 'vendedor@donj.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor', true, NOW(), NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'vendedor@donj.com');

INSERT INTO users (empresa_id, sucursal_id, name, email, password_hash, rol_principal, activo, email_verified_at, created_at, updated_at)
SELECT 1, 1, 'Rosa García', 'almacenero@donj.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'almacenero', true, NOW(), NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'almacenero@donj.com');

INSERT INTO users (empresa_id, sucursal_id, name, email, password_hash, rol_principal, activo, email_verified_at, created_at, updated_at)
SELECT 1, 1, 'Miguel Torres', 'empleado.admin@donj.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', true, NOW(), NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'empleado.admin@donj.com');

-- Verificar que se crearon correctamente
SELECT name, email, rol_principal FROM users WHERE empresa_id = 1 ORDER BY rol_principal;
