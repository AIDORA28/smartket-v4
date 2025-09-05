<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Empresa;

$empresa = Empresa::first();

if (!$empresa) {
    echo "No hay empresas en la base de datos\n";
    exit;
}

if (!User::where('email', 'admin@test.com')->exists()) {
    $user = User::create([
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'empresa_id' => $empresa->id
    ]);
    echo "Usuario creado: admin@test.com / password\n";
} else {
    echo "Usuario ya existe: admin@test.com / password\n";
}

echo "Empresa ID: " . $empresa->id . "\n";
echo "Total usuarios: " . User::count() . "\n";
