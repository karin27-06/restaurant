<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConsultasId extends Controller
{
    public function getUserId()
    {
        // Obtener el ID del usuario autenticado
        $user_id = Auth::user()->id;

        // Retornar el ID del usuario como respuesta JSON
        return response()->json([
            'success' => true,
            'user_id' => $user_id
        ]);
    }
}
