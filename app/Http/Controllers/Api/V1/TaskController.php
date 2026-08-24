<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::with(['project', 'assignee', 'creator'])->get();
            return response()->json(['data' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'nullable|string|in:low,medium,high,critical',
                'status' => 'nullable|string|in:todo,in_progress,review,testing,done',
                'due_date' => 'nullable|date',
                'estimated_hours' => 'nullable|integer|min:0',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            $task = Task::create([
                'project_id' => $validated['project_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'] ?? 'medium',
                'status' => $validated['status'] ?? 'todo',
                'due_date' => $validated['due_date'] ?? null,
                'estimated_hours' => $validated['estimated_hours'] ?? null,
                'assigned_to' => $validated['assigned_to'] ?? Auth::id(),
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'data' => $task->load(['project', 'assignee', 'creator']),
                'message' => 'Task created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Task store error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $task = Task::with(['project', 'assignee', 'creator'])->find($id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            return response()->json(['data' => $task]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            $task->update($request->all());
            return response()->json(['data' => $task]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            $task->delete();
            return response()->json(['message' => 'Task deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            $task->update(['status' => $request->status]);
            return response()->json(['data' => $task]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
