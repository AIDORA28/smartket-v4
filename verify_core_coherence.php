<?php

use App\Models\Core\User;
use App\Models\Core\Empresa;
use App\Models\Core\Plan;
use App\Models\Core\Sucursal;
use App\Models\Core\Rubro;
use Illuminate\Support\Facades\DB;

echo "=== VERIFICACIÓN DE COHERENCIA MODELOS vs BD ===\n\n";

// 1. Verificar User
echo "1. MODELO USER:\n";
$userFillable = (new User())->getFillable();
echo "Fillable: " . implode(', ', $userFillable) . "\n";

$userColumns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'users' ORDER BY ordinal_position");
$userDbColumns = array_map(fn($col) => $col->column_name, $userColumns);
echo "BD Columns: " . implode(', ', $userDbColumns) . "\n";

$missing = array_diff($userFillable, $userDbColumns);
$extra = array_diff($userDbColumns, $userFillable);
if ($missing) echo "❌ Fillable sin columna BD: " . implode(', ', $missing) . "\n";
if ($extra) echo "⚠️  Columnas BD no en fillable: " . implode(', ', $extra) . "\n";
echo "✅ User: " . (empty($missing) ? "OK" : "PROBLEMAS") . "\n\n";

// 2. Verificar Empresa
echo "2. MODELO EMPRESA:\n";
$empresaFillable = (new Empresa())->getFillable();
echo "Fillable: " . implode(', ', $empresaFillable) . "\n";

$empresaColumns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'empresas' ORDER BY ordinal_position");
$empresaDbColumns = array_map(fn($col) => $col->column_name, $empresaColumns);
echo "BD Columns: " . implode(', ', $empresaDbColumns) . "\n";

$missing = array_diff($empresaFillable, $empresaDbColumns);
$extra = array_diff($empresaDbColumns, $empresaFillable);
if ($missing) echo "❌ Fillable sin columna BD: " . implode(', ', $missing) . "\n";
if ($extra) echo "⚠️  Columnas BD no en fillable: " . implode(', ', $extra) . "\n";
echo "✅ Empresa: " . (empty($missing) ? "OK" : "PROBLEMAS") . "\n\n";

// 3. Verificar Plan
echo "3. MODELO PLAN:\n";
$planFillable = (new Plan())->getFillable();
echo "Fillable: " . implode(', ', $planFillable) . "\n";

$planColumns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'planes' ORDER BY ordinal_position");
$planDbColumns = array_map(fn($col) => $col->column_name, $planColumns);
echo "BD Columns: " . implode(', ', $planDbColumns) . "\n";

$missing = array_diff($planFillable, $planDbColumns);
$extra = array_diff($planDbColumns, $planFillable);
if ($missing) echo "❌ Fillable sin columna BD: " . implode(', ', $missing) . "\n";
if ($extra) echo "⚠️  Columnas BD no en fillable: " . implode(', ', $extra) . "\n";
echo "✅ Plan: " . (empty($missing) ? "OK" : "PROBLEMAS") . "\n\n";

// 4. Verificar constantes de roles
echo "4. VERIFICACIÓN ROLES:\n";
$rolesBase = User::ROLES_BASE;
$rolesPremium = User::ROLES_PREMIUM;
$roleOwner = User::ROLE_OWNER;

echo "Roles Base: " . implode(', ', array_keys($rolesBase)) . "\n";
echo "Roles Premium: " . implode(', ', array_keys($rolesPremium)) . "\n";
echo "Role Owner: " . $roleOwner . "\n";

// Verificar users con roles inválidos
$invalidRoles = DB::select("
    SELECT DISTINCT rol_principal 
    FROM users 
    WHERE rol_principal NOT IN (?, ?, ?, ?, ?, ?, ?)
", [
    'owner', 'admin', 'vendedor', 'cajero', 'almacenero', 'subgerente', 'gerente'
]);

if ($invalidRoles) {
    echo "❌ Roles inválidos en BD: " . implode(', ', array_map(fn($r) => $r->rol_principal, $invalidRoles)) . "\n";
} else {
    echo "✅ Todos los roles en BD son válidos\n";
}

echo "\n=== RESUMEN ===\n";
echo "Verificación completada. Revisa los errores marcados con ❌\n";
