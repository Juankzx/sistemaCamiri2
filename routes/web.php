<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SucursaleController;
use App\Http\Controllers\ProveedoreController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\DetalleOrdenCompraController;
use App\Http\Controllers\GuiaDespachoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('productos', ProductoController::class);
    Route::resource('inventarios', InventarioController::class);
});


Route::resource('home', App\Http\Controllers\HomeController::class)->middleware('auth');
Route::resource('categorias', App\Http\Controllers\CategoriaController::class)->middleware('auth');
Route::resource('proveedores', App\Http\Controllers\ProveedoreController::class)->middleware('auth');
Route::resource('ivas', App\Http\Controllers\IvaController::class)->middleware('auth');
Route::resource('sucursales', App\Http\Controllers\SucursaleController::class)->middleware('auth');
Route::resource('metodos-pagos', App\Http\Controllers\MetodosPagoController::class)->middleware('auth');
Route::resource('cajas', App\Http\Controllers\CajaController::class)->middleware('auth');
Route::resource('productos', App\Http\Controllers\ProductoController::class)->middleware('auth');
Route::resource('pedidos', App\Http\Controllers\PedidoController::class)->middleware('auth');
Route::resource('ventas', App\Http\Controllers\VentaController::class)->middleware('auth');
Route::resource('detalles-venta', App\Http\Controllers\DetallesVentumController::class)->middleware('auth');
Route::resource('inventarios', InventarioController::class)->middleware('auth');
Route::post('/inventarios/incrementar/{id}', [InventarioController::class, 'incrementarBodega'])->name('inventarios.incrementar');
Route::post('/inventarios/decrementar/{id}', [InventarioController::class, 'decrementarBodega'])->name('inventarios.decrementar');
Route::post('/inventarios/transferir/{id}', [InventarioController::class, 'transferirASucursal'])->name('inventarios.transferir');
Route::get('/api/check-producto-bodega-general/{productoId}', [OrdenCompraController::class, 'checkProductoEnBodegaGeneral']);



Route::resource('bodegas', App\Http\Controllers\BodegaController::class)->middleware('auth');

Route::resource('movimientos', App\Http\Controllers\MovimientoController::class)->middleware('auth');
Route::resource('ordenes-compras', App\Http\Controllers\OrdenCompraController::class)->middleware('auth');
Route::resource('ordenes', App\Http\Controllers\OrdenCompraController::class)->middleware('auth');
Route::get('/ordenes-compras/entregar/{id}', [OrdenCompraController::class, 'entregar'])->name('ordenes-compras.entregar')->middleware('auth');
Route::get('/api/ordenes-compra/{id}', [GuiaDespachoController::class, 'getOrdenCompraDetails']);


Route::resource('detalles-ordenes-compras', App\Http\Controllers\DetalleOrdenCompraController::class)->middleware('auth');
Route::resource('guias-despacho', App\Http\Controllers\GuiaDespachoController::class)->middleware('auth');
// Ruta API para obtener detalles de la guía de despacho
Route::get('/api/guias-despacho/{id}/detalles', [GuiaDespachoController::class, 'getDetalles']);
Route::get('guias-despacho/{id}/detalles', [GuiaDespachoController::class, 'getDetalles']);



Route::resource('facturas', App\Http\Controllers\FacturaController::class)->middleware('auth');
Route::resource('pagos', App\Http\Controllers\PagoController::class)->middleware('auth');
Route::get('pagos/create', [PagoController::class, 'create'])->name('pagos.create');
Route::get('/api/facturas/{id}/detalles', [FacturaController::class, 'getDetalles']);



Route::resource('unidades', App\Http\Controllers\UnidadMedidaController::class)->middleware('auth');
Route::get('ordenes-compras/{orden}', [OrdenCompraController::class, 'show'])->name('ordenes-compras.show');
Route::post('ordenes-compras/store-completa', [OrdenCompraController::class, 'storeCompleta'])->name('ordenes-compras.store-completa');

Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index');
Route::post('cajas/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
Route::post('cajas/cerrar/{id}', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
Route::get('/cajas/{id}', [CajaController::class, 'show'])->name('cajas.show');
Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');
Route::get('productos/sucursal/{id}', [App\Http\Controllers\VentaController::class, 'getProductosPorSucursal']);
Route::get('productos/sucursal/{sucursalId}', [VentaController::class, 'productosPorSucursal']);
Route::get('/productos/sucursal/{sucursal}', [ProductoController::class, 'getProductosPorSucursal']);






