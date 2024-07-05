<?php

namespace Database\Seeders;

use App\Models\Bodega;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BodegaGeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Bodega::create(['nombre' => 'Bodega General']);
    }
}
