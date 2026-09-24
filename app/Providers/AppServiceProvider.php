<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ---------------------------------------------------------
        // 1. SUPER ADMIN OVERRIDE
        // ---------------------------------------------------------
        // A super_admin instantly passes ALL permission checks.
        Gate::before(function (User $user, string $ability) {
            if ($user->roles->contains('name', 'super_admin')) {
                return true;
            }
        });

        // ---------------------------------------------------------
        // 2. DYNAMIC PERMISSION GATES (All 31 Permissions)
        // ---------------------------------------------------------
        try {
            if (Schema::hasTable('permissions')) {
                $permissions = Permission::all();

                foreach ($permissions as $permission) {
                    Gate::define($permission->name, function (User $user) use ($permission) {
                        return $user->hasPermission($permission->name);
                    });
                }
            }
        } catch (\Throwable $e) {
            // Prevent commands from crashing when DB is offline or migrating
        }

        // ---------------------------------------------------------
        // 3. ROLE-BASED GATES 
        // ---------------------------------------------------------
        // Creates Gates to check exactly WHO the user is, rather than what they can do.
        Gate::define('is_admin', function (User $user) {
            return $user->roles->contains('name', 'admin');
        });

        Gate::define('is_tour_manager', function (User $user) {
            return $user->roles->contains('name', 'tour_manager');
        });

        Gate::define('is_tour_guide', function (User $user) {
            return $user->roles->contains('name', 'tour_guide');
        });

        Gate::define('is_customer', function (User $user) {
            return $user->roles->contains('name', 'customer');
        });
    }
}
