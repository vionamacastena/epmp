<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        try {
            $projects = Project::with(['manager', 'creator'])->get();
            return response()->json([
                'data' => $projects,
                'message' => 'Projects retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Project index error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            $project = Project::create([
                'company_id' => $user->company_id ?? 1,
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'priority' => $request->priority ?? 'medium',
                'budget' => $request->budget ?? 0,
                'manager_id' => $request->manager_id ?? $user->id,
                'created_by' => $user->id,
                'status' => 'planning',
            ]);

            return response()->json([
                'data' => $project,
                'message' => 'Project created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Project store error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $project = Project::with(['manager', 'creator'])->find($id);
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }
            return response()->json(['data' => $project]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $project = Project::find($id);
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }
            $project->update($request->all());
            return response()->json([
                'data' => $project,
                'message' => 'Project updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $project = Project::find($id);
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }
            $project->delete();
            return response()->json(['message' => 'Project deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function tasks($id)
    {
        try {
            $project = Project::with('tasks')->find($id);
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }
            return response()->json([
                'data' => $project->tasks
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
