<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterById;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CategoryController extends Controller{
    public function index(){
        Gate::authorize('viewAny', Category::class);
        return Inertia::render('panel/category/indexCategory');
    }
    public function listarCategories(Request $request){
        Gate::authorize('viewAny', Category::class);
        try {
            $name = $request->get('name');
            $id = $request->get('id');
            $categories = app(Pipeline::class)
                ->send(Category::query())
                ->through([
                    new FilterByName($name),
                    new FilterById($id),
                ])
                ->thenReturn()->orderBy('id','asc')->paginate(12);
            return response()->json([
                'categories'=> CategoryResource::collection($categories),
                'pagination' => [
                    'total' => $categories->total(),
                    'current_page' => $categories->currentPage(),
                    'per_page' => $categories->perPage(),
                    'last_page' => $categories->lastPage(),
                    'from' => $categories->firstItem(),
                    'to' => $categories->lastItem(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar las categorías',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function create(){
        return Inertia::render('panel/category/components/formCategory');
    }
    public function store(StoreCategoryRequest $request){
        Gate::authorize('create', Category::class);
        $validated = $request->validated();
        $validated = $request->safe()->except(['state']);
        $category = Category::create(Arr::except($validated, ['state']));
        // // $validated['state'] = $validated['state'] === 'activo' ? true : false;
        return redirect()->route('panel.categories.index')->with('message', 'Categoría creada correctamente');   
    }
    public function show(Category $category){
        Gate::authorize('view', $category);
        return response()->json([
            'state' => true,
            'message' => 'Categoría encontrada',
            'category' => new CategoryResource($category),
        ], 200);
    }
    public function update(UpdateCategoryRequest $request, Category $category){
        Gate::authorize('update', $category);
        $validated = $request->validated();
        $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
        $category->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Categoría actualizada correctamente',
            'category' => new CategoryResource($category->refresh()),
        ]);
    }
    public function destroy(Category $category){
        Gate::authorize('delete', $category);
        $category->delete();
        return response()->json([
            'state' => true,
            'message' => 'Categoría eliminada correctamente',
        ]);
    }
    public function getCategoriesOption(){
        try {
            $categories = Category::select('id', 'name')->get();

            return response()->json([
                'categories' => $categories
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al obtener las categorias',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
