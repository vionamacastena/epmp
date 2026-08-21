<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Krijo disa projekte
        $projects = [
            [
                'name' => 'Website Redesign',
                'code' => 'PRJ-001',
                'description' => 'Redesign company website',
                'status' => 'active',
                'priority' => 'high',
                'start_date' => now(),
                'end_date' => now()->addMonths(2),
                'budget' => 15000,
                'manager_id' => 2,
                'created_by' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Mobile App Development',
                'code' => 'PRJ-002',
                'description' => 'Develop mobile app for iOS and Android',
                'status' => 'planning',
                'priority' => 'critical',
                'start_date' => now()->addMonth(),
                'end_date' => now()->addMonths(4),
                'budget' => 50000,
                'manager_id' => 3,
                'created_by' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Marketing Campaign',
                'code' => 'PRJ-003',
                'description' => 'Q4 Marketing campaign',
                'status' => 'on_hold',
                'priority' => 'medium',
                'start_date' => now()->addWeeks(2),
                'end_date' => now()->addMonths(1),
                'budget' => 8000,
                'manager_id' => 4,
                'created_by' => 1,
                'company_id' => 1,
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Krijo detyra për çdo projekt
            Task::create([
                'project_id' => $project->id,
                'title' => 'Research and planning',
                'description' => 'Conduct research and create project plan',
                'status' => 'done',
                'priority' => 'high',
                'due_date' => now()->addDays(5),
                'estimated_hours' => 20,
                'assigned_to' => 2,
                'created_by' => 1,
            ]);

            Task::create([
                'project_id' => $project->id,
                'title' => 'Design phase',
                'description' => 'Create wireframes and designs',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => now()->addDays(12),
                'estimated_hours' => 40,
                'assigned_to' => 4,
                'created_by' => 1,
            ]);

            Task::create([
                'project_id' => $project->id,
                'title' => 'Development',
                'description' => 'Implement the solution',
                'status' => 'todo',
                'priority' => 'critical',
                'due_date' => now()->addDays(20),
                'estimated_hours' => 80,
                'assigned_to' => 3,
                'created_by' => 1,
            ]);
        }
    }
}
