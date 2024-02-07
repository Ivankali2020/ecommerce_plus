<?php

use App\Http\Controllers\API\User\AuthController;
use App\Http\Controllers\API\User\ProductController;
use App\Http\Controllers\API\User\UserAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Auth
Route::post('/seller/{sellerId}/register', [AuthController::class, 'register']);
Route::post('/seller/{sellerId}/login', [AuthController::class, 'login']);

//brands
Route::get('/seller/{sellerId}/brands', [ProductController::class, 'getBrands']);

//category
Route::get('/seller/{sellerId}/categories', [ProductController::class, 'getCategories']);

//products
Route::get('/seller/{sellerId}/products', [ProductController::class, 'getProducts']);
Route::get('/seller/{sellerId}/product/{productId}', [ProductController::class, 'getProductDetail']);


Route::middleware(['auth:sanctum', 'checkUserSeller'])->group(function () {
    //Account
    Route::get('/seller/{sellerId}/me', [UserAccountController::class, 'getMyAccount']);
    Route::post('/seller/{sellerId}/me/logout', [AuthController::class, 'logout']);
    Route::post('/seller/{sellerId}/me/delete', [UserAccountController::class, 'deleteAccount']);
    //change password
    //update fcm token


});
