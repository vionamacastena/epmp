<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Finance2Controller extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'Finance2 works!']);
    }

    public function createBudget(Request $request)
    {
        try {
            $id = DB::table('budgets')->insertGetId([
                'project_id' => $request->project_id,
                'total_budget' => $request->total_budget,
                'currency' => $request->currency ?? 'EUR',
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['id' => $id, 'message' => 'Budget created!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDashboard($projectId)
    {
        try {
            $budget = DB::table('budgets')->where('project_id', $projectId)->first();
            return response()->json(['budget' => $budget]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
