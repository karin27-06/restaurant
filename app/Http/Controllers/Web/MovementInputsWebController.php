<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Input;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class MovementInputsWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', Input::class);
        return Inertia::render('panel/Movements/Inputs/indexMovementInput');
    }
}
