<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'idAlmacen',
        'idSupplier',
        'description',
        'state',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'idAlmacen');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'idSupplier');
    }
}
