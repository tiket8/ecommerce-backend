<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminPedidoController;
use App\Http\Controllers\AdminProductoController;
use App\Http\Controllers\AdminUsuarioController;
use App\Http\Controllers\AdminEstadisticasController;
use App\Http\Controllers\AuthController;

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

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rutas protegidas para el administrador
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::resource('admin/pedidos', AdminPedidoController::class);
    Route::resource('admin/productos', AdminProductoController::class);
    Route::resource('admin/usuarios', AdminUsuarioController::class);
    Route::get('admin/estadisticas', [AdminEstadisticasController::class, 'index'])->name('admin.estadisticas.index');
});

Route::middleware(['auth:sanctum', 'rol:admin'])->group(function () {
    Route::get('/productos', [AdminProductoController::class, 'index']);
    Route::post('/productos', [AdminProductoController::class, 'store']);
    Route::delete('/productos/{id}', [AdminProductoController::class, 'destroy']);
});