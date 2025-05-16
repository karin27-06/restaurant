<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;;
use App\Http\Requests\Dishes\StoreDishesRequests;
use App\Http\Requests\Dishes\UpdateDishesRequests;
use App\Http\Resources\DishesResource;
use App\Models\Dishes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Pipeline\Pipeline;
class DishesController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Dishes::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $state = $request->input('state');
        $query = app(Pipeline::class)
            ->send(Dishes::query())
            ->through([
                new FilterByName($search),
                new FilterByState($state),
            ])
            ->thenReturn();

        return DishesResource::collection($query->paginate($perPage));
    }
    public function store(StoreDishesRequests $request){
        Gate::authorize('create', Dishes::class);
        $validated = $request->validated();
        $exists = Dishes::whereRaw('LOWER(name) = ?', [$validated['name']])->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $dishes = Dishes::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Plato registrado correctamente.',
            'dishes' => $dishes
        ]);
    }
    public function show(Dishes $dishes){
        Gate::authorize('view', $dishes);
        return response()->json([
            'state' => true,
            'message' => 'Plato encontrado',
            'dishes' => new DishesResource($dishes),
        ], 200);
    }
    public function update(UpdateDishesRequests $request, Dishes $dishes){
        Gate::authorize('update', $dishes);
        $validated = $request->validated();
        $exists = Dishes::whereRaw('LOWER(name) = ?', [$validated['name']])
            ->where('id', '!=', $dishes->id)
            ->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $dishes->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Patos actualizado correctamente.',
            'dishes' => $dishes->refresh()
        ]);
    }
    public function destroy(Dishes $dishes){
        Gate::authorize('delete', $dishes);
        $dishes->delete();
        return response()->json([
            'state' => true,
            'message' => 'Platos eliminado de manera correcta',
        ]);
    }
}
