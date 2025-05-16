<?php

namespace Database\Seeders;

use App\Models\Areas;
use Illuminate\Database\Seeder;

class AreasSeeder extends Seeder{
    public function run(): void{
        Areas::factory(100)->create();
    }
}
