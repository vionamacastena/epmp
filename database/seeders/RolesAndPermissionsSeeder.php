<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ============ KRIJO PERMISSIONS ============
        
        $permissions = [
            // Company
            'view companies', 'create companies', 'edit companies', 'delete companies',
            
            // User
            'view users', 'create users', 'edit users', 'delete users', 'assign roles',
            
            // Project
            'view projects', 'create projects', 'edit projects', 'delete projects', 'assign projects',
            
            // Task
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks', 'complete tasks',
            
            // Team
            'view teams', 'create teams', 'edit teams', 'delete teams', 'manage team members',
            
            // Finance
            'view finance', 'manage budget', 'create invoices', 'edit invoices', 'delete invoices',
            
            // Report
            'view reports', 'generate reports',
            
            // Settings
            'view settings', 'edit settings',
            
            // Calendar
            'view calendar', 'create events', 'edit events', 'delete events',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        echo "✅ All permissions created!\n";

        // ============ KRIJO ROLE DHE ASSIGN PERMISSIONS ============

        // 1. Super Admin - Të gjitha permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'sanctum']);
        $superAdmin->syncPermissions(Permission::all());
        echo "✅ Super Admin role created with all permissions\n";

        // 2. Admin
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $admin->syncPermissions([
            'view users', 'create users', 'edit users', 'delete users', 'assign roles',
            'view projects', 'create projects', 'edit projects', 'delete projects', 'assign projects',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks', 'complete tasks',
            'view teams', 'create teams', 'edit teams', 'delete teams', 'manage team members',
            'view finance', 'manage budget', 'create invoices', 'edit invoices', 'delete invoices',
            'view reports', 'generate reports',
            'view settings', 'edit settings',
            'view calendar', 'create events', 'edit events', 'delete events',
        ]);
        echo "✅ Admin role created\n";

        // 3. Project Manager
        $pm = Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'sanctum']);
        $pm->syncPermissions([
            'view projects', 'create projects', 'edit projects', 'delete projects', 'assign projects',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks', 'complete tasks',
            'view teams', 'manage team members',
            'view finance',
            'view reports',
            'view calendar', 'create events', 'edit events', 'delete events',
        ]);
        echo "✅ Project Manager role created\n";

        // 4. Team Lead
        $teamLead = Role::firstOrCreate(['name' => 'team_lead', 'guard_name' => 'sanctum']);
        $teamLead->syncPermissions([
            'view projects', 'create projects', 'edit projects',
            'view tasks', 'create tasks', 'edit tasks', 'assign tasks', 'complete tasks',
            'view teams', 'manage team members',
            'view reports',
            'view calendar', 'create events',
        ]);
        echo "✅ Team Lead role created\n";

        // 5. Developer
        $developer = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'sanctum']);
        $developer->syncPermissions([
            'view projects',
            'view tasks', 'edit tasks', 'complete tasks',
            'view teams',
            'view calendar',
        ]);
        echo "✅ Developer role created\n";

        // 6. QA Engineer
        $qa = Role::firstOrCreate(['name' => 'qa', 'guard_name' => 'sanctum']);
        $qa->syncPermissions([
            'view projects',
            'view tasks', 'edit tasks', 'complete tasks',
            'view calendar',
        ]);
        echo "✅ QA role created\n";

        // 7. Designer
        $designer = Role::firstOrCreate(['name' => 'designer', 'guard_name' => 'sanctum']);
        $designer->syncPermissions([
            'view projects',
            'view tasks', 'edit tasks', 'complete tasks',
            'view calendar',
        ]);
        echo "✅ Designer role created\n";

        // 8. Client
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'sanctum']);
        $client->syncPermissions([
            'view projects',
            'view tasks',
        ]);
        echo "✅ Client role created\n";

        // 9. User (Default)
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'sanctum']);
        $user->syncPermissions([
            'view projects',
            'view tasks', 'complete tasks',
            'view calendar',
        ]);
        echo "✅ User role created\n";

        // ============ KRIJO USER-AT ME ROLE ============

        // 1. Super Admin
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@epmp.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $superAdminUser->assignRole('super_admin');
        echo "✅ Super Admin user created\n";

        // 2. Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@epmp.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $adminUser->assignRole('admin');
        echo "✅ Admin user created\n";

        // 3. Project Manager
        $pmUser = User::firstOrCreate(
            ['email' => 'pm@epmp.com'],
            [
                'name' => 'Project Manager',
                'email' => 'pm@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'project_manager',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $pmUser->assignRole('project_manager');
        echo "✅ Project Manager user created\n";

        // 4. Team Lead
        $teamLeadUser = User::firstOrCreate(
            ['email' => 'teamlead@epmp.com'],
            [
                'name' => 'Team Lead',
                'email' => 'teamlead@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'team_lead',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $teamLeadUser->assignRole('team_lead');
        echo "✅ Team Lead user created\n";

        // 5. Developer
        $devUser = User::firstOrCreate(
            ['email' => 'developer@epmp.com'],
            [
                'name' => 'Developer User',
                'email' => 'developer@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'developer',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $devUser->assignRole('developer');
        echo "✅ Developer user created\n";

        // 6. QA
        $qaUser = User::firstOrCreate(
            ['email' => 'qa@epmp.com'],
            [
                'name' => 'QA User',
                'email' => 'qa@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'qa',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $qaUser->assignRole('qa');
        echo "✅ QA user created\n";

        // 7. Designer
        $designerUser = User::firstOrCreate(
            ['email' => 'designer@epmp.com'],
            [
                'name' => 'Designer User',
                'email' => 'designer@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'designer',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $designerUser->assignRole('designer');
        echo "✅ Designer user created\n";

        // 8. Client
        $clientUser = User::firstOrCreate(
            ['email' => 'client@epmp.com'],
            [
                'name' => 'Client User',
                'email' => 'client@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $clientUser->assignRole('client');
        echo "✅ Client user created\n";

        // 9. User (Default)
        $userUser = User::firstOrCreate(
            ['email' => 'user@epmp.com'],
            [
                'name' => 'Default User',
                'email' => 'user@epmp.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'company_id' => 1,
                'is_active' => true,
            ]
        );
        $userUser->assignRole('user');
        echo "✅ Default user created\n";

        echo "\n🎉 All roles, permissions and users created successfully!\n";
    }
}
