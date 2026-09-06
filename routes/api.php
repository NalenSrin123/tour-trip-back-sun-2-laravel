<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TourImageController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Categories CRUD API Routes
Route::apiResource('categories', CategoryController::class);

// Roles CRUD API Routes
Route::apiResource('roles', RoleController::class);

// Tour Images CRUD API Routes
Route::patch('tour-images/{id}/primary', [TourImageController::class, 'setPrimary']);
Route::apiResource('tour-images', TourImageController::class);

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
