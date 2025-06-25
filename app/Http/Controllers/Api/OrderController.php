<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Orders;
use App\Models\OrderDishes;
use App\Http\Requests\Orders\StoreOrdersRequest;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function store(StoreOrdersRequest $request)
    {
        Gate::authorize('create', Orders::class);
        $validated = $request->validated();
        $platos = $request->input('platos', []);
        $order = Orders::create($validated);
        // Si hay platos, los registramos en order_dishes
        foreach ($platos as $plato) {
            OrderDishes::create([
                'idOrder' => $order->id,
                'idDishes' => $plato['id'],
                'quantity' => $plato['cantidad'],
                'price' => $plato['price'],
            ]);
        }
        return response()->json([
            'message' => 'Orden registrada correctamente',
            'order' => new OrderResource($order),
        ], 201);
    }
}