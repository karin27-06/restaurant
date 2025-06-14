<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovementInputDetails\StoreMovementInputDetailRequest;
use App\Http\Requests\MovementInputDetails\UpdateMovementInputDetailRequest;
use App\Http\Resources\MovementInputDetailResource;
use App\Models\MovementInputDetail;
use App\Pipelines\FilterByNameInput;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Gate;

class MovementInputDetailController extends Controller
{
public function index(Request $request, $id)
{
    Gate::authorize('viewAny', MovementInputDetail::class);

    $perPage = $request->input('per_page', 15);

    // Intentar buscar los detalles con el primer filtro por idMovementInput
    $movementInputDetails = app(Pipeline::class)
        ->send(MovementInputDetail::query()->where('idMovementInput', $id))  // Filtro por idMovementInput
        ->through([new FilterByNameInput($request->input('search'))])
        ->thenReturn()
        ->with('movementInput', 'input')  // Cargar relaciones necesarias
        ->paginate($perPage);  // Pagina los resultados

    // Verificar si no hay datos (vacío) con el filtro original
    if ($movementInputDetails->isEmpty()) {
        // Si no hay resultados, intentar con el filtro por 'id'
        $movementInputDetails = app(Pipeline::class)
            ->send(MovementInputDetail::query()->where('id', $id))  // Filtro por id
            ->through([new FilterByNameInput($request->input('search'))])
            ->thenReturn()
            ->with('movementInput', 'input')  // Cargar relaciones necesarias
            ->paginate($perPage);  // Pagina los resultados
    }

    // Variables para los totales
    $subtotal = 0;
    $totalIGV = 0;
    $total = 0;

    // Recorrer para hacer cálculos
    foreach ($movementInputDetails as $detail) {
        // Verificar si el IGV está habilitado
        if ($detail->movementInput->igv_state == 0) {
            // El totalPrice ya tiene el IGV incluido.
            // El IGV es el 18% del total (que es el precio con el IGV)
            $igv = $detail->totalPrice * 0.18;

            // El subtotal es el totalPrice menos el IGV
            $subtotal += $detail->totalPrice - $igv;

            // El totalIGV es simplemente el IGV calculado
            $totalIGV += $igv;
        } else if ($detail->movementInput->igv_state == 1) {
            $igv = $detail->totalPrice * 0.18;
            $totalIGV += $igv;
            
            // El subtotal es el totalPrice sin el IGV
            $subtotal += $detail->totalPrice;
        }
    }

    // El total final es la suma del subtotal y el total del IGV
    $total = $subtotal + $totalIGV;

    // Devolver los datos con la paginación y totales
    return response()->json([
        'state' => true,
        'message' => 'Detalles de movimiento cargados correctamente.',
        'data' => MovementInputDetailResource::collection($movementInputDetails),
        'meta' => [
            'current_page' => $movementInputDetails->currentPage(),
            'total' => $movementInputDetails->total(),
            'last_page' => $movementInputDetails->lastPage(),
            'per_page' => $movementInputDetails->perPage(),
        ],
        'subtotal' => number_format($subtotal, 2),
        'total_igv' => number_format($totalIGV, 2),
        'total' => number_format($total, 2),
    ]);
}






public function store(StoreMovementInputDetailRequest $request)
{
    Gate::authorize('create', MovementInputDetail::class);

    $validated = $request->validated(); // Valida los datos según la solicitud

    // Verificar si ya existe un detalle de movimiento con el mismo idInput e idMovementInput
    $exists = MovementInputDetail::where('idMovementInput', $validated['idMovementInput'])
                                ->where('idInput', $validated['idInput'])
                                ->exists();

    if ($exists) {
        return response()->json([
            'state' => false,
            'message' => 'Ya existe un detalle de movimiento con el mismo insumo para este movimiento.',
        ], 422); // 422 Unprocessable Entity si el registro ya existe
    }

    // Crear un nuevo detalle de movimiento
    $movementInputDetail = MovementInputDetail::create($validated);

    return response()->json([
        'state' => true,
        'message' => 'Detalle de movimiento registrado correctamente.',
        'movementInputDetail' => new MovementInputDetailResource($movementInputDetail)
    ]);
}



    public function show(MovementInputDetail $movementInputDetail)
    {
        Gate::authorize('view', $movementInputDetail);

        return response()->json([
            'state' => true,
            'message' => 'Detalle de movimiento encontrado',
            'movementInputDetail' => new MovementInputDetailResource($movementInputDetail),
        ], 200);
    }


    public function update(UpdateMovementInputDetailRequest $request, MovementInputDetail $MovementInputDetail)
    {
        Gate::authorize('update', $MovementInputDetail);

        $validated = $request->validated();

        // Check if the MovementInput exists
        $movementInputExists = \App\Models\MovementInput::find($validated['idMovementInput']);

        if (!$movementInputExists) {
            return response()->json([
                'state' => false,
                'message' => 'El movimiento de insumo no existe.',
            ], 404);  // Return error if the referenced movement doesn't exist
        }

        // Check if this movement input and input combination already exists
        $inputExists = MovementInputDetail::where('idMovementInput', $validated['idMovementInput'])
            ->where('idInput', $validated['idInput'])
            ->where('id', '!=', $MovementInputDetail->id)  // Verify for other records with different ID
            ->exists();

        if ($inputExists) {
            return response()->json([
                'errors' => ['input' => ['Ya existe este detalle para el mismo movimiento.']]
            ], 422);
        }

        // Update the Movement Input Detail
        $MovementInputDetail->update($validated);

        return response()->json([
            'state' => true,
            'message' => 'Detalle de movimiento actualizado de manera correcta',
        ]);
    }

    public function destroy($id)
    {
        // Buscar el detalle de movimiento por su ID
        $movementInputDetail = MovementInputDetail::find($id);

        // Verificar si el detalle de movimiento existe
        if (!$movementInputDetail) {
            return response()->json([
                'state' => false,
                'message' => 'Detalle de movimiento no encontrado.',
            ], 404);
        }

        // Eliminar el detalle de movimiento
        $movementInputDetail->delete();

        // Retornar una respuesta exitosa
        return response()->json([
            'state' => true,
            'message' => 'Detalle de movimiento eliminado correctamente.',
        ]);
    }
}
