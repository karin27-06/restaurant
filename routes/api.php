<?php

use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\Panel\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\CategoryController;
use App\Http\Controllers\Panel\FloorController;
use App\Http\Controllers\Panel\ProductController;
use App\Models\ClientType;

Route::middleware('auth')->group(function () {
    Route::apiResource('Almacen', AlmacenController::class);
    Route::apiResource('Categoria', CategoryController::class);
    Route::apiResource('tipos_clientes', ClientType::class);
    Route::apiResource('cliente', CustomerController::class);
    Route::apiResource('piso', FloorController::class);
    Route::apiResource('producto', ProductController::class);
});

