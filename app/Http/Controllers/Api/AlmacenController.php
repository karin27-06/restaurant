<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Almacen\StoreAlmacenRequest;
use App\Http\Requests\Almacen\UpdateAlmacenRequest;
use App\Models\Almacen;
use App\Http\Resources\AlmacenResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AlmacenController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Almacen::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search', '');
        $estado = $request->input('state');

        $query = Almacen::query();

        if (!empty($search)) {
            $normalizedSearch = strtolower(trim(preg_replace('/\s+/', ' ', $search)));
            $searchTerms = explode(' ', $normalizedSearch);

            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->orWhere(function ($subQuery) use ($term) {
                        $subQuery->where('name', 'ILIKE', '%' . $term . '%')
                            ->orWhereRaw("CASE WHEN state THEN 'activo' ELSE 'inactivo' END ILIKE ?", ["%{$term}%"]);
                    });
                }
            });
        }
        if (isset($estado)) {
            $query->where('state', (bool)$estado);
        }

        $almacen = $query->paginate($perPage);
        return AlmacenResource::collection($almacen);
    }
    public function store(StoreAlmacenRequest $request){
        Gate::authorize('create', Almacen::class);
        $validated = $request->validated();
        $exists = Almacen::whereRaw('LOWER(name) = ?', [$validated['name']])->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $almacen = Almacen::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Almacén registrado correctamente.',
            'almacen' => $almacen
        ]);
    }
    public function show(Almacen $almacen){
        Gate::authorize('view', $almacen);
        return response()->json([
            'state' => true,
            'message' => 'Almacen encontrado',
            'almacen' => new AlmacenResource($almacen),
        ], 200);
    }
    public function update(UpdateAlmacenRequest $request, Almacen $almacen){
        Gate::authorize('update', $almacen);
        $validated = $request->validated();
        $exists = Almacen::whereRaw('LOWER(name) = ?', [$validated['name']])
            ->where('id', '!=', $almacen->id)
            ->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $almacen->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Almacén actualizado correctamente.',
            'almacen' => $almacen->refresh()
        ]);
    }
    public function destroy(Almacen $almacen){
        Gate::authorize('delete', $almacen);
        $almacen->delete();
        return response()->json([
            'state' => true,
            'message' => 'Almacen eliminado de manera correcta',
        ]);
    }
}
