<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Asegúrate de que este sea el modelo correcto
use Spatie\Permission\Models\Role;

class VendedorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Verificar si ya existe un usuario vendedor
        $vendedorUser = User::where('email', 'vendedor@gmail.com')->first();

        if (!$vendedorUser) {
            // Crear el usuario vendedor
            $vendedorUser = User::create([
                'name' => 'vendedor',
                'email' => 'vendedor@gmail.com',
                'password' => bcrypt('vendedor2024'), // Cambia la contraseña si es necesario
                'pin' => '123456', // Agregar un PIN único si lo usas en tu sistema
            ]);

            // Asignar el rol vendedor al usuario
            $vendedorRole = Role::firstOrCreate(['name' => 'vendedor']);
            $vendedorUser->assignRole($vendedorRole);

            $this->command->info('Usuario vendedor creado con éxito.');
        } else {
            $this->command->info('El usuario vendedor ya existe.');
        }
    }
}
