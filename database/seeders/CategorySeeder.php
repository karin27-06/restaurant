<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder{
    public function run(): void{
        Category::create([
            'name' => 'Entrantes',
            'state' => true,
        ]);
        Category::create([
            'name' => 'Platos Principales',
            'state' => true,
        ]);
        Category::create([
            'name' => 'Postres',
            'state' => true,
        ]);
        Category::create([
            'name' => 'Bebidas',
            'state' => true,
        ]);
        Category::create([
            'name' => 'Especialidades',
            'state' => true,
        ]);
    }
}
