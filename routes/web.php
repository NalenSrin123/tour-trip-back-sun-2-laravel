<?php

use App\Http\Controllers\PaymentController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/user', function () {
    $users = User::with('roles.permissions')->get();

    return response()->json($users);
});

Route::get('/user/{id}', function ($id) {
    $users = User::select('id', 'name') // 1. Select only User ID and Name
        ->with([
            'roles:id,name',             // 2. Select only Role ID and Name
            'roles.permissions:id,name'  // 3. Select only Permission ID and Name
        ])

        // Filter: Only allow if the user has a role with the 'view_tours' permission
        ->whereHas('roles.permissions', function ($query) {
            $query->where('permissions.name', 'view_tours');
        })
        ->findOrFail($id); // Fetch the single user matching the $id

    // Hide the pivot objects for a cleaner JSON response
    $users->each(function ($user) {
        $user->roles->makeHidden('pivot');
        $user->roles->each(function ($role) {
            $role->permissions->makeHidden('pivot');
        });
    });
    return response()->json($users);
});

Route::post('/payway/checkout', [PaymentController::class, 'checkout']);
Route::get('/payway/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Polling status endpoint called by the Blade script
Route::get('/payment/check-status/{tran_id}', [PaymentController::class, 'checkStatus'])
    ->name('payment.check-status');

// Redirect page once payment is confirmed
Route::get('/payment/success', [PaymentController::class, 'success'])
    ->name('payment.success');








use Illuminate\Support\Facades\Artisan;

Route::get('/run-setup', function () {
    Artisan::call('storage:link');
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    return 'Setup Completed Successfully!';
});