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

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');




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
Route::resource('inventarios', App\Http\Controllers\InventarioController::class)->middleware('auth');
Route::post('/inventarios/{id}/increase-quantity', [InventarioController::class, 'updateQuantity'])->name('inventarios.updateQuantity');
Route::post('/inventarios/{id}/decrease-quantity', [InventarioController::class, 'decreaseQuantity'])->name('inventarios.decreaseQuantity');
Route::resource('bodegas', App\Http\Controllers\BodegaController::class)->middleware('auth');

Route::resource('movimientos', App\Http\Controllers\MovimientoController::class)->middleware('auth');
Route::resource('ordenes-compras', App\Http\Controllers\OrdenCompraController::class)->middleware('auth');
Route::resource('ordenes', App\Http\Controllers\OrdenCompraController::class)->middleware('auth');
Route::get('/ordenes-compras/entregar/{id}', [OrdenCompraController::class, 'entregar'])->name('ordenes-compras.entregar')->middleware('auth');


Route::resource('detalles-ordenes-compras', App\Http\Controllers\DetalleOrdenCompraController::class)->middleware('auth');
Route::resource('guias-despacho', App\Http\Controllers\GuiaDespachoController::class)->middleware('auth');


Route::resource('facturas', App\Http\Controllers\FacturaController::class)->middleware('auth');
Route::resource('pagos', App\Http\Controllers\PagoController::class)->middleware('auth');
Route::resource('unidades', App\Http\Controllers\UnidadMedidaController::class)->middleware('auth');
Route::get('ordenes-compras/{orden}', [OrdenCompraController::class, 'show'])->name('ordenes-compras.show');
Route::post('ordenes-compras/store-completa', [OrdenCompraController::class, 'storeCompleta'])->name('ordenes-compras.store-completa');

Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index');
Route::post('cajas/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
Route::post('cajas/cerrar/{id}', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
Route::get('/cajas/{id}', [CajaController::class, 'show'])->name('cajas.show');
Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');

