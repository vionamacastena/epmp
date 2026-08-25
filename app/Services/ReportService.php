<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateProjectReport($projectId, $filters = [])
    {
        $project = Project::with(['tasks', 'timeEntries', 'budget', 'expenses'])->find($projectId);
        
        if (!$project) {
            return null;
        }

        $tasks = $project->tasks;
        $timeEntries = $project->timeEntries;
        $expenses = $project->expenses;

        return [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'code' => $project->code,
                'status' => $project->status,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
            ],
            'tasks' => [
                'total' => $tasks->count(),
                'completed' => $tasks->where('status', 'done')->count(),
                'in_progress' => $tasks->where('status', 'in_progress')->count(),
                'todo' => $tasks->where('status', 'todo')->count(),
                'completion_rate' => $tasks->count() > 0 
                    ? round(($tasks->where('status', 'done')->count() / $tasks->count()) * 100, 2)
                    : 0,
                'by_priority' => $tasks->groupBy('priority')->map->count(),
                'by_assignee' => $tasks->groupBy('assignee.name')->map->count(),
            ],
            'time' => [
                'total_hours' => $timeEntries->sum('hours'),
                'by_user' => $timeEntries->groupBy('user.name')->map->sum('hours'),
                'by_date' => $timeEntries->groupBy('date')->map->sum('hours'),
            ],
            'finance' => [
                'budget' => $project->budget ? [
                    'total' => $project->budget->amount,
                    'spent' => $project->budget->getSpentAmount(),
                    'remaining' => $project->budget->getRemainingAmount(),
                    'utilization' => $project->budget->getUtilization(),
                ] : null,
                'expenses' => [
                    'total' => $expenses->sum('amount'),
                    'by_category' => $expenses->groupBy('category')->map->sum('amount'),
                    'by_status' => $expenses->groupBy('status')->map->sum('amount'),
                ],
                'invoices' => [
                    'total' => $project->invoices->sum('total'),
                    'paid' => $project->invoices->where('status', 'paid')->sum('total'),
                    'unpaid' => $project->invoices->where('status', '!=', 'paid')->sum('total'),
                ],
            ],
            'generated_at' => now()->toISOString(),
        ];
    }

    public function generateTeamPerformanceReport($teamId, $dateFrom, $dateTo)
    {
        // Team performance logic
        return [
            'team_id' => $teamId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'metrics' => [
                'tasks_completed' => 0,
                'avg_completion_time' => 0,
                'total_hours' => 0,
                'velocity' => 0,
            ],
            'generated_at' => now()->toISOString(),
        ];
    }

    public function generateFinancialReport($companyId, $dateFrom, $dateTo)
    {
        // Financial report logic
        return [
            'company_id' => $companyId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'summary' => [
                'total_revenue' => 0,
                'total_expenses' => 0,
                'net_profit' => 0,
                'profit_margin' => 0,
            ],
            'generated_at' => now()->toISOString(),
        ];
    }

    public function generateProductivityReport($userId, $dateFrom, $dateTo)
    {
        // Productivity report logic
        return [
            'user_id' => $userId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'metrics' => [
                'tasks_completed' => 0,
                'hours_logged' => 0,
                'avg_task_completion' => 0,
                'productivity_score' => 0,
            ],
            'generated_at' => now()->toISOString(),
        ];
    }
}
