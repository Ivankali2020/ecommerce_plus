<?php

use App\Http\Controllers\API\User\AuthController;
use App\Http\Controllers\API\User\ProductController;
use App\Http\Controllers\API\User\UserAccountController;
use Illuminate\Support\Facades\Route;

Route::prefix('/seller/{sellerId}')->group(function ($sellerId) {
    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // brands
    Route::get('/brands', [ProductController::class, 'getBrands']);

    // category
    Route::get('/categories', [ProductController::class, 'getCategories']);

    // products
    Route::get('/products', [ProductController::class, 'getProducts']);
    Route::get('/product/{productId}', [ProductController::class, 'getProductDetail']);

    Route::middleware(['auth:sanctum', 'checkUserSeller'])->group(function () {
        // Account
        Route::get('/me', [UserAccountController::class, 'getMyAccount']);
        Route::post('/me/logout', [AuthController::class, 'logout']);
        Route::post('/me/delete', [UserAccountController::class, 'deleteAccount']);
        // change password
        // update fcm token

    });
});
