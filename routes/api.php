<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('test_token', function (Request $request) {
    return response()->json([
        'raw_cookie_header' => $request->header('Cookie'),
        'parsed_cookie_value' => $request->cookie('access_token'),
    ]);
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Guests can view categories and products, but cannot modify them
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires valid Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
<<<<<<< HEAD
=======


    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
>>>>>>> 3b21b59f4a9cf7d24971ce885aef9cbe15150853

    // Authenticated Write Actions (Create, Update, Delete)
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);

    // Orders are fully private (a user must be logged in to buy or see history)
    Route::apiResource('orders', OrderController::class);



});
