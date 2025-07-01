<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use HasFactory;

    // Definir los campos que son asignables en masa
    protected $fillable = [
        'idSale',
        'prefix',
        'serie',
        'number'
    ];


}
