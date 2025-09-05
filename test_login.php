<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Verificar usuario
echo "=== VERIFICANDO DATOS DE PRUEBA ===" . PHP_EOL;

$user = User::where('email', 'admin@donj.com')->first();

if (!$user) {
    echo "Creando usuario de prueba..." . PHP_EOL;
    $user = User::create([
        'name' => 'Admin Don José',
        'email' => 'admin@donj.com', 
        'password' => Hash::make('password123'),
        'empresa_id' => 1,
    ]);
    echo "Usuario creado!" . PHP_EOL;
} else {
    echo "Usuario existente: {$user->email}" . PHP_EOL;
}

echo "User ID: {$user->id}" . PHP_EOL;
echo "Empresa ID: {$user->empresa_id}" . PHP_EOL;

// Verificar empresa
$empresa = Empresa::find(1);
if ($empresa) {
    echo "Empresa: {$empresa->nombre}" . PHP_EOL;
    echo "Empresa activa: " . ($empresa->activa ? 'Sí' : 'No') . PHP_EOL;
} else {
    echo "No existe empresa ID 1" . PHP_EOL;
}

// Verificar productos
$productos = \App\Models\Producto::where('empresa_id', 1)->count();
echo "Productos para empresa 1: {$productos}" . PHP_EOL;

echo PHP_EOL;
echo "=== CREDENCIALES PARA LOGIN ===" . PHP_EOL;
echo "Email: admin@donj.com" . PHP_EOL;
echo "Password: password123" . PHP_EOL;
