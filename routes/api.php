<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'authenticate']);

Route::group(['middleware:auth:santum'], function () {
    Route::resource('/categories', CategoryController::class);
    Route::resource('/brands', BrandController::class);
});
