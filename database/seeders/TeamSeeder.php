<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@epmp.com',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'company_id' => 1,
                'is_active' => true,
            ]);
        }

        $teams = [
            ['name' => 'Design Team', 'description' => 'UI/UX design and prototyping'],
            ['name' => 'Development Team', 'description' => 'Full-stack development and architecture'],
            ['name' => 'QA Team', 'description' => 'Quality assurance and testing'],
            ['name' => 'Marketing Team', 'description' => 'Marketing campaigns and content'],
            ['name' => 'Product Team', 'description' => 'Product management and strategy'],
        ];

        foreach ($teams as $team) {
            Team::create([
                'name' => $team['name'],
                'description' => $team['description'],
                'lead_id' => $user->id,
                'company_id' => $user->company_id ?? 1,
                'status' => 'active',
            ]);
            echo "✅ Team created: {$team['name']}\n";
        }
    }
}
