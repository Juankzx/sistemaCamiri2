<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

Route::resource('categorias', App\Http\Controllers\CategoriaController::class)->middleware('auth');
Route::resource('proveedores', App\Http\Controllers\ProveedorController::class)->middleware('auth');
Route::resource('ivas', App\Http\Controllers\IvaController::class)->middleware('auth');
Route::resource('sucursales', App\Http\Controllers\SucursaleController::class)->middleware('auth');
Route::resource('metodos-pagos', App\Http\Controllers\MetodosPagoController::class)->middleware('auth');
Route::resource('cajas', App\Http\Controllers\CajaController::class)->middleware('auth');
Route::resource('productos', App\Http\Controllers\ProductoController::class)->middleware('auth');
Route::resource('inventarios', App\Http\Controllers\InventarioController::class)->middleware('auth');
Route::resource('pedidos', App\Http\Controllers\PedidoController::class)->middleware('auth');
Route::resource('ventas', App\Http\Controllers\VentaController::class)->middleware('auth');
Route::resource('detalles-venta', App\Http\Controllers\DetallesVentumController::class)->middleware('auth');
Route::resource('pagos-proveedors', App\Http\Controllers\PagosProveedorController::class)->middleware('auth');

