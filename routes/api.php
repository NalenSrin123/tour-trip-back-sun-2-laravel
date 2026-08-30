<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Categories CRUD API Routes
Route::apiResource('categories', CategoryController::class);

Route::prefix('/auth')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/verify-otp', [\App\Http\Controllers\Api\AuthController::class, 'verifyOtp']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

    // 2. PROTECTED ROUTES (Middleware goes here)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    });
});