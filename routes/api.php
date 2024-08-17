<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProductoController;
use App\Http\Controllers\CarritoController;
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
// Ruta para obtener productos de Electrónica
Route::get('/productos/electronica', [\App\Http\Controllers\ProductoController::class, 'getProductosElectronica']);
// Ruta para obtener productos de Beterwere
Route::get('/productos/beterwere', [\App\Http\Controllers\ProductoController::class, 'getProductosBeterwere']);


// Rutas protegidas para usuarios autenticados (clientes)
Route::middleware('auth:sanctum')->group(function () {

    // Rutas del carrito (disponibles para clientes)
    Route::post('/carrito', [CarritoController::class, 'store']);
    Route::get('/carrito/{categoria}', [CarritoController::class, 'index']);
    Route::delete('/carrito/{id}', [CarritoController::class, 'destroy']);
    
    // Rutas de pedidos
    Route::post('/pedidos', [\App\Http\Controllers\PedidoController::class, 'store']);
    Route::get('/pedidos', [\App\Http\Controllers\PedidoController::class, 'index']);
    Route::get('/perfil', [\App\Http\Controllers\UsuarioController::class, 'show']);
    Route::put('/perfil', [\App\Http\Controllers\UsuarioController::class, 'update']);
});

// Rutas del administrador
Route::middleware('auth:sanctum')->group(function () {

    // Rutas de productos para administrador
    Route::get('/admin/productos', [AdminProductoController::class, 'index']);
    Route::post('/admin/productos', [AdminProductoController::class, 'store']);
    Route::put('/admin/productos/{id}', [AdminProductoController::class, 'update']);
    Route::delete('/admin/productos/{id}', [AdminProductoController::class, 'destroy']);

    // Rutas de usuarios para administrador
    Route::get('/admin/usuarios', [AdminController::class, 'getUsuarios']);
    Route::put('/admin/usuarios/desactivar/{id}', [AdminController::class, 'desactivar']);
    Route::put('/admin/usuarios/activar/{id}', [AdminController::class, 'activar']);

    // Estadísticas
    Route::get('/admin/estadisticas', [AdminEstadisticasController::class, 'index']);
});