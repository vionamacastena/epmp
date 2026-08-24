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
            return response()->json(['data' => $projects]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|unique:projects,code',
                'description' => 'nullable|string',
                'priority' => 'nullable|string|in:low,medium,high,critical',
                'budget' => 'nullable|numeric|min:0',
                'manager_id' => 'nullable|exists:users,id',
            ]);

            $project = Project::create([
                'company_id' => Auth::user()->company_id ?? 1,
                'name' => $validated['name'],
                'code' => $validated['code'] ?? 'PRJ-' . strtoupper(substr($validated['name'], 0, 3)) . '-' . rand(100, 999),
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'] ?? 'medium',
                'budget' => $validated['budget'] ?? 0,
                'manager_id' => $validated['manager_id'] ?? Auth::id(),
                'created_by' => Auth::id(),
                'status' => 'planning',
            ]);

            return response()->json([
                'data' => $project->load(['manager', 'creator']),
                'message' => 'Project created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Project store error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $project = Project::with(['manager', 'creator', 'tasks'])->find($id);
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }
            return response()->json(['data' => $project]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
            return response()->json(['data' => $project]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
            return response()->json(['message' => 'Project deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function tasks($id)
    {
        try {
            $project = Project::with('tasks')->find($id);
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }
            return response()->json(['data' => $project->tasks]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
