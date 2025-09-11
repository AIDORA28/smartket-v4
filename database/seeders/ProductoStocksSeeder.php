<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\ProductoStock;
use App\Models\Core\Sucursal;
use Illuminate\Database\Seeder;

class ProductoStocksSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Obtener todos los productos que no tienen stock
        $productosSinStock = Producto::doesntHave('stocks')->get();

        foreach ($productosSinStock as $producto) {
            // Obtener las sucursales de la empresa del producto
            $sucursales = Sucursal::where('empresa_id', $producto->empresa_id)->get();
            
            foreach ($sucursales as $sucursal) {
                // Crear stock para cada sucursal
                ProductoStock::create([
                    'empresa_id' => $producto->empresa_id,
                    'producto_id' => $producto->id,
                    'sucursal_id' => $sucursal->id,
                    'cantidad_actual' => rand(10, 100), // Stock aleatorio entre 10 y 100
                    'cantidad_reservada' => 0,
                    'cantidad_disponible' => rand(10, 100),
                    'costo_promedio' => $producto->precio_costo ?? 0,
                    'ultimo_movimiento' => now(),
                ]);
            }
        }

        // También vamos a actualizar algunos stocks existentes para que tengan valores más realistas
        ProductoStock::chunk(10, function ($stocks) {
            foreach ($stocks as $stock) {
                $cantidadActual = rand(5, 200);
                $stock->update([
                    'cantidad_actual' => $cantidadActual,
                    'cantidad_disponible' => $cantidadActual, // Asumimos que no hay reservas
                    'ultimo_movimiento' => now()->subDays(rand(1, 30)),
                ]);
            }
        });

        $this->command->info('✅ Stocks de productos actualizados correctamente');
    }
}

