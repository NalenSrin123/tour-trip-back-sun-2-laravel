<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Categories CRUD API Routes
Route::apiResource('categories', CategoryController::class);

Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/login', [AuthController::class, 'login']);

    // Google OAuth Routes
    Route::get('/google/redirect', [GoogleController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    // 2. PROTECTED ROUTES (Middleware goes here)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});