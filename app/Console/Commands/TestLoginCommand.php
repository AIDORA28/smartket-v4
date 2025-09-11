<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Core\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestLoginCommand extends Command
{
    protected $signature = 'test:login';
    protected $description = 'Test user authentication';

    public function handle()
    {
        $this->info('=== PRUEBA DE AUTENTICACIÓN ===');

        // Credenciales de prueba
        $email = 'admin@donj.com';
        $password = 'password123';

        $this->info("Intentando autenticar:");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");

        // Buscar usuario
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error('Usuario no encontrado');
            return;
        }

        $this->info("Usuario encontrado:");
        $this->info("ID: {$user->id}");
        $this->info("Nombre: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Activo: " . ($user->activo ? 'SI' : 'NO'));
        $this->info("Password Hash: {$user->password_hash}");

        // Verificar password hash directamente
        $hashCheck = Hash::check($password, $user->password_hash);
        $this->info("Verificación directa del hash: " . ($hashCheck ? '✅ Correcto' : '❌ Incorrecto'));

        // Verificar el accessor
        $this->info("Accessor getPasswordAttribute: " . (!empty($user->password) ? '✅ Funciona' : '❌ No funciona'));

        // Probar Auth::attempt
        $this->info('=== PRUEBA CON AUTH::ATTEMPT ===');

        $credentials = [
            'email' => $email,
            'password' => $password,
            'activo' => true
        ];

        $authResult = Auth::attempt($credentials);
        $this->info("Auth::attempt resultado: " . ($authResult ? '✅ Éxito' : '❌ Falló'));

        if ($authResult) {
            $authenticatedUser = Auth::user();
            $this->info("Usuario autenticado: {$authenticatedUser->name} ({$authenticatedUser->email})");
            Auth::logout();
        } else {
            $this->info("Probando sin condición 'activo'...");
            $simpleCredentials = [
                'email' => $email,
                'password' => $password
            ];
            
            $simpleAuthResult = Auth::attempt($simpleCredentials);
            $this->info("Auth::attempt simple: " . ($simpleAuthResult ? '✅ Éxito' : '❌ Falló'));
            
            if ($simpleAuthResult) {
                $authenticatedUser = Auth::user();
                $this->info("Usuario autenticado: {$authenticatedUser->name} ({$authenticatedUser->email})");
                Auth::logout();
            }
        }

        return 0;
    }
}

