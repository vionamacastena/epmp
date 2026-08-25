<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Project;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function projectReport(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
            ]);

            $report = $this->reportService->generateProjectReport(
                $request->project_id,
                $request->only(['date_from', 'date_to'])
            );

            return response()->json([
                'data' => $report,
                'message' => 'Report generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function teamPerformance(Request $request)
    {
        try {
            $request->validate([
                'team_id' => 'required|exists:teams,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date|after:date_from',
            ]);

            $report = $this->reportService->generateTeamPerformanceReport(
                $request->team_id,
                $request->date_from,
                $request->date_to
            );

            return response()->json([
                'data' => $report,
                'message' => 'Report generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function financialReport(Request $request)
    {
        try {
            $request->validate([
                'date_from' => 'required|date',
                'date_to' => 'required|date|after:date_from',
            ]);

            $tenant = Auth::user()->tenant;
            $report = $this->reportService->generateFinancialReport(
                $tenant->id,
                $request->date_from,
                $request->date_to
            );

            return response()->json([
                'data' => $report,
                'message' => 'Report generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function productivityReport(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date|after:date_from',
            ]);

            $userId = $request->user_id ?? Auth::id();
            $report = $this->reportService->generateProductivityReport(
                $userId,
                $request->date_from,
                $request->date_to
            );

            return response()->json([
                'data' => $report,
                'message' => 'Report generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

public function overview(Request $request)
{
    try {
        $user = Auth::user();
        $companyId = $user->company_id;
        
        $projects = Project::where('company_id', $companyId)->get();
        
        return response()->json([
            'data' => [
                'total_projects' => $projects->count(),
                'active_projects' => $projects->where('status', 'active')->count(),
                'completed_projects' => $projects->where('status', 'completed')->count(),
                'on_hold_projects' => $projects->where('status', 'on_hold')->count(),
                'projects' => $projects->map(function ($project) {
                    $totalTasks = $project->tasks->count();
                    $completedTasks = $project->tasks->where('status', 'done')->count();
                    
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'status' => $project->status,
                        'progress' => $totalTasks > 0 
                            ? round(($completedTasks / $totalTasks) * 100, 2)
                            : 0,
                        'tasks_count' => $totalTasks,
                        'completed_tasks' => $completedTasks,
                    ];
                }),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}
    public function budgetVsActual(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
            ]);

            $project = Project::with(['budget', 'expenses'])->find($request->project_id);

            return response()->json([
                'data' => [
                    'project' => $project->name,
                    'budget' => $project->budget ? [
                        'allocated' => $project->budget->amount,
                        'spent' => $project->budget->getSpentAmount(),
                        'remaining' => $project->budget->getRemainingAmount(),
                        'utilization' => $project->budget->getUtilization(),
                    ] : null,
                    'expenses' => $project->expenses->groupBy('category')->map->sum('amount'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
