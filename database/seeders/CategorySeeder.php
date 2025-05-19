<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['name' => 'Entrada',        'state' => true],
            ['name' => 'Plato de Fondo', 'state' => true],
            ['name' => 'Postre',         'state' => true],
            ['name' => 'Bebida',         'state' => true],
            ['name' => 'Sopa',           'state' => true],
            ['name' => 'Ensalada',       'state' => true],
            ['name' => 'Guarnición',     'state' => true],
            ['name' => 'Combo',          'state' => true],
            ['name' => 'Promoción',      'state' => true],
            ['name' => 'Adicional',      'state' => true],
        ];

        foreach ($categorias as $categoria) {
            Category::create($categoria);
        }
    }
}
