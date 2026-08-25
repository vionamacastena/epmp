<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class RoleTestSeeder extends Seeder
{
    public function run(): void
    {
        // Sigurohu që ka një kompani
        $company = Company::first();
        if (!$company) {
            $company = Company::create([
                'name' => 'EPMP Demo Company',
                'email' => 'demo@epmp.com',
                'phone' => '123456789',
                'address' => 'Demo Address',
                'status' => 'active',
                'plan' => 'free',
                'created_by' => 1,
            ]);
            $this->command->info('✅ Created demo company');
        }

        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@epmp.test', 'role' => 'super_admin'],
            ['name' => 'Company Owner', 'email' => 'owner@epmp.test', 'role' => 'owner'],
            ['name' => 'Admin User', 'email' => 'admin@epmp.test', 'role' => 'admin'],
            ['name' => 'Project Manager', 'email' => 'pm@epmp.test', 'role' => 'project_manager'],
            ['name' => 'Team Lead', 'email' => 'teamlead@epmp.test', 'role' => 'team_lead'],
            ['name' => 'Developer', 'email' => 'dev@epmp.test', 'role' => 'developer'],
            ['name' => 'QA Engineer', 'email' => 'qa@epmp.test', 'role' => 'qa'],
            ['name' => 'Designer', 'email' => 'designer@epmp.test', 'role' => 'designer'],
            ['name' => 'Basic User', 'email' => 'user@epmp.test', 'role' => 'user'],
            ['name' => 'Client', 'email' => 'client@epmp.test', 'role' => 'client'],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password123'),
                    'role' => $userData['role'],
                    'company_id' => $company->id,
                ]
            );
            $this->command->info("✅ Created {$userData['name']}");
        }

        $this->command->info('✅ All test users created successfully!');
        $this->command->info('📧 All passwords: password123');
    }
}
