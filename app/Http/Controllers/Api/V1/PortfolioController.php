<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $companyId = $user->company_id;
            
            $projects = Project::with(['tasks', 'budget', 'expenses'])
                ->where('company_id', $companyId)
                ->get();

            $portfolio = [
                'total_projects' => $projects->count(),
                'active_projects' => $projects->where('status', 'active')->count(),
                'completed_projects' => $projects->where('status', 'completed')->count(),
                'total_budget' => $projects->sum('budget.amount'),
                'total_spent' => $projects->sum('expenses.amount'),
                'average_progress' => $projects->avg(function ($project) {
                    $totalTasks = $project->tasks->count();
                    if ($totalTasks === 0) return 0;
                    return ($project->tasks->where('status', 'done')->count() / $totalTasks) * 100;
                }),
                'projects' => $projects->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'status' => $project->status,
                        'progress' => $project->tasks->count() > 0 
                            ? round(($project->tasks->where('status', 'done')->count() / $project->tasks->count()) * 100, 2)
                            : 0,
                        'budget' => $project->budget ? $project->budget->amount : 0,
                        'spent' => $project->expenses->sum('amount'),
                        'tasks' => $project->tasks->count(),
                        'completed_tasks' => $project->tasks->where('status', 'done')->count(),
                    ];
                }),
            ];

            return response()->json(['data' => $portfolio]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
