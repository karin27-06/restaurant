<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDishResource;

use App\Models\OrderDishes;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pipeline\Pipeline;
use App\Pipelines\FilterByNameHistorial;
use App\Pipelines\FilterByStateHistorial;

class OrderDishController extends Controller
{
    // Lista de pedidos con relaciones
public function index(Request $request)
{
    Gate::authorize('viewAny', Orders::class);

    $perPage = $request->input('per_page', 15);
    $idOrder = $request->input('idOrder');

    $query = OrderDishes::query();

    // Filtro condicional por idOrder
    if (!is_null($idOrder)) {
        $query->where('idOrder', $idOrder);
    }

    // Aplica otros filtros si existen
    $query = app(Pipeline::class)
        ->send($query)
        ->through([
            new FilterByNameHistorial($request->input('search')),
            new FilterByStateHistorial($request->input('state')),
        ])
        ->thenReturn();

    $orders = $query->paginate($perPage);

    return OrderDishResource::collection($orders);
}


}
