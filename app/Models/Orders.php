<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders'; 
    protected $fillable = [
        'idCustomer',
        'idTable',
        'totalPrice',
        'state',
    ];

    // Relación con el modelo Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'idCustomer');
    }

    // Relación con el modelo Table
    public function table()
    {
        return $this->belongsTo(Table::class, 'idTable');
    }

    // Relación uno a muchos con OrderDish (platos en el pedido)
    public function orderDishes()
    {
        return $this->hasMany(OrderDishes::class, 'idOrder');
    }
}
