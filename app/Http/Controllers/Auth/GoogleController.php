<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

// use Laravel\Socialite\Socialite;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['prompt' => 'select_account']) // Even if you know who this user is, make them click and confirm which account they want to use
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = DB::transaction(function () use ($googleUser) {

                // 1. Find the user by email or create them using your exact schema
                $user = User::updateOrCreate(
                    ['email' => $googleUser->getEmail()],
                    [
                        'name' => $googleUser->getName(),
                        'email_verified_at' => now(), // Mark email as verified
                        'status' => 'active',         // Override the 'inactive' default
                    ]
                );

                // 2. Link the social account using only the columns present in the ERD
                $user->socialAccounts()->firstOrCreate([
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                ]);

                // 3. Set Role to 'user' if not already set
                $userRole = $user->roles()->where('name', 'user')->first();
                if ($userRole) {
                    $user->roles()->syncWithoutDetaching([$userRole->id]);
                }

                return $user;
            });

            // 3. Issue the Sanctum token (creates a record in personal_access_token)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Google login successful',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to authenticate with Google',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
