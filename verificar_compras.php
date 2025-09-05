<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ESTRUCTURA DE TABLA COMPRAS ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('compras');
print_r($columns);

echo "\n=== VERIFICANDO DATOS DE EJEMPLO ===\n";
$compras = \App\Models\Compra::first();
if ($compras) {
    echo "Campos disponibles en primera compra:\n";
    print_r($compras->toArray());
} else {
    echo "No hay compras en la base de datos\n";
}
?>
