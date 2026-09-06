<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

Route::apiResource('roles', RoleController::class);