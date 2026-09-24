<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // ---------------------------------------------------------
            // 1. TOUR MANAGEMENT
            // ---------------------------------------------------------
            ['name' => 'view_tours'],
            ['name' => 'store_tours'],
            ['name' => 'update_tours'],
            ['name' => 'destroy_tours'],

            // ---------------------------------------------------------
            // 2. BOOKING MANAGEMENT
            // ---------------------------------------------------------
            ['name' => 'view_bookings'],
            ['name' => 'store_bookings'],
            ['name' => 'update_bookings'],
            ['name' => 'destroy_bookings'],
            ['name' => 'approve_bookings'], // Specific action for admins/staff
            ['name' => 'cancel_bookings'],

            // ---------------------------------------------------------
            // 3. CATEGORY & LOCATION MANAGEMENT
            // ---------------------------------------------------------
            ['name' => 'view_categories'],
            ['name' => 'store_categories'],
            ['name' => 'update_categories'],
            ['name' => 'destroy_categories'],

            ['name' => 'view_locations'],
            ['name' => 'store_locations'],
            ['name' => 'update_locations'],
            ['name' => 'destroy_locations'],

            // ---------------------------------------------------------
            // 4. USER & GUIDE MANAGEMENT (From your User Model)
            // ---------------------------------------------------------
            ['name' => 'view_users'],
            ['name' => 'store_users'],
            ['name' => 'update_users'],
            ['name' => 'destroy_users'],

            ['name' => 'view_guides'],
            ['name' => 'store_guides'],
            ['name' => 'update_guides'],
            ['name' => 'destroy_guides'],

            // ---------------------------------------------------------
            // 5. ROLE & PERMISSION MANAGEMENT (Super Admin level)
            // ---------------------------------------------------------
            ['name' => 'view_roles'],
            ['name' => 'store_roles'],
            ['name' => 'update_roles'],
            ['name' => 'destroy_roles'],

            ['name' => 'assign_permissions'], // Ability to attach permissions to roles/users
        ];

        // Insert using firstOrCreate to avoid duplicates if seeded multiple times
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']]);
        }
    }
}
