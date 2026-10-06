<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PaymentCallbackController;
use App\Http\Controllers\Api\ReferenceDataController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\TourImageController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\TourScheduleController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuideController;

use App\Http\Controllers\Api\BookingController;
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


// Route::apiResource('guides', GuideController::class);
Route::middleware('auth:sanctum')->group(function () {

    Route::get('guides', [GuideController::class, 'index'])
        ->middleware('can:view_guides');

    Route::get('guides/{id}', [GuideController::class, 'show'])
        ->middleware('can:view_guides');

    //User can be request to create a guide profile, but only admin can approve it
    Route::post('guides', [GuideController::class, 'store']);

    Route::put('guides/{id}', [GuideController::class, 'update'])
        ->middleware('can:update_guides');
    Route::patch('guides/{id}', [GuideController::class, 'update'])
        ->middleware('can:update_guides');
    Route::delete('guides/{id}', [GuideController::class, 'destroy'])
        ->middleware('can:destroy_guides');

    Route::post('guides/{id}/approve', [GuideController::class, 'adminApproveGuide'])
        ->middleware('can:update_guides');
});


Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/reset-password-with-otp', [AuthController::class, 'resetPasswordWithOtp']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    // Google OAuth Routes
    Route::get('/google/redirect', [GoogleController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    // 2. PROTECTED ROUTES (Middleware goes here)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
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

// Bookings API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('bookings', BookingController::class)->only(['index', 'show']);
    Route::post('bookings', [BookingController::class, 'store'])->middleware('can:store_bookings');
    Route::patch('bookings/{id}/cancel', [BookingController::class, 'cancel'])->middleware('can:cancel_bookings');
    Route::delete('bookings/{id}', [BookingController::class, 'destroy'])->middleware('can:destroy_bookings');
});


Route::get('/reference-data', [ReferenceDataController::class, 'index']);

// Tour Schedule API Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('tour-schedules', TourScheduleController::class)->only(['index', 'show']);

    // 1. Admin & Tour Manager only
    Route::middleware('role:admin|tour_manager')->group(function () {

        Route::apiResource('tour-schedules', TourScheduleController::class)->except(['index', 'show']);

        Route::post('tour-schedules/{id}/restore', [TourScheduleController::class, 'restore']);
    });
});

// Payment Callback Routes
Route::post('/payments/aba/callback', [PaymentCallbackController::class, 'handleAbaCallback'])
    ->name('aba.callback');

//Check Payment Status (Frontend Polling Fallback)

Route::get('/payments/{tranId}/status', [PaymentCallbackController::class, 'checkStatus'])
    ->name('payment.status')->middleware('auth:sanctum');

Route::get('/payments/{tranId}/check-status', [PaymentController::class, 'checkPaymentStatus'])
    ->name('payment.check-status')->middleware('auth:sanctum');