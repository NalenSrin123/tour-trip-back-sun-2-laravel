<?php

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