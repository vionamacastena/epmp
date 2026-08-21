<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\Expense;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function overview()
    {
        try {
            return response()->json([
                'projects' => ['total' => Project::count()],
                'tasks' => [
                    'total' => Task::count(),
                    'completed' => Task::where('status', 'done')->count(),
                    'in_progress' => Task::where('status', 'in_progress')->count(),
                    'todo' => Task::where('status', 'todo')->count(),
                    'completion_rate' => Task::count() > 0 ? round((Task::where('status', 'done')->count() / Task::count()) * 100) : 0,
                ],
                'time' => ['user_total_hours' => TimeEntry::where('user_id', Auth::id())->sum('hours')],
                'finance' => [
                    'total_expenses' => Expense::sum('amount') ?? 0,
                    'total_invoiced' => Invoice::where('status', 'paid')->sum('total') ?? 0,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
