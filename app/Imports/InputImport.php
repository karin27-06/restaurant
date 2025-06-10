<?php

namespace App\Imports;

use App\Models\Input;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class InputImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Input::create([
                'name' => $row['nombre'],
                'description' => $row['descripción'],
                'price' => $row['precio'],
                'quantity' => $row['cantidad'],
                'idAlmacen' => $row['almacen'],
                'idSupplier' => $row['proveedor'],
                'state' => strtolower($row['estado']) === 'activo' ? true : false,
            ]);
        }
    }
}
