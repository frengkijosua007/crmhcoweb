<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = [
            'admin' => 'Full access to all features',
            'manager' => 'Manage projects, clients, and reports',
            'marketing' => 'Manage clients and projects',
            'surveyor' => 'Conduct surveys and upload reports',
            'user' => 'Basic access'
        ];

        foreach ($roles as $name => $description) {
            // Check if the role already exists before creating it
            if (!Role::where('name', $name)->where('guard_name', 'web')->exists()) {
                Role::create([
                    'name' => $name,
                    'guard_name' => 'web'
                ]);
            }
        }

        // Ensure at least one admin user exists
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        // Make sure user has admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
