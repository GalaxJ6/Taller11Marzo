<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('jwt.auth');

Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/ventas', [VentaController::class, 'registrarVenta']);
    Route::get('/productos', [ProductoController::class, 'consultarProductos']);
    Route::post('/productos', [ProductoController::class, 'crearProducto']);
});