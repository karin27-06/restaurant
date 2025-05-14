<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterById;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ProductController extends Controller{
    public function listarProductos(Request $request){
        Gate::authorize('viewAny', Product::class);
        try {
            $name = $request->get('name');
            $id = $request->get('id');
            $products = app(Pipeline::class)
                ->send(Product::query())
                ->through([
                    new FilterByName($name),
                    new FilterById($id),
                ])
                ->thenReturn()->orderBy('id','asc')->paginate(12);
            return response()->json([
                'products'=> ProductResource::collection($products),
                'pagination' => [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los productos',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function create(){
        return Inertia::render('panel/product/components/formProduct');
    }

    public function store(StoreProductRequest $request){
        Gate::authorize('create', Product::class);
        $validated = $request->validated();
    
        // Asegurarte de que el campo 'state' sea booleano
        $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
        
        $product = Product::create($validated);
        return redirect()->route('panel.products.index')->with('message', 'Producto creado correctamente');
    }
    public function show(Product $product){
        Gate::authorize('view', $product);
        return response()->json([
            'state' => true,
            'message' => 'Producto encontrado',
            'product' => new ProductResource($product),
        ], 200);
    }
    public function update(UpdateProductRequest $request, Product $product){
        Gate::authorize('update', $product);
        $validated = $request->validated();
        $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
        $product->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Producto actualizado correctamente',
            'product' => new ProductResource($product->refresh()),
        ]);
    }
    public function destroy(Product $product){
        Gate::authorize('delete', $product);
        $product->delete();
        return response()->json([
            'state' => true,
            'message' => 'Producto eliminado correctamente',
        ]);
    }   
}
