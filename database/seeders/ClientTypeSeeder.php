<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder{
    public function run(): void{
       ClientType::create([
            'name' => 'Persona',
            'state' => true,
        ]);
        ClientType::create([
            'name' => 'Empresa',
            'state' => true,
        ]);
    }
}
