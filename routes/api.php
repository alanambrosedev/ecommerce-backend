<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\CategoryController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'authenticate']);

Route::group(['middleware:auth:santum'], function () {
    Route::get('/categories', [CategoryController::class, 'index']);
});
