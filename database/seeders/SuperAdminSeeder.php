<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Krijo kompaninë kryesore
        $company = Company::create([
            'name' => 'EPMP Global',
            'email' => 'admin@epmp.com',
            'subdomain' => 'epmp',
            'plan' => 'enterprise',
            'status' => 'active',
            'created_by' => 1,
        ]);

        // Krijo Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@epmp.com',
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }
}
