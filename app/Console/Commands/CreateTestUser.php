<?php

namespace App\Console\Commands;

use App\Models\Core\User;
use Illuminate\Console\Command;

class CreateTestUser extends Command
{
    protected $signature = 'user:create-test';
    protected $description = 'Create a test user for development';

    public function handle()
    {
        // Verificar si ya existe
        $existingUser = User::where('email', 'admin@test.com')->first();
        
        if ($existingUser) {
            $this->info('Usuario ya existe: ' . $existingUser->email);
            $this->info('Contraseña: 123456');
            return 0;
        }

        $user = User::create([
            'name' => 'Admin Test',
            'nombre' => 'Admin Test',
            'email' => 'admin@test.com',
            'password_hash' => bcrypt('123456'),
            'empresa_id' => 1,
            'activo' => true,
            'rol_principal' => 'owner'
        ]);

        $this->info('Usuario creado: ' . $user->email);
        $this->info('Nombre: ' . ($user->nombre ?? $user->name));
        $this->info('Contraseña: 123456');
        
        return 0;
    }
}

