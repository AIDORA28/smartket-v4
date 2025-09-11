<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventory\Categoria;

class CategoriasSeeder extends Seeder
{
    /**
     * Categorías completas para diferentes tipos de negocio en Perú
     */
    public function run(): void
    {
        $categorias = [
            // === ABARROTES ===
            ['nombre' => 'Aceites y Vinagres', 'descripcion' => 'Aceites comestibles, vinagres y aderezos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Arroz y Granos', 'descripcion' => 'Arroz, quinua, avena y cereales', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Azúcar y Endulzantes', 'descripcion' => 'Azúcar blanca, rubia, miel y edulcorantes', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Conservas', 'descripcion' => 'Atún, sardinas, duraznos y conservas diversas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Fideos y Pastas', 'descripcion' => 'Fideos, pastas secas y frescas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Harinas', 'descripcion' => 'Harina de trigo, preparadas y especiales', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Legumbres Secas', 'descripcion' => 'Lentejas, garbanzos, frijoles secos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Menestras', 'descripcion' => 'Pallares, frijoles canarios y menestras', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Sal y Condimentos', 'descripcion' => 'Sal de mesa, marina y condimentos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Salsas y Cremas', 'descripcion' => 'Mayonesa, ketchup, mostaza y salsas', 'empresa_id' => 1, 'activa' => true],

            // === BEBIDAS ===
            ['nombre' => 'Agua', 'descripcion' => 'Agua mineral, de mesa y saborizada', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Bebidas Alcohólicas', 'descripcion' => 'Cervezas, vinos y licores', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Bebidas Calientes', 'descripcion' => 'Té, café, chocolate caliente', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Bebidas Energéticas', 'descripcion' => 'Energizantes y bebidas deportivas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Gaseosas', 'descripcion' => 'Coca-Cola, Inca Kola, Pepsi y gaseosas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Jugos y Néctares', 'descripcion' => 'Jugos naturales, néctares y pulpas', 'empresa_id' => 1, 'activa' => true],

            // === LÁCTEOS ===
            ['nombre' => 'Leche', 'descripcion' => 'Leche fresca, evaporada y en polvo', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Quesos', 'descripcion' => 'Quesos frescos, maduros y procesados', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Yogures', 'descripcion' => 'Yogures naturales, con frutas y griegos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Mantequilla y Margarinas', 'descripcion' => 'Mantequilla, margarina y untables', 'empresa_id' => 1, 'activa' => true],

            // === PANADERÍA Y PASTELERÍA ===
            ['nombre' => 'Panadería', 'descripcion' => 'Pan francés, integral, molde y especiales', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Pastelería', 'descripcion' => 'Pasteles, tortas, queques y postres', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Galletas', 'descripcion' => 'Galletas dulces, saladas y crackers', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Bizcochos', 'descripcion' => 'Bizcochos, tostadas y panes dulces', 'empresa_id' => 1, 'activa' => true],

            // === SNACKS Y DULCES ===
            ['nombre' => 'Chocolates', 'descripcion' => 'Chocolates, bombones y coberturas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Caramelos', 'descripcion' => 'Caramelos duros, blandos y chicles', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Papas Fritas', 'descripcion' => 'Papas fritas, chifles y snacks salados', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Frutos Secos', 'descripcion' => 'Nueces, almendras, maní y mix', 'empresa_id' => 1, 'activa' => true],

            // === LIMPIEZA ===
            ['nombre' => 'Detergentes', 'descripcion' => 'Detergentes para ropa y vajilla', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Papel Higiénico', 'descripcion' => 'Papel higiénico y servilletas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Productos de Limpieza', 'descripcion' => 'Lejía, desinfectantes y limpiadores', 'empresa_id' => 1, 'activa' => true],

            // === CUIDADO PERSONAL ===
            ['nombre' => 'Cuidado Capilar', 'descripcion' => 'Champús, acondicionadores y tratamientos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Higiene Bucal', 'descripcion' => 'Pasta dental, cepillos y enjuagues', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Cuidado Corporal', 'descripcion' => 'Jabones, geles y cremas corporales', 'empresa_id' => 1, 'activa' => true],

            // === VERDURAS Y FRUTAS ===
            ['nombre' => 'Verduras', 'descripcion' => 'Verduras frescas y hortalizas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Frutas', 'descripcion' => 'Frutas frescas nacionales e importadas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Hierbas Aromáticas', 'descripcion' => 'Perejil, cilantro, hierba buena', 'empresa_id' => 1, 'activa' => true],

            // === CARNES ===
            ['nombre' => 'Carnes Rojas', 'descripcion' => 'Res, carnero y cerdo', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Aves', 'descripcion' => 'Pollo, pavo y derivados', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Pescados y Mariscos', 'descripcion' => 'Pescados frescos y mariscos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Embutidos', 'descripcion' => 'Jamón, salchichas y embutidos', 'empresa_id' => 1, 'activa' => true],

            // === FARMACIA ===
            ['nombre' => 'Medicamentos', 'descripcion' => 'Medicamentos con y sin receta', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Vitaminas', 'descripcion' => 'Vitaminas y suplementos nutricionales', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Primeros Auxilios', 'descripcion' => 'Vendas, gasas y material médico', 'empresa_id' => 1, 'activa' => true],

            // === FERRETERÍA ===
            ['nombre' => 'Herramientas', 'descripcion' => 'Martillos, destornilladores y herramientas', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Tornillería', 'descripcion' => 'Tornillos, tuercas y pernos', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Pinturas', 'descripcion' => 'Pinturas, barnices y solventes', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Electricidad', 'descripcion' => 'Cables, focos y material eléctrico', 'empresa_id' => 1, 'activa' => true],

            // === LIBRERÍA ===
            ['nombre' => 'Útiles Escolares', 'descripcion' => 'Cuadernos, lápices y material escolar', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Oficina', 'descripcion' => 'Papel, archivadores y material de oficina', 'empresa_id' => 1, 'activa' => true],

            // === ROPA ===
            ['nombre' => 'Ropa Casual', 'descripcion' => 'Polos, pantalones y ropa casual', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Ropa Interior', 'descripcion' => 'Ropa interior masculina y femenina', 'empresa_id' => 1, 'activa' => true],
            ['nombre' => 'Calzado', 'descripcion' => 'Zapatos, zapatillas y sandalias', 'empresa_id' => 1, 'activa' => true],

            // === GENERAL ===
            ['nombre' => 'Otros', 'descripcion' => 'Productos diversos no clasificados', 'empresa_id' => 1, 'activa' => true],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(
                ['nombre' => $categoria['nombre'], 'empresa_id' => $categoria['empresa_id']],
                $categoria
            );
        }
    }
}

