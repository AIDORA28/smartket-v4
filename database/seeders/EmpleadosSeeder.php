<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Core\User;
use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use Illuminate\Support\Facades\Hash;

class EmpleadosSeeder extends Seeder
{
    /**
     * Crear empleados de prueba para la empresa Don José
     */
    public function run(): void
    {
        echo "👥 Creando empleados para Tienda Don José...\n\n";

        // Obtener la empresa Don José
        $empresa = Empresa::where('nombre', 'Tienda Don José')->first();
        $sucursal = Sucursal::where('empresa_id', $empresa->id)->first();

        if (!$empresa || !$sucursal) {
            echo "❌ No se encontró la empresa 'Tienda Don José'. Ejecuta primero DatabaseSeeder.\n";
            return;
        }

        // Crear empleados con diferentes roles
        $empleados = [
            [
                'name' => 'Ana Martínez',
                'email' => 'cajero@donj.com',
                'rol_principal' => 'cajero',
                'descripcion' => 'Cajera principal - turno mañana'
            ],
            [
                'name' => 'Carlos Ruiz',
                'email' => 'vendedor@donj.com',
                'rol_principal' => 'vendedor',
                'descripcion' => 'Vendedor especialista en atención al cliente'
            ],
            [
                'name' => 'Rosa García',
                'email' => 'almacenero@donj.com',
                'rol_principal' => 'almacenero',
                'descripcion' => 'Encargada de almacén e inventarios'
            ],
            [
                'name' => 'Miguel Torres',
                'email' => 'admin@empleado.donj.com',
                'rol_principal' => 'admin',
                'descripcion' => 'Administrador empleado - supervisor general'
            ],
            [
                'name' => 'Lucia Fernández',
                'email' => 'cajero2@donj.com',
                'rol_principal' => 'cajero',
                'descripcion' => 'Cajera - turno tarde'
            ],
            [
                'name' => 'Roberto Silva',
                'email' => 'vendedor2@donj.com',
                'rol_principal' => 'vendedor',
                'descripcion' => 'Vendedor - especialista en productos'
            ]
        ];

        foreach ($empleados as $empleadoData) {
            $empleado = User::create([
                'empresa_id' => $empresa->id,
                'sucursal_id' => $sucursal->id,
                'name' => $empleadoData['name'],
                'email' => $empleadoData['email'],
                'password_hash' => Hash::make('password123'),
                'rol_principal' => $empleadoData['rol_principal'],
                'activo' => 1,
                'email_verified_at' => now(),
            ]);

            echo "✅ {$empleadoData['name']} ({$empleadoData['rol_principal']}) - {$empleadoData['email']}\n";
        }

        echo "\n🎯 CREDENCIALES DE EMPLEADOS CREADAS:\n";
        echo "════════════════════════════════════════\n\n";
        
        echo "🏪 TIENDA DON JOSÉ - EMPLEADOS:\n\n";
        
        echo "👨‍💼 ADMINISTRADOR EMPLEADO:\n";
        echo "   📧 admin@empleado.donj.com\n";
        echo "   🔑 password123\n";
        echo "   🎭 Rol: admin (empleado)\n\n";
        
        echo "💰 CAJEROS:\n";
        echo "   📧 cajero@donj.com (Ana Martínez)\n";
        echo "   📧 cajero2@donj.com (Lucia Fernández)\n";
        echo "   🔑 password123\n";
        echo "   🎭 Rol: cajero\n\n";
        
        echo "🛍️ VENDEDORES:\n";
        echo "   📧 vendedor@donj.com (Carlos Ruiz)\n";
        echo "   📧 vendedor2@donj.com (Roberto Silva)\n";
        echo "   🔑 password123\n";
        echo "   🎭 Rol: vendedor\n\n";
        
        echo "📦 ALMACENERO:\n";
        echo "   📧 almacenero@donj.com (Rosa García)\n";
        echo "   🔑 password123\n";
        echo "   🎭 Rol: almacenero\n\n";
        
        echo "ℹ️  Todos los empleados usan la contraseña: password123\n";
        echo "🏢 Empresa: Tienda Don José (misma empresa que admin@donj.com)\n\n";
        
        echo "✨ Ahora puedes probar diferentes dashboards:\n";
        echo "   • admin@donj.com → OwnerDashboard (propietario)\n";
        echo "   • admin@empleado.donj.com → EmployeeDashboard (admin empleado)\n";
        echo "   • cajero@donj.com → EmployeeDashboard (cajero)\n";
        echo "   • vendedor@donj.com → EmployeeDashboard (vendedor)\n";
        echo "   • almacenero@donj.com → EmployeeDashboard (almacenero)\n\n";
    }
}
