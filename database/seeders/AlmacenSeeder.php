<?php

namespace Database\Seeders;

use App\Models\Almacen;
use Illuminate\Database\Seeder;

class AlmacenSeeder extends Seeder{
    public function run(): void{
        Almacen::create([
            'name' => 'Almacen Principal',
            'state' => true,
        ]);
        Almacen::create([
            'name' => 'Almacen Secundario',
            'state' => true,
        ]);
    }
}
