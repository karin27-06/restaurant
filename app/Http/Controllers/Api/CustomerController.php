<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\StoreCustomerRequest;
use App\Http\Requests\Cliente\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Pipelines\FilterByName;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CustomerController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Customer::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $query = app(Pipeline::class)
            ->send(Customer::query())
            ->through([
                new FilterByName($search),
            ])
            ->thenReturn();

        return CustomerResource::collection($query->paginate($perPage));
    }
    public function store(StoreCustomerRequest $request){
        Gate::authorize('create', Customer::class);
        $validated = $request->validated();
        $customer = Customer::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Cliente registrado correctamente.',
            'customer' => $customer
        ]);
    }
    public function show(Customer $customer){
        Gate::authorize('view', $customer);
        return response()->json([
            'status' => true,
            'message' => 'Cliente encontrado',
            'customer' => new CustomerResource($customer)
        ]);
    }
    public function update(UpdateCustomerRequest $request, Customer $customer){
        Gate::authorize('update', $customer);
        $validated = $request->validated();
        $customer->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Cliente actualizado correctamente.',
            'customer' => $customer->refresh()
        ]);
    }
    public function destroy(Customer $customer){
        Gate::authorize('delete', $customer);
        $customer->delete();
        return response()->json([
            'status' => true,
            'message' => 'Cliente eliminado correctamente'
        ]);
    }
}
