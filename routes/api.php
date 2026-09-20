<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReferenceDataController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\TourImageController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\DestinationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuideController;

use App\Http\Controllers\Api\TourInclusionController;



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
Route::apiResource('destinations', DestinationController::class);
Route::apiResource('guides', GuideController::class);



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



// 1. Public Routes (Anyone can view tours)
Route::apiResource('tours', TourController::class)->only(['index', 'show']);
// 2. Protected Routes (Must be logged in and have permissions)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('tours', [TourController::class, 'store'])
        ->middleware('can:store_tours');

    Route::put('tours/{tour}', [TourController::class, 'update'])
        ->middleware('can:update_tours');
    Route::patch('tours/{tour}', [TourController::class, 'update'])
        ->middleware('can:update_tours');
    Route::delete('tours/{tour}', [TourController::class, 'destroy'])
        ->middleware('can:destroy_tours');

    Route::post('tours/{tour}/restore', [TourController::class, 'restore'])
        ->middleware('can:update_tours');

});


// Tour Inclusions CRUD API Routes
Route::apiResource('tour-inclusions', TourInclusionController::class);

Route::get('/reference-data', [ReferenceDataController::class, 'index']);