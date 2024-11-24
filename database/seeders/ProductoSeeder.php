<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Producto; // Asegúrate de que el modelo Producto exista

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 2; $i <= 100; $i++) {
            Producto::create([
                'categoria_id' => 1, // Categoría General
                'proveedor_id' => 1, // Proveedor 1
                'unidadmedida_id' => 1, // Unidad de Medida 1
                'codigo_barra' => Str::random(10), // Código de barra aleatorio
                'nombre' => 'Producto ' . $i, // Nombre del producto
                'imagen' => null, // Dejar nulo o asignar un valor predeterminado si es necesario
                'preciocompra' => rand(500, 20000), // Precio de compra aleatorio entre 500 y 5000
                'precioventa' => rand(600, 10000), // Precio de venta aleatorio mayor al precio de compra
                'estado' => true, // Producto activo
            ]);
        }
    }
}
