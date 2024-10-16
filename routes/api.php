<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\CollectionController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::prefix('collections')->controller(CollectionController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('{collection}', 'show');
        Route::put('{collection}', 'update');
        Route::delete('{collection}', 'destroy');
    });

    Route::prefix('media')->controller(MediaController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
    });
});
