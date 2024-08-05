<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('admin/pedidos', AdminPedidoController::class);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('admin/productos', AdminProductoController::class);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('admin/usuarios', AdminUsuarioController::class);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/estadisticas', [AdminEstadisticasController::class, 'index'])->name('admin.estadisticas.index');
});
