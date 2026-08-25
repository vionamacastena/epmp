<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Feature;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $free = Plan::create([
            'name' => 'free',
            'display_name' => 'Free',
            'description' => 'Perfect for small teams',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'max_users' => 5,
            'max_projects' => 3,
            'max_teams' => 2,
            'max_storage_gb' => 1,
            'max_ai_requests' => 10,
            'sort_order' => 1,
        ]);

        $pro = Plan::create([
            'name' => 'pro',
            'display_name' => 'Pro',
            'description' => 'Advanced features for growing teams',
            'price_monthly' => 29.00,
            'price_yearly' => 290.00,
            'max_users' => 50,
            'max_projects' => -1,
            'max_teams' => -1,
            'max_storage_gb' => 100,
            'max_ai_requests' => 500,
            'sort_order' => 2,
        ]);

        $enterprise = Plan::create([
    'name' => 'enterprise',
    'display_name' => 'Enterprise',
    'description' => 'Full power for large organizations',
    'price_monthly' => 0,
    'price_yearly' => 0,
    'max_users' => -1,
    'max_projects' => -1,
    'max_teams' => -1,
    'max_storage_gb' => -1,
    'max_ai_requests' => -1,
    'sort_order' => 3,
]); 

        $freeFeature = Feature::where('key', 'sprint_management')->first();
        if ($freeFeature) {
            $free->features()->attach($freeFeature->id, ['is_enabled' => true]);
        }

        $proFeatures = Feature::whereIn('key', ['sprint_management', 'budget_management', 'ai_features', 'advanced_reports', 'calendar_integration'])->get();
        foreach ($proFeatures as $feature) {
            $pro->features()->attach($feature->id, ['is_enabled' => true]);
        }

        $allFeatures = Feature::all();
        foreach ($allFeatures as $feature) {
            $enterprise->features()->attach($feature->id, ['is_enabled' => true]);
        }
    }
}
