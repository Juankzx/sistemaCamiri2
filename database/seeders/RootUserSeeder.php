<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Asegúrate de que este sea el modelo correcto
use Spatie\Permission\Models\Role;

class RootUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Verificar si ya existe un usuario root
        $rootUser = User::where('email', 'root@gmail.com')->first();

        if (!$rootUser) {
            // Crear el usuario root
            $rootUser = User::create([
                'name' => 'root',
                'email' => 'root@gmail.com',
                'password' => bcrypt('root2024'), // Cambia la contraseña si es necesario
            ]);

            // Asignar el rol root al usuario
            $rootRole = Role::firstOrCreate(['name' => 'root']);
            $rootUser->assignRole($rootRole);

            $this->command->info('Usuario root creado con éxito.');
        } else {
            $this->command->info('El usuario root ya existe.');
        }
    }
}
