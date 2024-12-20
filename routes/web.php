<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SucursaleController;
use App\Http\Controllers\ProveedoreController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\DetalleOrdenCompraController;
use App\Http\Controllers\DetalleGuiaDespachoController;
use App\Http\Controllers\GuiaDespachoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\MetodosPagoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CajeroLoginController;


// Ruta pública de login
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

// Ruta para manejar el POST del formulario de login
Route::post('login', [LoginController::class, 'login'])->name('login.post');

// Ruta para logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Ruta pública de registro
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->middleware('guest')->name('register');
Route::post('register', [RegisterController::class, 'register'])->middleware('guest')->name('register.post');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('productos', ProductoController::class);
    Route::resource('inventarios', InventarioController::class);


// Rutas de recursos
Route::resource('categorias', CategoriaController::class)->middleware('auth');
Route::resource('proveedores', ProveedoreController::class)->middleware('auth');
Route::resource('sucursales', SucursaleController::class)->middleware('auth');
Route::resource('metodos-pagos', MetodosPagoController::class)->middleware('auth');
Route::resource('bodegas', BodegaController::class)->middleware('auth');
Route::resource('movimientos', MovimientoController::class)->middleware('auth');
Route::resource('facturas', FacturaController::class)->middleware('auth');
Route::resource('unidades', \App\Http\Controllers\UnidadMedidaController::class)->middleware('auth');

// Modulo Cajas
Route::resource('cajas', CajaController::class)->middleware('auth');
Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index');
Route::post('cajas/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
Route::post('cajas/cerrar/{id}', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
Route::get('/cajas/{id}/imprimir-boleta', [CajaController::class, 'imprimirBoleta'])->name('cajas.imprimir_boleta');

// Modulo Ventas
Route::resource('ventas', VentaController::class)->middleware('auth');
Route::resource('detalles-venta', DetalleOrdenCompraController::class)->middleware('auth');
Route::get('/ventas/{id}/print', [VentaController::class, 'print'])->name('ventas.print');


// Modulo Producto
Route::resource('productos', ProductoController::class)->middleware('auth');
Route::get('/api/check-producto-bodega-general/{productoId}', [OrdenCompraController::class, 'checkProductoEnBodegaGeneral']);

// Modulo Inventario
Route::post('/inventarios/incrementar/{id}', [InventarioController::class, 'incrementarBodega'])->name('inventarios.incrementar');
Route::post('/inventarios/decrementar/{id}', [InventarioController::class, 'decrementarBodega'])->name('inventarios.decrementar');
Route::post('/inventarios/transferir/{id}', [InventarioController::class, 'transferirASucursal'])->name('inventarios.transferir');
Route::post('/inventarios/transferirmasivo', [InventarioController::class, 'transferirMasivo'])
    ->name('inventarios.transferirmasivo')
    ->middleware('auth');
Route::post('/inventarios/storeMultiple', [InventarioController::class, 'storeMultiple'])->name('inventarios.storeMultiple');


// Modulo Solicitud Pedido
Route::resource('ordenes-compras', OrdenCompraController::class)->middleware('auth');
Route::get('/ordenes-compras/{id}/entregar', [OrdenCompraController::class, 'entregar'])->name('ordenes-compras.entregar');
Route::get('ordenes-compras/{id}/pdf', [OrdenCompraController::class, 'exportarPdf'])->name('ordenes-compras.exportarPdf');
Route::get('/api/ordenes-compra/{id}', [GuiaDespachoController::class, 'getOrdenCompraDetails']);
Route::resource('detalles-ordenes-compras', DetalleOrdenCompraController::class)->middleware('auth');
Route::post('ordenes-compras/store-completa', [OrdenCompraController::class, 'storeCompleta'])->name('ordenes-compras.store-completa');


// Modulo Guia Despacho
Route::resource('guias-despacho', GuiaDespachoController::class)->middleware('auth');
Route::get('/api/guias-despacho/{id}/detalles', [GuiaDespachoController::class, 'getDetalles']);
Route::get('guias-despacho/{id}', [GuiaDespachoController::class, 'show'])->name('guias-despacho.show');


//Modulo Facturas
Route::get('/api/facturas/{id}/detalles', [FacturaController::class, 'getDetalles']);

// Modulo Pagos
Route::resource('pagos', PagoController::class)->middleware('auth');
Route::get('/api/pagos/{id}/detalles', [PagoController::class, 'getFacturaDetalles']);

// Modulo Reportes
Route::prefix('reportes')->middleware('auth')->group(function () {
    Route::get('/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::get('/compras', [ReporteController::class, 'compras'])->name('reportes.compras');
    Route::get('/financieros', [ReporteController::class, 'financieros'])->name('reportes.financieros');
});

// Modulo Roles
Route::resource('users', UserController::class)->middleware(['auth']);
Route::get('asignar-rol', [UserController::class, 'mostrarFormularioAsignarRol'])->name('asignar-rol');
Route::post('asignar-rol', [UserController::class, 'asignarRol'])->name('asignar-rol.store');
Route::post('remover-rol', [UserController::class, 'removerRol'])->name('asignar-rol.destroy');

Route::post('/cajeros/login', [CajeroLoginController::class, 'login'])->name('cajeros.login');

});
//Route::post('/login-with-pin', [AuthController::class, 'loginWithPin'])->name('loginWithPin');
Route::post('/login-with-pin', [\App\Http\Controllers\Auth\LoginController::class, 'loginWithPin'])->name('loginWithPin');
Route::get('/test-login-with-pin', function () {
    \Log::info('Ruta /test-login-with-pin fue accedida.');
    return response()->json(['message' => 'Esta ruta es solo para pruebas.']);

    


});
Route::get('/obtener-datos-ventas/{periodo}', [HomeController::class, 'obtenerDatosVentas'])
     ->name('obtener-datos-ventas')
     ->middleware('auth');


