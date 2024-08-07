<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\AdminProductoController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de autenticación
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

// Ruta para obtener las ofertas
Route::get('ofertas', [OfertaController::class, 'index']);

//Rutas admin
Route::middleware('auth:sanctum')->group(function () {
    // Pedidos
    Route::get('/admin/pedidos', [AdminController::class, 'getPedidos']);
    Route::get('/admin/pedidos/{id}', [AdminController::class, 'showPedido']);
    Route::put('/admin/pedidos/{id}', [AdminController::class, 'updatePedido']);

    // Productos
    Route::get('/admin/productos', [AdminProductoController::class, 'index']);
    Route::post('/admin/productos', [AdminProductoController::class, 'store']);
    Route::put('/admin/productos/{id}', [AdminProductoController::class, 'update']);
    Route::delete('/admin/productos/{id}', [AdminProductoController::class, 'destroy']);

    // Usuarios
    Route::get('/admin/usuarios', [AdminController::class, 'getUsuarios']);
    Route::delete('/admin/usuarios/{id}', [AdminController::class, 'destroyUsuario']);

    // Estadísticas
    Route::get('/admin/estadisticas', [AdminController::class, 'getEstadisticas']);
});