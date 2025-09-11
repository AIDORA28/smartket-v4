<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

echo "📋 USUARIOS DEL SISTEMA SMARTKET\n";
echo "================================\n\n";

try {
    // Conectar sin boot completo
    $config = require 'config/database.php';
    $pdo = new PDO(
        'pgsql:host=' . env('DB_HOST', 'localhost') . ';dbname=' . env('DB_DATABASE', 'smartket'),
        env('DB_USERNAME', 'postgres'),
        env('DB_PASSWORD', '')
    );
    
    $stmt = $pdo->prepare("SELECT name, email, rol_principal, empresa_id FROM users ORDER BY empresa_id, rol_principal");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $currentEmpresa = null;
    foreach ($users as $user) {
        if ($currentEmpresa !== $user['empresa_id']) {
            $currentEmpresa = $user['empresa_id'];
            echo "\n🏢 EMPRESA ID: {$user['empresa_id']}\n";
            echo "════════════════════\n";
        }
        
        echo "👤 {$user['name']}\n";
        echo "   📧 Email: {$user['email']}\n";
        echo "   🎭 Rol: {$user['rol_principal']}\n";
        echo "   🔑 Password: password123\n";
        echo "   ─────────────────────────────\n";
    }
    
    echo "\nℹ️  Todas las cuentas usan la contraseña: password123\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
