<?php

// Simple login test
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Crear usuario si no existe
$user = User::where('email', 'admin@donj.com')->first();

if (!$user) {
    echo "Creating test user...\n";
    $user = User::create([
        'name' => 'Admin Don José',
        'email' => 'admin@donj.com', 
        'password' => Hash::make('password123'),
        'empresa_id' => 1,
    ]);
    echo "User created!\n";
} else {
    echo "User already exists: {$user->email}\n";
}

echo "User ID: {$user->id}\n";
echo "Empresa ID: {$user->empresa_id}\n";
