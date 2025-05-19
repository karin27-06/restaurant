<?php

use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Api\AreasController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ConsultasDni;
use App\Http\Controllers\Api\DishesController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\UsuariosController;
use App\Http\Controllers\Api\ClientTypeController;
use App\Http\Controllers\Api\EmployeeTypeController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Web\AlmacenWebController;
use App\Http\Controllers\Web\AreasWebController;
use App\Http\Controllers\Web\CategoryWebController;
use App\Http\Controllers\Web\SupplierWebController;
use App\Http\Controllers\Web\ClientTypeWebController;
use App\Http\Controllers\Web\EmployeeTypeWebController;
use App\Http\Controllers\Web\CustomerWebController;
use App\Http\Controllers\Web\DishesWebController;
use App\Http\Controllers\Web\EmployeeWebController;
use App\Http\Controllers\Web\FloorWebController;
use App\Http\Controllers\Web\ProductWebController;
use App\Http\Controllers\Web\UsuarioWebController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return Inertia::render('Dashboard', [
            'mustReset' => $user->restablecimiento == 0,
        ]);
    })->name('dashboard');

    #VISTAS DEL FRONTEND
    Route::get('/almacenes', [AlmacenWebController::class, 'index'])->name('index.view');
    Route::get('/categorias', [CategoryWebController::class, 'index'])->name('index.view');
    Route::get('/proveedores', [SupplierWebController::class, 'index'])->name('index.view');
    Route::get('/clientes', [CustomerWebController::class, 'index'])->name('index.view');
    Route::get('/empleados', [EmployeeWebController::class, 'index'])->name('index.view');
    Route::get('/tipo_clientes', [ClientTypeWebController::class, 'index'])->name('index.view');
    Route::get('/tipo_empleados', [EmployeeTypeWebController::class, 'index'])->name('index.view');
    Route::get('/pisos', [FloorWebController::class, 'index'])->name('index.view');
    Route::get('/productos', [ProductWebController::class, 'index'])->name('index.view');
    Route::get('/usuario', [UsuarioWebController::class,'index'])->name('index.view');
    Route::get('/areas', [AreasWebController::class,'index'])->name('index.view');
    Route::get('/platos', [DishesWebController::class,'index'])->name('index.view');
    Route::get('/roles', [UsuarioWebController::class, 'roles'])->name('roles.view');

    #CONSULTA  => BACKEND
    Route::get('/consulta/{dni}', [ConsultasDni::class, 'consultar'])->name('consultar.dni');

    #AREAS => BACKEND
    Route::prefix('area')->group(function () {
        Route::get('/', [AreasController::class, 'index'])->name('area.index');
        Route::post('/', [AreasController::class, 'store'])->name('area.store');
        Route::get('{areas}', [AreasController::class, 'show'])->name('area.show');
        Route::put('{areas}', [AreasController::class, 'update'])->name('area.update');
        Route::delete('{areas}', [AreasController::class, 'destroy'])->name('area.destroy');
    });

    #PLATOS => BACKEND
    Route::prefix('plato')->group(function () {
        Route::get('/', [DishesController::class, 'index'])->name('plato.index');
        Route::post('/', [DishesController::class, 'store'])->name('plato.store');
        Route::get('{dishes}', [DishesController::class, 'show'])->name('plato.show');
        Route::put('{dishes}', [DishesController::class, 'update'])->name('plato.update');
        Route::delete('{dishes}', [DishesController::class, 'destroy'])->name('plato.destroy');
    });

    #CLIENTE => BACKEND
    Route::prefix('cliente')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('cliente.index');
        Route::post('/', [CustomerController::class, 'store'])->name('clientes.store');
        Route::get('{customer}', [CustomerController::class, 'show'])->name('clientes.show');
        Route::put('{customer}', [CustomerController::class, 'update'])->name('clientes.update');
        Route::delete('{customer}', [CustomerController::class, 'destroy'])->name('clientes.destroy');
    });

    // PROVEEDOR -> BACKEND
    Route::prefix('proveedor')->group(function(){
        Route::get('/', [SupplierController::class, 'index'])->name('proveedor.index');
        Route::post('/', [SupplierController::class, 'store'])->name('proveedores.store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('proveedores.show');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('proveedores.update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('proveedores.destroy');
    });

    #EMPLEADO => BACKEND
    Route::prefix('empleado')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('empleado.index');
        Route::post('/', [EmployeeController::class, 'store'])->name('empleados.store');
        Route::get('{employee}', [EmployeeController::class, 'show'])->name('empleados.show');
        Route::put('{employee}', [EmployeeController::class, 'update'])->name('empleados.update');
        Route::delete('{employee}', [EmployeeController::class, 'destroy'])->name('empleados.destroy');
    });

    #ALMACENES -> BACKEND
    Route::prefix('almacen')->group(function(){
        Route::get('/', [AlmacenController::class, 'index'])->name('almacen.index');
        Route::post('/',[AlmacenController::class, 'store'])->name('almacen.store');
        Route::get('/{almacen}',[AlmacenController::class, 'show'])->name('almacen.show');
        Route::put('/{almacen}',[AlmacenController::class, 'update'])->name('almacen.update');
        Route::delete('/{almacen}',[AlmacenController::class, 'destroy'])->name('almacen.destroy');
    });

    #CATEGORIA -> BACKEND
    Route::prefix('categoria')->group(function(){
        Route::get('/', [CategoryController::class, 'index'])->name('Categoria.index');
        Route::post('/',[CategoryController::class, 'store'])->name('Categoria.store');
        Route::get('/{category}',[CategoryController::class, 'show'])->name('Categoria.show');
        Route::put('/{category}',[CategoryController::class, 'update'])->name('Categoria.update');
        Route::delete('/{category}',[CategoryController::class, 'destroy'])->name('Categoria.destroy');
    });

    #TIPOS DE CLIENTES -> BACKEND
    Route::prefix('tipo_cliente')->group(function(){
        Route::get('/', [ClientTypeController::class, 'index'])->name('Tipos_Clientes.index');
        Route::post('/',[ClientTypeController::class, 'store'])->name('Tipos_Clientes.store');
        Route::get('/{clientType}',[ClientTypeController::class, 'show'])->name('Tipos_Clientes.show');
        Route::put('/{clientType}',[ClientTypeController::class, 'update'])->name('Tipos_Clientes.update');
        Route::delete('/{clientType}',[ClientTypeController::class, 'destroy'])->name('Tipos_Clientes.destroy');
    });
    
    #TIPOS DE EMPLEADOS -> BACKEND
    Route::prefix('tipo_empleado')->group(function(){
        Route::get('/', [EmployeeTypeController::class, 'index'])->name('Tipos_Empleados.index');
        Route::post('/',[EmployeeTypeController::class, 'store'])->name('Tipos_Empleados.store');
        Route::get('/{employeeType}', [EmployeeTypeController::class, 'show'])->name('Tipos_Empleados.show');
        Route::put('/{employeeType}', [EmployeeTypeController::class, 'update'])->name('Tipos_Empleados.update');
        Route::delete('/{employeeType}', [EmployeeTypeController::class, 'destroy'])->name('Tipos_Empleados.destroy');
    });

    #PISOS -> BACKEND
    Route::prefix('piso')->group(function(){
        Route::get('/', [FloorController::class, 'index'])->name('Pisos.index');
        Route::post('/',[FloorController::class, 'store'])->name('Pisos.store');
        Route::get('/{floor}',[FloorController::class, 'show'])->name('Pisos.show');
        Route::put('/{floor}',[FloorController::class, 'update'])->name('Pisos.update');
        Route::delete('/{floor}',[FloorController::class, 'destroy'])->name('Pisos.destroy');
    });

    #PRODUCTOS -> BACKEND
    Route::prefix('producto')->group(function(){
        Route::get('/', [ProductController::class, 'index'])->name('Productos.index');
        Route::post('/',[ProductController::class, 'store'])->name('Productos.store');
        Route::get('/{product}',[ProductController::class, 'show'])->name('Productos.show');
        Route::put('/{product}',[ProductController::class, 'update'])->name('Productos.update');
        Route::delete('/{product}',[ProductController::class, 'destroy'])->name('Productos.destroy');
    });

    #USUARIOS -> BACKEND
    Route::prefix('usuarios')->group(function(){
        Route::get('/', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::post('/',[UsuariosController::class, 'store'])->name('usuarios.store');
        Route::get('/{user}',[UsuariosController::class, 'show'])->name('usuarios.show');
        Route::put('/{user}',[UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/{user}',[UsuariosController::class, 'destroy'])->name('usuarios.destroy');
    });

    #ROLES => BACKEND
    Route::prefix('rol')->group(function () {
        Route::get('/', [RolesController::class, 'index'])->name('rol.index');
        Route::get('/Permisos', [RolesController::class, 'indexPermisos'])->name('rol.indexPermisos');
        Route::post('/', [RolesController::class, 'store'])->name('rol.store');
        Route::get('/{id}', [RolesController::class, 'show'])->name('rol.show');
        Route::put('/{id}', [RolesController::class, 'update'])->name('rol.update');
        Route::delete('/{id}', [RolesController::class, 'destroy'])->name('rol.destroy');
    });
}); 

// Archivos de configuración adicionales
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';