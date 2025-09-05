<?php

use App\Models\Cliente;
use App\Services\TenantService;

// Obtener empresa actual
$tenantService = app(TenantService::class);
$empresa = $tenantService->getEmpresa();

echo "=== AGREGANDO CLIENTES ADICIONALES PARA MODULO 5 ===\n";
echo "Empresa: {$empresa->nombre}\n";

// Crear clientes adicionales para testing
$clientesAdicionales = [
    [
        'nombre' => 'María González Pérez',
        'tipo_documento' => 'DNI',
        'numero_documento' => '45678901',
        'email' => 'maria.gonzalez@email.com',
        'telefono' => '987654321',
        'direccion' => 'Av. Los Rosales 456, San Miguel',
        'fecha_nacimiento' => '1985-05-15',
        'genero' => 'F',
        'es_empresa' => false,
        'permite_credito' => true,
        'limite_credito' => 5000.00,
        'credito_usado' => 1200.00,
        'descuento_porcentaje' => 5.00,
        'activo' => true,
    ],
    [
        'nombre' => 'RESTAURANT EL BUEN SABOR E.I.R.L.',
        'tipo_documento' => 'RUC',
        'numero_documento' => '20123456789',
        'email' => 'ventas@buensabor.com.pe',
        'telefono' => '014567890',
        'direccion' => 'Jr. Los Pinos 789, Centro Histórico',
        'es_empresa' => true,
        'permite_credito' => true,
        'limite_credito' => 15000.00,
        'credito_usado' => 8500.00,
        'descuento_porcentaje' => 12.00,
        'activo' => true,
    ],
    [
        'nombre' => 'Carlos Mendoza Ruiz',
        'tipo_documento' => 'DNI',
        'numero_documento' => '78901234',
        'email' => 'carlos.mendoza@gmail.com',
        'telefono' => '956789012',
        'direccion' => 'Calle Las Flores 234, Miraflores',
        'fecha_nacimiento' => '1978-11-20',
        'genero' => 'M',
        'es_empresa' => false,
        'permite_credito' => false,
        'activo' => true,
    ],
    [
        'nombre' => 'Ana Lucia Torres Vega',
        'tipo_documento' => 'DNI',
        'numero_documento' => '56789012',
        'email' => 'ana.torres@hotmail.com',
        'telefono' => '923456789',
        'direccion' => 'Av. El Sol 567, San Borja',
        'fecha_nacimiento' => '1992-02-28',
        'genero' => 'F',
        'es_empresa' => false,
        'permite_credito' => true,
        'limite_credito' => 3000.00,
        'credito_usado' => 0.00,
        'descuento_porcentaje' => 0.00,
        'activo' => true,
    ],
    [
        'nombre' => 'BODEGA SANTA ROSA S.A.C.',
        'tipo_documento' => 'RUC',
        'numero_documento' => '20987654321',
        'email' => 'compras@santarosa.com.pe',
        'telefono' => '012345678',
        'direccion' => 'Av. La Marina 890, Pueblo Libre',
        'es_empresa' => true,
        'permite_credito' => true,
        'limite_credito' => 25000.00,
        'credito_usado' => 12750.00,
        'descuento_porcentaje' => 15.00,
        'activo' => true,
    ],
    [
        'nombre' => 'Luis Fernando Díaz',
        'tipo_documento' => 'DNI',
        'numero_documento' => '34567890',
        'telefono' => '912345678',
        'es_empresa' => false,
        'permite_credito' => false,
        'activo' => false, // Cliente inactivo para testing
    ]
];

$creados = 0;
foreach ($clientesAdicionales as $clienteData) {
    // Verificar si ya existe
    $existe = Cliente::where('empresa_id', $empresa->id)
        ->where('numero_documento', $clienteData['numero_documento'])
        ->exists();
        
    if (!$existe) {
        $clienteData['empresa_id'] = $empresa->id;
        Cliente::create($clienteData);
        $creados++;
        echo "✓ Cliente creado: {$clienteData['nombre']}\n";
    } else {
        echo "- Cliente ya existe: {$clienteData['nombre']}\n";
    }
}

echo "\n=== RESUMEN ===\n";
echo "Clientes creados: {$creados}\n";
echo "Total clientes: " . Cliente::where('empresa_id', $empresa->id)->count() . "\n";
echo "Clientes activos: " . Cliente::where('empresa_id', $empresa->id)->where('activo', true)->count() . "\n";
echo "Clientes con crédito: " . Cliente::where('empresa_id', $empresa->id)->where('permite_credito', true)->count() . "\n";
echo "Clientes empresas: " . Cliente::where('empresa_id', $empresa->id)->where('es_empresa', true)->count() . "\n";
