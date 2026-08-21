<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['project', 'assignee'])->get();
        return response()->json(['data' => $tasks]);
    }

    public function store(Request $request)
    {
        $task = Task::create([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description ?? null,
            'priority' => $request->priority ?? 'medium',
            'status' => $request->status ?? 'todo',
            'due_date' => $request->due_date ?? null,
            'estimated_hours' => $request->estimated_hours ?? null,
            'assigned_to' => $request->assigned_to ?? Auth::id(),
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'data' => $task->load(['project', 'assignee']),
            'message' => 'Task created'
        ], 201);
    }

    public function show($id)
    {
        $task = Task::with(['project', 'assignee', 'creator'])->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        return response()->json(['data' => $task]);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->update($request->all());
        return response()->json(['data' => $task]);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
