<?php

use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\ProductController;
use App\Models\ClientType;
use App\Models\EmployeeType;

Route::middleware('auth')->group(function () {
    Route::apiResource('Almacen', AlmacenController::class);
    Route::apiResource('Categoria', CategoryController::class);
    Route::apiResource('proveedor', SupplierController::class);
    Route::apiResource('tipos_clientes', ClientType::class);
    Route::apiResource('tipos_empleados', EmployeeType::class);
    Route::apiResource('cliente', CustomerController::class);
    Route::apiResource('empleado', EmployeeController::class);
    Route::apiResource('piso', FloorController::class);
    Route::apiResource('producto', ProductController::class);
});

