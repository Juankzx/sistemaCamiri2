<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar a cada seeder de forma secuencial
        $this->call([
            RolesAndPermissionsSeeder::class,
            RootUserSeeder::class,
            VendedorUserSeeder::class,
            BodegaGeneralSeeder::class,
            ProductoSeeder::class,
        ]);
    }
}
