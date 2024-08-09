<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\AdminProductoController;
use App\Http\Controllers\AdminPedidoController;
use App\Http\Controllers\AdminUsuarioController;
use App\Http\Controllers\AdminEstadisticasController;

// Ruta para obtener el usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de autenticación
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

// Ruta para obtener las ofertas (no requiere autenticación)
Route::get('ofertas', [OfertaController::class, 'index']);

// Rutas protegidas por autenticación para admin
Route::middleware('auth:sanctum')->group(function () {
    // Pedidos
    Route::get('/admin/pedidos', [AdminPedidoController::class, 'index']);
    Route::get('/admin/pedidos/{id}', [AdminPedidoController::class, 'show']);
    Route::put('/admin/pedidos/{id}', [AdminPedidoController::class, 'update']);

    // Productos
    Route::get('/admin/productos', [AdminProductoController::class, 'index']);
    Route::post('/admin/productos', [AdminProductoController::class, 'store']);
    Route::put('/admin/productos/{id}', [AdminProductoController::class, 'update']);
    Route::delete('/admin/productos/{id}', [AdminProductoController::class, 'destroy']);

    // Usuarios
    Route::get('/admin/usuarios', [AdminUsuarioController::class, 'index']);
    Route::delete('/admin/usuarios/{id}', [AdminUsuarioController::class, 'destroy']);

    // Estadísticas
    Route::get('/admin/estadisticas', [AdminEstadisticasController::class, 'index']);
});
