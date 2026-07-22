<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\OrderController as AdminOrderController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\front\AccountController;
use App\Http\Controllers\front\OrderController;
use App\Http\Controllers\front\ProductController as FrontProductController;
use App\Http\Controllers\SizeController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'authenticate']);
Route::post('/register', [AccountController::class, 'register']);
Route::post('/authenticate', [AccountController::class, 'authenticate']);

Route::group(['middleware' => ['auth:sanctum', 'checkAdminRole']], function () {
    Route::resource('/categories', CategoryController::class);
    Route::resource('/brands', BrandController::class);
    Route::get('/sizes', [SizeController::class, 'index']);
    Route::apiResource('/products', ProductController::class);
    Route::post('/temp-images', [TempImageController::class, 'store']);
    Route::post('/save-product-image', [ProductController::class, 'saveProductImages']);
    Route::post('/change-product-default-image', [ProductController::class, 'updateDefaultImage']);
    Route::delete('/delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);
    Route::get('/latest-products', [FrontProductController::class, 'getLatestProducts']);
    Route::get('/featured-products', [FrontProductController::class, 'getFeaturedProducts']);
    Route::get('/get-products', [FrontProductController::class, 'getProducts']);
    Route::get('/get-brands', [FrontProductController::class, 'getBrands']);
    Route::get('/get-categories', [FrontProductController::class, 'getCategories']);
    Route::get('/get-product/{id}', [FrontProductController::class, 'getProductDetails']);
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
});
Route::group(['middleware' => ['auth:sanctum', 'checkUserRole']], function () {
    Route::post('/save-order', [OrderController::class, 'saveOrder']);
    Route::post('/order-details/{id}', [OrderController::class, 'getOrderDetails']);
});
