<?php
/**
 * Script de diagnóstico - Verificación de usuarios y login
 * Uso: php scripts/database/user_check.php
 */

require __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Empresa;
use App\Models\Plan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

function checkUsers()
{
    echo "\n=== VERIFICACIÓN DE USUARIOS ===\n";
    
    // 1. Contar usuarios
    $totalUsers = User::count();
    echo "👥 Total usuarios: {$totalUsers}\n";
    
    if ($totalUsers > 0) {
        echo "\n📋 Lista de usuarios:\n";
        User::with('empresa')->take(10)->get()->each(function($user) {
            $empresa = $user->empresa->nombre ?? 'Sin empresa';
            $activo = $user->activo ? '✅' : '❌';
            echo "  - ID:{$user->id} | {$user->email} | {$user->name} | {$empresa} | {$activo}\n";
        });
        
        if ($totalUsers > 10) {
            echo "  ... y " . ($totalUsers - 10) . " usuarios más\n";
        }
    }
    
    // 2. Verificar estructura de autenticación
    echo "\n⚙️ Configuración de Auth:\n";
    echo "  - Guard: " . config('auth.defaults.guard') . "\n";
    echo "  - Provider: " . config('auth.guards.web.provider') . "\n";
    echo "  - Model: " . config('auth.providers.users.model') . "\n";
}

function checkPlans()
{
    echo "\n=== VERIFICACIÓN DE PLANES ===\n";
    
    $totalPlanes = Plan::count();
    echo "📊 Total planes: {$totalPlanes}\n";
    
    if ($totalPlanes > 0) {
        echo "\n💼 Lista de planes:\n";
        Plan::all()->each(function($plan) {
            $visible = $plan->es_visible ? '👁️' : '🚫';
            $activo = $plan->activo ? '✅' : '❌';
            echo "  - {$plan->nombre} | \${$plan->precio_mensual} | {$visible} {$activo}\n";
        });
    }
}

function checkEmpresas()
{
    echo "\n=== VERIFICACIÓN DE EMPRESAS ===\n";
    
    $totalEmpresas = Empresa::count();
    echo "🏢 Total empresas: {$totalEmpresas}\n";
    
    if ($totalEmpresas > 0) {
        echo "\n🏪 Lista de empresas:\n";
        Empresa::with('plan')->take(5)->get()->each(function($empresa) {
            $plan = $empresa->plan->nombre ?? 'Sin plan';
            $activa = $empresa->activa ? '✅' : '❌';
            echo "  - {$empresa->nombre} | Plan: {$plan} | {$activa}\n";
        });
    }
}

// Main execution
echo "=== DIAGNÓSTICO DE USUARIOS - SMARTKET V4 ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";

checkUsers();
checkPlans();
checkEmpresas();

echo "\n✅ Diagnóstico completado.\n";
