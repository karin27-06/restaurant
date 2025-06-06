<?php

use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AreasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Reportes\CategoryPDFController;
use App\Http\Controllers\Reportes\SupplierPDFController;
use App\Http\Controllers\Reportes\AlmacenPDFController;
use App\Http\Controllers\Reportes\FloorPDFController;
use App\Http\Controllers\Reportes\AreaPDFController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\ProductController;
use App\Models\ClientType;
use App\Models\EmployeeType;

Route::middleware('auth')->group(function () {
    Route::apiResource('Almacen', AlmacenController::class);
    Route::apiResource('Categoria', CategoryController::class);
    Route::apiResource('proveedor', SupplierController::class);
    Route::apiResource('presentacion', SupplierController::class);
    Route::apiResource('tipos_clientes', ClientType::class);
    Route::apiResource('tipos_empleados', EmployeeType::class);
    Route::apiResource('cliente', CustomerController::class);
    Route::apiResource('empleado', EmployeeController::class);
    Route::apiResource('piso', FloorController::class);
    Route::apiResource('producto', ProductController::class);

    #EXPORT API
    // Exportación a Excel
    Route::get('/categorias/export-excel', [CategoryController::class, 'exportExcel']);
    Route::get('/almacenes/export-excel', [AlmacenController::class, 'exportExcel']);
    Route::get('/proveedores/export-excel', [SupplierController::class, 'exportExcel']);
    Route::get('/pisos/export-excel', [FloorController::class, 'exportExcel']);
    Route::get('/areas/export-excel', [AreasController::class, 'exportExcel']);

    // Exportación a PDF
    Route::get('/categorias/export-pdf', [CategoryPDFController::class, 'exportPDF']);
    Route::get('/almacenes/export-pdf', [AlmacenPDFController::class, 'exportPDF']);
    Route::get('/proveedores/export-pdf', [SupplierPDFController::class, 'exportPDF']);
    Route::get('/pisos/export-pdf', [FloorPDFController::class, 'exportPDF']);
    Route::get('/areas/export-pdf', [AreaPDFController::class, 'exportPDF']);
    
    #IMPORT API
    // Importación de Excel
    Route::post('/categorias/import-excel', [CategoryController::class, 'importExcel']);
    Route::post('/almacenes/import-excel', [AlmacenController::class, 'importExcel']);
    Route::post('/proveedores/import-excel', [SupplierController::class, 'importExcel']);
    Route::post('/pisos/import-excel', [FloorController::class, 'importExcel']);
    Route::post('/areas/import-excel', [AreasController::class, 'importExcel']);
});

