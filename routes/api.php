<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post('/users', [UserController::class,'register']);
Route::post('/users/login', [UserController::class,'login']);

#route yang membutuhkan login
Route::middleware(\App\Http\Middleware\ApiAuthMiddleware::class)->group(function () {
    Route::get('/users/current', [UserController::class,'get']);
    Route::patch('/users/current', [UserController::class,'update']);
    Route::delete('/users/logout', [UserController::class,'logout']);

    Route::get('/products', [ProductController::class,'index']);
    Route::post('/products/add', [ProductController::class, 'add']);
    Route::patch('/products/update/{id}', [ProductController::class, 'update']);
    Route::delete('/products/delete/{id}', [ProductController::class, 'delete']);
});
