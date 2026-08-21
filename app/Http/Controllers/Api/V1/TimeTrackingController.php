<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimeTrackingController extends Controller
{
    public function index()
    {
        try {
            $entries = DB::select("
                SELECT * FROM time_entries 
                WHERE user_id = ?
                ORDER BY date DESC
            ", [Auth::id()]);

            return response()->json($entries);
        } catch (\Exception $e) {
            Log::error('TimeTracking index: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $userId = Auth::id();
            $taskId = $request->task_id;
            $hours = $request->hours;
            $date = $request->date ?? date('Y-m-d');
            $description = $request->description;

            Log::info("Creating time entry: user_id={$userId}, task_id={$taskId}, hours={$hours}");

            $id = DB::table('time_entries')->insertGetId([
                'user_id' => $userId,
                'task_id' => $taskId,
                'hours' => $hours,
                'date' => $date,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $entry = DB::table('time_entries')->where('id', $id)->first();

            return response()->json($entry, 201);
        } catch (\Exception $e) {
            Log::error('TimeTracking store: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
