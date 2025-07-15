<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Roles
        $roles = ['admin', 'manager', 'marketing', 'surveyor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        
        // Create Permissions
        $permissions = [
            // Client permissions
            'view-clients', 'create-clients', 'edit-clients', 'delete-clients',
            // Project permissions
            'view-projects', 'create-projects', 'edit-projects', 'delete-projects',
            // Survey permissions
            'view-surveys', 'create-surveys', 'edit-surveys', 'delete-surveys',
            // Pipeline permissions
            'view-pipeline', 'edit-pipeline',
            // Document permissions
            'view-documents', 'upload-documents', 'delete-documents',
            // Report permissions
            'view-reports', 'export-reports',
            // User management
            'manage-users', 'manage-roles',
            // Settings
            'manage-settings'
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Assign permissions to roles
        $adminRole = Role::findByName('admin');
        $adminRole->syncPermissions(Permission::all());
        
        $managerRole = Role::findByName('manager');
        $managerRole->syncPermissions([
            'view-clients', 'view-projects', 'view-surveys',
            'view-pipeline', 'view-documents', 'view-reports',
            'export-reports'
        ]);
        
        $marketingRole = Role::findByName('marketing');
        $marketingRole->syncPermissions([
            'view-clients', 'create-clients', 'edit-clients',
            'view-projects', 'create-projects', 'edit-projects',
            'view-surveys', 'view-pipeline', 'edit-pipeline',
            'view-documents', 'upload-documents'
        ]);
        
        $surveyorRole = Role::findByName('surveyor');
        $surveyorRole->syncPermissions([
            'view-surveys', 'create-surveys', 'edit-surveys',
            'upload-documents'
        ]);
        
        // Create default users
        $users = [
            [
                'name' => 'Admin Hansen',
                'email' => 'admin@hansen.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Jl. Sudirman No. 1, Jakarta',
                'is_active' => true
            ],
            [
                'name' => 'Manager Hansen',
                'email' => 'manager@hansen.com',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'phone' => '081234567891',
                'address' => 'Jl. Thamrin No. 2, Jakarta',
                'is_active' => true
            ],
            [
                'name' => 'Marketing Hansen',
                'email' => 'marketing@hansen.com',
                'password' => Hash::make('password123'),
                'role' => 'marketing',
                'phone' => '081234567892',
                'address' => 'Jl. Gatot Subroto No. 3, Jakarta',
                'is_active' => true
            ],
            [
                'name' => 'Surveyor Hansen',
                'email' => 'surveyor@hansen.com',
                'password' => Hash::make('password123'),
                'role' => 'surveyor',
                'phone' => '081234567893',
                'address' => 'Jl. HR Rasuna Said No. 4, Jakarta',
                'is_active' => true
            ],
            [
                'name' => 'Surveyor 2',
                'email' => 'surveyor2@hansen.com',
                'password' => Hash::make('password123'),
                'role' => 'surveyor',
                'phone' => '081234567894',
                'address' => 'Jl. Kuningan No. 5, Jakarta',
                'is_active' => true
            ]
        ];
        
        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Sync role (akan replace role lama jika ada)
            $user->syncRoles([$role]);
        }
        
        $this->command->info('Users seeded successfully!');
    }
}