<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['key' => 'sprint_management', 'name' => 'Sprint Management', 'category' => 'project_management'],
            ['key' => 'budget_management', 'name' => 'Budget Management', 'category' => 'finance'],
            ['key' => 'ai_features', 'name' => 'AI Features', 'category' => 'ai'],
            ['key' => 'advanced_reports', 'name' => 'Advanced Reports', 'category' => 'reports'],
            ['key' => 'calendar_integration', 'name' => 'Calendar Integration', 'category' => 'integrations'],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }
}
