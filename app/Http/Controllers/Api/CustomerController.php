<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\ClientType;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CustomerController extends Controller{
    public function listarCustomers(Request $request){
        Gate::authorize('viewAny', Customer::class);
        try {
            $name = $request->get('name');
            $customers = Customer::when($name, function ($query, $name) {
                return $query->whereLike('name', "%$name%");
            })->orderBy('id','asc')->paginate(12);
            return response()->json([
                'customers' => CustomerResource::collection($customers),
                'pagination' => [
                    'total' => $customers->total(),
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'last_page' => $customers->lastPage(),
                    'from' => $customers->firstItem(),
                    'to' => $customers->lastItem()
                ]
                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los clientes',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function create(){
        $client_types = ClientType::select('id', 'name')
            ->where('state', 1)
            ->orderBy('id')
            ->get();
        return Inertia::render('panel/customer/components/formCustomer', [
            'customerTypes' => $client_types
        ]);
    }
    public function store(StoreCustomerRequest $request){
        Gate::authorize('create', Customer::class); 
        $validated = $request->validated();
        $customer = Customer::create($validated);
        return redirect()->route('panel.customers.index')->with('message', 'Cliente creado correctamente');
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
        $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
        $customer->update($validated);
        return response()->json([
            'status' => true,  
            'message' => 'Cliente actualizado correctamente desde backend',
            'customer' => new CustomerResource($customer)
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
