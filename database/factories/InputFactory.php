<?php

namespace Database\Factories;

use App\Models\Input;
use App\Models\Almacen;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class InputFactory extends Factory
{
    protected $model = Input::class;

    protected static $insumos = [
        'Aceite de oliva',
        'Harina de trigo',
        'Azúcar',
        'Sal',
        'Pimienta negra',
        'Tomate',
        'Cebolla',
        'Ajo',
        'Leche',
        'Huevos',
        'Arroz',
        'Carne de res',
        'Pollo',
        'Pescado',
        'Queso',
        'Mantequilla',
        'Vino tinto',
        'Vinagre',
        'Pasta',
        'Caldo de pollo',
    ];

    public function definition()
    {

        return [
            'name' => $this->faker->unique()->randomElement(self::$insumos),
            'price' => $this->faker->randomFloat(2, 1, 500),
            'quantity' => $this->faker->numberBetween(1, 200),
            'idAlmacen' => Almacen::inRandomOrder()->first()->id,
            'idSupplier' => Supplier::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence(),
            'state' => $this->faker->boolean(90),
        ];
    }
}
