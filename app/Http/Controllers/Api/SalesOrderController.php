<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleOrder\StoreSaleOrderRequest;
use App\Http\Requests\SaleOrder\UpdateSaleOrderRequest;
use App\Http\Resources\SaleOrderResource;
use App\Models\SalesOrder;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SalesOrderController extends Controller
{
public function index(Request $request)
{
    Gate::authorize('viewAny', Sale::class);

    $perPage = $request->input('per_page', 15);
    $saleOrders = SalesOrder::with(['sale', 'order', 'order.orderDishes']); // Asegúrate de que se cargue la relación 'orderDishes'

    // Filtrar por idOrder si se pasa en la solicitud
    if ($idOrder = $request->input('idOrder')) {
        $saleOrders->where('idOrder', $idOrder);
    }

    // Filtrar por idSale si se pasa en la solicitud
    if ($idSale = $request->input('idSale')) {
        $saleOrders->where('idSale', $idSale);
    }
    // Filtrar por código de cliente usando LIKE si se pasa en la solicitud
    if ($codigo = $request->input('search')) {
        $saleOrders->whereHas('sale.customer', function ($query) use ($codigo) {
            $query->where('codigo', 'like', '%' . $codigo . '%');
        });
    }

    // Filtrar por fecha de inicio y fecha final si se pasan en la solicitud
    if ($startDate = $request->input('start_date')) {
        $saleOrders->whereDate('created_at', '>=', $startDate);
    }

    if ($endDate = $request->input('end_date')) {
        $saleOrders->whereDate('created_at', '<=', $endDate);
    }

    // Paginación
    $saleOrders = $saleOrders->paginate($perPage);

    return SaleOrderResource::collection($saleOrders);
}





    public function store(StoreSaleOrderRequest $request)
    {
        Gate::authorize('create', arguments: Sale::class);

        $validated = $request->validated();

        // Verificar si ya existe una venta con el mismo idSale y idOrder
        $exists = SalesOrder::where('idOrder', $validated['idOrder'])
                           ->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['sale_order' => ['Este pedido ya está registrado.']]
            ], 422);
        }

        $saleOrder = SalesOrder::create($validated);

        return response()->json([
            'state' => true,
            'message' => 'Pedido de venta registrado correctamente.',
            'sale_order' => new SaleOrderResource($saleOrder)
        ]);
    }

    public function show(Sale $saleOrder)
    {
        Gate::authorize('view', arguments: $saleOrder);

        return response()->json([
            'state' => true,
            'message' => 'Pedido de venta encontrado',
            'sale_order' => new SaleOrderResource($saleOrder),
        ], 200);
    }

    public function update(UpdateSaleOrderRequest $request, Sale $saleOrder)
    {
        Gate::authorize('update', $saleOrder);

        $validated = $request->validated();

        // Verificar si el pedido ya existe con los mismos ids de venta y orden (excepto el que se está actualizando)
        $exists = SalesOrder::where('idSale', $validated['idSale'])
                           ->where('idOrder', $validated['idOrder'])
                           ->where('id', '!=', $saleOrder->id)
                           ->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['sale_order' => ['Este pedido ya está registrado.']]
            ], 422);
        }

        $saleOrder->update($validated);

        return response()->json([
            'state' => true,
            'message' => 'Pedido de venta actualizado correctamente',
            'sale_order' => new SaleOrderResource($saleOrder->refresh()),
        ]);
    }

    public function destroy(Sale $saleOrder)
    {
        Gate::authorize('delete', $saleOrder);

        $saleOrder->delete();

        return response()->json([
            'state' => true,
            'message' => 'Pedido de venta eliminado correctamente',
        ]);
    }
}
