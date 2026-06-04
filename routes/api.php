<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\Front\ProductController as FrontProductController;
use App\Http\Controllers\SizeController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'authenticate']);

Route::group(['middleware:auth:santum'], function () {
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
});
