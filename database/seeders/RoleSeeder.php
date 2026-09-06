<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define standard roles based on your ERD
        $admin = Role::firstOrCreate(['name' => 'super_admin', 'type' => 'system']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'type' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'tour_manager', 'type' => 'staff']);
        $guide = Role::firstOrCreate(['name' => 'tour_guide', 'type' => 'staff']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'type' => 'user']);

        // 2. Fetch all available permissions from the database
        $allPermissions = Permission::all();

        // 3. Super Admin gets EVERYTHING
        $admin->permissions()->sync($allPermissions->pluck('id'));

        // 4. Tour Manager gets specific CRUD permissions, but cannot manage roles/users
        $managerPermissions = $allPermissions->whereIn('name', [
            'view_tours',
            'store_tours',
            'update_tours',
            'view_bookings',
            'update_bookings',
            'approve_bookings',
            'cancel_bookings',
            'view_categories',
            'store_categories',
            'update_categories',
            'view_locations',
            'store_locations',
            'update_locations',
        ]);
        $manager->permissions()->sync($managerPermissions->pluck('id'));

        // 5. Tour Guide gets read-only access to their relevant areas
        $guidePermissions = $allPermissions->whereIn('name', [
            'view_tours',
            'view_bookings',
            'view_locations'
        ]);
        $guide->permissions()->sync($guidePermissions->pluck('id'));

        // Note: Customers typically don't need backend permissions attached, 
        // they just need the 'customer' role for basic front-end access checks.

    }
}
