<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Core\User;
use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use App\Models\Core\UserEmpresaAcceso;
use App\Models\Core\UserSucursalAcceso;

class MultiTenantSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Configurando accesos multi-tenant...');

        // Obtener usuarios existentes
        $ownerDonJose = User::where('email', 'admin@donj.com')->first();
        $ownerEsperanza = User::where('email', 'admin@esperanza.com')->first();

        if (!$ownerDonJose || !$ownerEsperanza) {
            $this->command->warn('No se encontraron los usuarios base. Ejecuta DatabaseSeeder primero.');
            return;
        }

        // Configurar accesos para owner de Don José
        if ($ownerDonJose) {
            // Acceso a su empresa principal
            UserEmpresaAcceso::updateOrCreate([
                'user_id' => $ownerDonJose->id,
                'empresa_id' => $ownerDonJose->empresa_id
            ], [
                'rol_en_empresa' => 'owner',
                'activo' => true
            ]);

            // Acceso a su sucursal principal
            UserSucursalAcceso::updateOrCreate([
                'user_id' => $ownerDonJose->id,
                'sucursal_id' => $ownerDonJose->sucursal_id
            ], [
                'rol_en_sucursal' => 'admin',
                'activo' => true
            ]);

            // Establecer contexto activo
            $ownerDonJose->update([
                'empresa_activa_id' => $ownerDonJose->empresa_id,
                'sucursal_activa_id' => $ownerDonJose->sucursal_id,
                'ultimo_cambio_contexto' => now()
            ]);
        }

        // Configurar accesos para owner de La Esperanza
        if ($ownerEsperanza) {
            // Acceso a su empresa principal
            UserEmpresaAcceso::updateOrCreate([
                'user_id' => $ownerEsperanza->id,
                'empresa_id' => $ownerEsperanza->empresa_id
            ], [
                'rol_en_empresa' => 'owner',
                'activo' => true
            ]);

            // Acceso a su sucursal principal
            UserSucursalAcceso::updateOrCreate([
                'user_id' => $ownerEsperanza->id,
                'sucursal_id' => $ownerEsperanza->sucursal_id
            ], [
                'rol_en_sucursal' => 'admin',
                'activo' => true
            ]);

            // Establecer contexto activo
            $ownerEsperanza->update([
                'empresa_activa_id' => $ownerEsperanza->empresa_id,
                'sucursal_activa_id' => $ownerEsperanza->sucursal_id,
                'ultimo_cambio_contexto' => now()
            ]);
        }

        // CREAR ESCENARIO MULTI-TENANT DE EJEMPLO
        $this->createMultiTenantScenario($ownerDonJose);

        $this->command->info('✅ Accesos multi-tenant configurados exitosamente');
    }

    /**
     * Crear un escenario multi-tenant de ejemplo
     */
    private function createMultiTenantScenario(User $owner)
    {
        if (!$owner) return;

        $empresa = $owner->empresa;
        
        // Crear una segunda sucursal para demostrar multi-sucursal
        $sucursal2 = Sucursal::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Sucursal Norte',
            'codigo_interno' => 'NORTE',
            'direccion' => 'Av. Túpac Amaru 456, Los Olivos',
            'activa' => true
        ]);

        // Crear usuarios adicionales para demostrar roles
        $gerente = User::create([
            'name' => 'Carlos Gerente',
            'email' => 'gerente@donj.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal2->id,
            'rol_principal' => 'gerente',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        $subgerente = User::create([
            'name' => 'Ana Subgerente',
            'email' => 'subgerente@donj.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal2->id,
            'rol_principal' => 'subgerente',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        $vendedor = User::create([
            'name' => 'Miguel Vendedor',
            'email' => 'vendedor@donj.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $empresa->id,
            'sucursal_id' => $sucursal2->id,
            'rol_principal' => 'vendedor',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        // Configurar accesos multi-tenant para el gerente (puede acceder a toda la empresa)
        UserEmpresaAcceso::create([
            'user_id' => $gerente->id,
            'empresa_id' => $empresa->id,
            'rol_en_empresa' => 'gerente',
            'activo' => true
        ]);

        // El gerente puede acceder a todas las sucursales
        foreach ($empresa->sucursales as $sucursal) {
            UserSucursalAcceso::create([
                'user_id' => $gerente->id,
                'sucursal_id' => $sucursal->id,
                'rol_en_sucursal' => 'admin',
                'activo' => true
            ]);
        }

        // Configurar contexto activo para gerente
        $gerente->update([
            'empresa_activa_id' => $empresa->id,
            'sucursal_activa_id' => $sucursal2->id,
            'ultimo_cambio_contexto' => now()
        ]);

        // Configurar accesos para subgerente (solo sucursales específicas)
        UserEmpresaAcceso::create([
            'user_id' => $subgerente->id,
            'empresa_id' => $empresa->id,
            'rol_en_empresa' => 'admin',
            'activo' => true
        ]);

        // Subgerente puede manejar múltiples sucursales
        UserSucursalAcceso::create([
            'user_id' => $subgerente->id,
            'sucursal_id' => $sucursal2->id,
            'rol_en_sucursal' => 'subgerente',
            'activo' => true
        ]);

        UserSucursalAcceso::create([
            'user_id' => $subgerente->id,
            'sucursal_id' => $owner->sucursal_id, // También puede acceder a la sucursal principal
            'rol_en_sucursal' => 'admin',
            'activo' => true
        ]);

        // Configurar contexto activo para subgerente
        $subgerente->update([
            'empresa_activa_id' => $empresa->id,
            'sucursal_activa_id' => $sucursal2->id,
            'ultimo_cambio_contexto' => now()
        ]);

        // Configurar accesos básicos para vendedor (solo su sucursal)
        UserEmpresaAcceso::create([
            'user_id' => $vendedor->id,
            'empresa_id' => $empresa->id,
            'rol_en_empresa' => 'admin',
            'activo' => true
        ]);

        UserSucursalAcceso::create([
            'user_id' => $vendedor->id,
            'sucursal_id' => $sucursal2->id,
            'rol_en_sucursal' => 'vendedor',
            'activo' => true
        ]);

        // Configurar contexto activo para vendedor
        $vendedor->update([
            'empresa_activa_id' => $empresa->id,
            'sucursal_activa_id' => $sucursal2->id,
            'ultimo_cambio_contexto' => now()
        ]);

        // Dar acceso al owner a la nueva sucursal también
        UserSucursalAcceso::create([
            'user_id' => $owner->id,
            'sucursal_id' => $sucursal2->id,
            'rol_en_sucursal' => 'admin',
            'activo' => true
        ]);

        $this->command->info('📊 Escenario multi-tenant creado:');
        $this->command->info("   - Owner: {$owner->email} (acceso total)");
        $this->command->info("   - Gerente: {$gerente->email} (toda la empresa)");
        $this->command->info("   - Subgerente: {$subgerente->email} (múltiples sucursales)");
        $this->command->info("   - Vendedor: {$vendedor->email} (una sucursal)");
        $this->command->info("   - Sucursales: Principal + Norte");
    }
}
