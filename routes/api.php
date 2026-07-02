<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Resources\UserResource;

use App\Http\Controllers\Api\NasaProxyController;

use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\UserProfileController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/google/url', [AuthController::class, 'googleUrl']);
    Route::post('/google/callback', [AuthController::class, 'googleCallback']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    Route::get('/user/profile', function (Request $request) {
        return new UserResource($request->user());
    });
    Route::put('/user/profile', [UserProfileController::class, 'update']);

    Route::prefix('journals')->group(function () {
        Route::get('/', [JournalController::class, 'index']);
        Route::post('/', [JournalController::class, 'store']);
        Route::get('/public', [JournalController::class, 'publicFeed']);
    });

    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/toggle', [FavoriteController::class, 'toggle']);
    });

    Route::prefix('nasa')->group(function () {
        Route::get('/apod', [NasaProxyController::class, 'apod']);
        Route::get('/eonet', [NasaProxyController::class, 'eonet']);
        Route::get('/neo', [NasaProxyController::class, 'neo']);
        Route::get('/space-weather', [NasaProxyController::class, 'spaceWeather']);
    });
});
