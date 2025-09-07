<?php

require_once __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Ejecutar el seeder de demostración
require_once __DIR__ . '/../../database/seeders/DashboardDemoSeeder.php';

$seeder = new DashboardDemoSeeder();
$seeder->run();
