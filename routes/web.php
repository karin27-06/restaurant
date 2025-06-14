<?php

use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Reportes\AlmacenPDFController;
use App\Http\Controllers\Reportes\EmployeeTypePDFController;
use App\Http\Controllers\Reportes\FloorPDFController;
use App\Http\Controllers\Reportes\CategoryPDFController;
use App\Http\Controllers\Reportes\ProductPDFController;
use App\Http\Controllers\Reportes\ClientTypePDFController;
use App\Http\Controllers\Reportes\DishPDFController;
use App\Http\Controllers\Reportes\InputPDFController;
use App\Http\Controllers\Reportes\TablePDFController;
use App\Http\Controllers\Reportes\PresentationPDFController;
use App\Http\Controllers\Reportes\CustomerPDFController;
use App\Http\Controllers\Reportes\EmployeePDFController;
use App\Http\Controllers\Reportes\SupplierPDFController;
use App\Http\Controllers\Reportes\AreaPDFController;
use App\Http\Controllers\Api\InputController;
use App\Http\Controllers\Api\MovementInputController;
use App\Http\Controllers\Api\TablesController;
use App\Http\Controllers\Api\AreasController;
use App\Http\Controllers\Api\CajaController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\PresentationController;
use App\Http\Controllers\Api\ConsultasDni;
use App\Http\Controllers\Api\ConsultasId;
use App\Http\Controllers\Api\DishesController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\UsuariosController;
use App\Http\Controllers\Api\ClientTypeController;
use App\Http\Controllers\Api\EmployeeTypeController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\MovementInputDetailController;
use App\Http\Controllers\Web\AlmacenWebController;
use App\Http\Controllers\Web\AreasWebController;
use App\Http\Controllers\Web\CajaWebController;
use App\Http\Controllers\Web\CategoryWebController;
use App\Http\Controllers\Web\SupplierWebController;
use App\Http\Controllers\Web\PresentationWebController;
use App\Http\Controllers\Web\ClientTypeWebController;
use App\Http\Controllers\Web\EmployeeTypeWebController;
use App\Http\Controllers\Web\CustomerWebController;
use App\Http\Controllers\Web\DishesWebController;
use App\Http\Controllers\Web\EmployeeWebController;
use App\Http\Controllers\Web\FloorWebController;
use App\Http\Controllers\Web\InputWebController;
use App\Http\Controllers\Web\MovementInputsWebController;
use App\Http\Controllers\Web\ProductWebController;
use App\Http\Controllers\Web\TableWebController;
use App\Http\Controllers\Web\UsuarioWebController;
use App\Http\Controllers\Web\MovementInputDetailWebController;
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
    Route::get('/presentaciones', [PresentationWebController::class, 'index'])->name('index.view');
    Route::get('/clientes', [CustomerWebController::class, 'index'])->name('index.view');
    Route::get('/empleados', [EmployeeWebController::class, 'index'])->name('index.view');
    Route::get('/tipo_clientes', [ClientTypeWebController::class, 'index'])->name('index.view');
    Route::get('/tipo_empleados', [EmployeeTypeWebController::class, 'index'])->name('index.view');
    Route::get('/pisos', [FloorWebController::class, 'index'])->name('index.view');
    Route::get('/productos', [ProductWebController::class, 'index'])->name('index.view');
    Route::get('/cajas', [CajaWebController::class, 'index'])->name('index.view');
    Route::get('/usuario', [UsuarioWebController::class, 'index'])->name('index.view');
    Route::get('/areas', [AreasWebController::class, 'index'])->name('index.view');
    Route::get('/platos', [DishesWebController::class, 'index'])->name('index.view');
    Route::get('/mesas', [TableWebController::class, 'index'])->name('index.view');
    Route::get('/insumos', [InputWebController::class, 'index'])->name('index.view');
    Route::get('/insumos/movimientos', action: [MovementInputsWebController::class, 'index'])->name('index.view');
    Route::get('/roles', [UsuarioWebController::class, 'roles'])->name('roles.view');
    Route::get(uri: '/insumos/movimientos/detalles/{id}', action: [MovementInputDetailWebController::class, 'index'])->name('index.view');


    #CONSULTA  => BACKEND
    Route::get('/consulta/{dni}', [ConsultasDni::class, 'consultar'])->name('consultar.dni');
    Route::get('/user-id', [ConsultasId::class, 'getUserId'])->middleware('auth:api');

    #AREAS => BACKEND
    Route::prefix('area')->group(function () {
        Route::get('/', [AreasController::class, 'index'])->name('area.index');
        Route::post('/', [AreasController::class, 'store'])->name('area.store');
        Route::get('{areas}', [AreasController::class, 'show'])->name('area.show');
        Route::put('{areas}', [AreasController::class, 'update'])->name('area.update');
        Route::delete('{areas}', [AreasController::class, 'destroy'])->name('area.destroy');
    });
    #MESAS => BACKEND
    Route::prefix('mesa')->group(function () {
        Route::get('/', action: [TablesController::class, 'index'])->name('mesas.index');
        Route::post('/', [TablesController::class, 'store'])->name('mesas.store');
        Route::get('{table}', [TablesController::class, 'show'])->name('mesas.show');
        Route::put('{table}', [TablesController::class, 'update'])->name('mesas.update');
        Route::delete('{table}', [TablesController::class, 'destroy'])->name('mesas.destroy');
    });
    #INSUMOS => BACKEND
    Route::prefix('insumo')->group(function () {
        Route::get('/', action: [InputController::class, 'index'])->name('inputs.index');
        Route::post('/', [InputController::class, 'store'])->name('inputs.store');
        Route::get('{input}', [InputController::class, 'show'])->name('inputs.show');
        Route::put('{input}', [InputController::class, 'update'])->name('inputs.update');
        Route::delete('{input}', [InputController::class, 'destroy'])->name('inputs.destroy');
    });


    // INSUMOS -> MOVIMIENTOS (BACKEND)
    Route::prefix('insumos/movimiento')->group(function () {
        Route::get('/', [MovementInputController::class, 'index'])->name('movimientos.index');
        Route::post('/', [MovementInputController::class, 'store'])->name('movimientos.store');
        Route::get('{movementInput}', [MovementInputController::class, 'show'])->name('movimientos.show');
        Route::put('{movementInput}', [MovementInputController::class, 'update'])->name('movimientos.update');
        Route::delete('{movementInput}', [MovementInputController::class, 'destroy'])->name('movimientos.destroy');
    });
    // INSUMOS -> MOVIMIENTOS -> DETALLES (BACKEND)
    Route::prefix('insumos/movimientos/detalle')->group(function () {
        Route::get('{id}', [MovementInputDetailController::class, 'index'])->name('movimientosinput.index');
        Route::delete('{id}', [MovementInputDetailController::class, 'destroy'])->name('movimientosinput.destroy');
        Route::put('{MovementInputDetail}', [MovementInputDetailController::class, 'update'])->name('movimientosinput.update');
        Route::post('/', [MovementInputDetailController::class, 'store'])->name('movimientosinput.store');
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
    Route::prefix('proveedor')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('proveedor.index');
        Route::post('/', [SupplierController::class, 'store'])->name('proveedores.store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('proveedores.show');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('proveedores.update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('proveedores.destroy');
    });

    // PRESENTACION -> BACKEND
    Route::prefix('presentacion')->group(function () {
        Route::get('/', [PresentationController::class, 'index'])->name('presentacion.index');
        Route::post('/', [PresentationController::class, 'store'])->name('presentaciones.store');
        Route::get('/{presentation}', [PresentationController::class, 'show'])->name('presentaciones.show');
        Route::put('/{presentation}', [PresentationController::class, 'update'])->name('presentaciones.update');
        Route::delete('/{presentation}', [PresentationController::class, 'destroy'])->name('presentaciones.destroy');
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
    Route::prefix('almacen')->group(function () {
        Route::get('/', [AlmacenController::class, 'index'])->name('almacen.index');
        Route::post('/', [AlmacenController::class, 'store'])->name('almacen.store');
        Route::get('/{almacen}', [AlmacenController::class, 'show'])->name('almacen.show');
        Route::put('/{almacen}', [AlmacenController::class, 'update'])->name('almacen.update');
        Route::delete('/{almacen}', [AlmacenController::class, 'destroy'])->name('almacen.destroy');
    });

    #CATEGORIA -> BACKEND
    Route::prefix('categoria')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('Categoria.index');
        Route::post('/', [CategoryController::class, 'store'])->name('Categoria.store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('Categoria.show');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('Categoria.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('Categoria.destroy');
    });

    #TIPOS DE CLIENTES -> BACKEND
    Route::prefix('tipo_cliente')->group(function () {
        Route::get('/', [ClientTypeController::class, 'index'])->name('Tipos_Clientes.index');
        Route::post('/', [ClientTypeController::class, 'store'])->name('Tipos_Clientes.store');
        Route::get('/{clientType}', [ClientTypeController::class, 'show'])->name('Tipos_Clientes.show');
        Route::put('/{clientType}', [ClientTypeController::class, 'update'])->name('Tipos_Clientes.update');
        Route::delete('/{clientType}', [ClientTypeController::class, 'destroy'])->name('Tipos_Clientes.destroy');
    });

    #TIPOS DE EMPLEADOS -> BACKEND
    Route::prefix('tipo_empleado')->group(function () {
        Route::get('/', [EmployeeTypeController::class, 'index'])->name('Tipos_Empleados.index');
        Route::post('/', [EmployeeTypeController::class, 'store'])->name('Tipos_Empleados.store');
        Route::get('/{employeeType}', [EmployeeTypeController::class, 'show'])->name('Tipos_Empleados.show');
        Route::put('/{employeeType}', [EmployeeTypeController::class, 'update'])->name('Tipos_Empleados.update');
        Route::delete('/{employeeType}', [EmployeeTypeController::class, 'destroy'])->name('Tipos_Empleados.destroy');
    });

    #PISOS -> BACKEND
    Route::prefix('piso')->group(function () {
        Route::get('/', [FloorController::class, 'index'])->name('Pisos.index');
        Route::post('/', [FloorController::class, 'store'])->name('Pisos.store');
        Route::get('/{floor}', [FloorController::class, 'show'])->name('Pisos.show');
        Route::put('/{floor}', [FloorController::class, 'update'])->name('Pisos.update');
        Route::delete('/{floor}', [FloorController::class, 'destroy'])->name('Pisos.destroy');
    });

    #PRODUCTOS -> BACKEND
    Route::prefix('producto')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('Productos.index');
        Route::post('/', [ProductController::class, 'store'])->name('Productos.store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('Productos.show');
        Route::put('/{product}', [ProductController::class, 'update'])->name('Productos.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('Productos.destroy');
    });

    #CAJAS -> BACKEND
    Route::prefix('caja')->group(function () {
        Route::get('/', [CajaController::class, 'index'])->name('Cajas.index');
        Route::post('/', [CajaController::class, 'store'])->name('Cajas.store');
        Route::get('/{caja}', [CajaController::class, 'show'])->name('Cajas.show');
        Route::put('/{caja}', [CajaController::class, 'update'])->name('Cajas.update');
        Route::delete('/{caja}', [CajaController::class, 'destroy'])->name('Cajas.destroy');
    });

    #USUARIOS -> BACKEND
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::post('/', [UsuariosController::class, 'store'])->name('usuarios.store');
        Route::get('/{user}', [UsuariosController::class, 'show'])->name('usuarios.show');
        Route::put('/{user}', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/{user}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
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
    Route::prefix('panel/reports')->group(function () {
        #EXPORTACION Y IMPORTACION CATEGORIAS
        Route::get('/export-excel-categories', [CategoryController::class, 'exportExcel'])->name('export-excel-categories');
        Route::get('/export-pdf-categories', [CategoryPDFController::class, 'exportPDF'])->name('export-pdf-categories');
        // Ruta para importar desde Excel
        Route::post('/import-excel-categories', [CategoryController::class, 'importExcel'])->name('import-excel-categories');

        #EXPORTACION Y IMPORTACION ALMACENES
        Route::get('/export-excel-almacenes', [AlmacenController::class, 'exportExcel'])->name('export-excel-almacenes');
        Route::get('/export-pdf-almacenes', [AlmacenPDFController::class, 'exportPDF'])->name('export-pdf-almacenes');
        // Ruta para importar desde Excel
        Route::post('/import-excel-almacenes', [AlmacenController::class, 'importExcel'])->name('import-excel-almacenes');

        #EXPORTACION Y IMPORTACION PROVEEDORES
        Route::get('/export-excel-suppliers', [SupplierController::class, 'exportExcel'])->name('export-excel-suppliers');
        Route::get('/export-pdf-suppliers', [SupplierPDFController::class, 'exportPDF'])->name('export-pdf-suppliers');
        // Ruta para importar desde Excel
        Route::post('/import-excel-suppliers', [SupplierController::class, 'importExcel'])->name('import-excel-suppliers');

        #EXPORTACION Y IMPORTACION PISOS
        Route::get('/export-excel-floors', [FloorController::class, 'exportExcel'])->name('export-excel-floors');
        Route::get('/export-pdf-floors', [FloorPDFController::class, 'exportPDF'])->name('export-pdf-floors');
        // Ruta para importar desde Excel
        Route::post('/import-excel-floors', [FloorController::class, 'importExcel'])->name('import-excel-floors');

        #EXPORTACION Y IMPORTACION AREAS
        Route::get('/export-excel-areas', [AreasController::class, 'exportExcel'])->name('export-excel-areas');
        Route::get('/export-pdf-areas', [AreaPDFController::class, 'exportPDF'])->name('export-pdf-areas');
        // Ruta para importar desde Excel
        Route::post('/import-excel-areas', [AreasController::class, 'importExcel'])->name('import-excel-areas');

        #EXPORTACION Y IMPORTACION PRODUCTOS
        Route::get('/export-excel-products', [ProductController::class, 'exportExcel'])->name('export-excel-products');
        Route::get('/export-pdf-products', [ProductPDFController::class, 'exportPDF'])->name('export-pdf-products');
        // Ruta para importar desde Excel
        Route::post('/import-excel-products', [ProductController::class, 'importExcel'])->name('import-excel-products');

        #EXPORTACION Y IMPORTACION INSUMOS
        Route::get('/export-excel-inputs', [InputController::class, 'exportExcel'])->name('export-excel-inputs');
        Route::get('/export-pdf-inputs', [InputPDFController::class, 'exportPDF'])->name('export-pdf-inputs');
        // Ruta para importar desde Excel
        Route::post('/import-excel-inputs', [InputController::class, 'importExcel'])->name('import-excel-inputs');

        #EXPORTACION Y IMPORTACION PLATOS
        Route::get('/export-excel-dishes', [DishesController::class, 'exportExcel'])->name('export-excel-dishes');
        Route::get('/export-pdf-dishes', [DishPDFController::class, 'exportPDF'])->name('export-pdf-dishes');
        // Ruta para importar desde Excel
        Route::post('/import-excel-dishes', [DishesController::class, 'importExcel'])->name('import-excel-dishes');

        #EXPORTACION Y IMPORTACION MESAS
        Route::get('/export-excel-tables', [TablesController::class, 'exportExcel'])->name('export-excel-tables');
        Route::get('/export-pdf-tables', [TablePDFController::class, 'exportPDF'])->name('export-pdf-tables');
        // Ruta para importar desde Excel
        Route::post('/import-excel-tables', [TablesController::class, 'importExcel'])->name('import-excel-tables');

        #EXPORTACION Y IMPORTACION PRESENTACIONES
        Route::get('/export-excel-presentations', [PresentationController::class, 'exportExcel'])->name('export-excel-presentations');
        Route::get('/export-pdf-presentations', [PresentationPDFController::class, 'exportPDF'])->name('export-pdf-presentations');
        // Ruta para importar desde Excel
        Route::post('/import-excel-presentations', [PresentationController::class, 'importExcel'])->name('import-excel-presentations');

        #EXPORTACION Y IMPORTACION TIPOS DE CLIENTES
        Route::get('/export-excel-clientTypes', [ClientTypeController::class, 'exportExcel'])->name('export-excel-clientTypes');
        Route::get('/export-pdf-clientTypes', [ClientTypePDFController::class, 'exportPDF'])->name('export-pdf-clientTypes');
        // Ruta para importar desde Excel
        Route::post('/import-excel-clientTypes', [ClientTypeController::class, 'importExcel'])->name('import-excel-clientTypes');

        #EXPORTACION Y IMPORTACION TIPOS DE EMPLEADOS
        Route::get('/export-excel-employeeTypes', [EmployeeTypeController::class, 'exportExcel'])->name('export-excel-employeeTypes');
        Route::get('/export-pdf-employeeTypes', [EmployeeTypePDFController::class, 'exportPDF'])->name('export-pdf-employeeTypes');
        // Ruta para importar desde Excel
        Route::post('/import-excel-employeeTypes', [EmployeeTypeController::class, 'importExcel'])->name('import-excel-employeeTypes');

        #EXPORTACION Y IMPORTACION CLIENTES
        Route::get('/export-excel-customers', [CustomerController::class, 'exportExcel'])->name('export-excel-customers');
        Route::get('/export-pdf-customers', [CustomerPDFController::class, 'exportPDF'])->name('export-pdf-customers');
        // Ruta para importar desde Excel
        Route::post('/import-excel-customers', [CustomerController::class, 'importExcel'])->name('import-excel-customers');

        #EXPORTACION Y IMPORTACION EMPLEADOS
        Route::get('/export-excel-employees', [EmployeeController::class, 'exportExcel'])->name('export-excel-employees');
        Route::get('/export-pdf-employees', [EmployeePDFController::class, 'exportPDF'])->name('export-pdf-employees');
        // Ruta para importar desde Excel
        Route::post('/import-excel-employees', [EmployeeController::class, 'importExcel'])->name('import-excel-employees');
    });
});

// Archivos de configuración adicionales
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
