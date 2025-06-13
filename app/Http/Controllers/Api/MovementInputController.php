<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovementInputs\StoreMovementInputRequest;
use App\Http\Requests\MovementInputs\UpdateMovementInputRequest;
use App\Http\Resources\MovementInputResource;
use App\Models\MovementInput;
use App\Pipelines\FilterByCode;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;

class MovementInputController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', MovementInput::class);

        $perPage = $request->input('per_page', 15);

        $movementsInput = app(Pipeline::class)
            ->send(MovementInput::query())
            ->through(pipes: [
                new FilterByCode($request->input('search')),
                new FilterByState($request->input('state')),
            ])
            ->thenReturn()
            ->paginate($perPage);

        return MovementInputResource::collection($movementsInput);
    }

    public function store(StoreMovementInputRequest $request)
    {
        Gate::authorize('create', MovementInput::class);

        $validated = $request->validated();

        $exists = MovementInput::whereRaw('LOWER(code) = ?', [strtolower($validated['code'])])->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['code' => ['Este código ya está registrado.']]
            ], 422);
        }

        $movementInput = MovementInput::create($validated);

        return response()->json([
            'state' => true,
            'message' => 'Movimiento registrado correctamente.',
            'movement' => $movementInput
        ]);
    }

    public function show(MovementInput $movementInput)
    {
        Gate::authorize('view', $movementInput);

        return response()->json([
            'state' => true,
            'message' => 'Movimiento encontrado',
            'movement' => new MovementInputResource($movementInput),
        ], 200);
    }

    public function update(UpdateMovementInputRequest $request, MovementInput $movementInput)
{
    // Autorización de la acción de actualización
    Gate::authorize('update', $movementInput);

    // Validación de los datos de la solicitud
    $validated = $request->validated();

    // Actualización del movimiento con los datos validados
    $movementInput->update($validated);

    // Respuesta de éxito con el movimiento actualizado
    return response()->json([
        'state' => true,
        'message' => 'Movimiento de insumo actualizado correctamente.',
        'movement' => $movementInput->refresh() // Retorna el movimiento actualizado
    ]);
}


    public function destroy(MovementInput $movementInput)
    {
        Gate::authorize('delete', $movementInput);

        $movementInput->delete();

        return response()->json([
            'state' => true,
            'message' => 'Movimiento eliminado correctamente',
        ]);
    }

 

    
}
