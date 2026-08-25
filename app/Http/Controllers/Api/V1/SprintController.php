<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sprint;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SprintController extends Controller
{
    public function index($projectId)
    {
        try {
            $sprints = Sprint::where('project_id', $projectId)->orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $sprints]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'name' => 'required|string|max:255',
                'goal' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            $sprint = Sprint::create([
                'project_id' => $request->project_id,
                'name' => $request->name,
                'goal' => $request->goal,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'planning',
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Sprint created successfully',
                'data' => $sprint
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $sprint = Sprint::with(['tasks', 'tasks.assignee'])->find($id);
            if (!$sprint) {
                return response()->json(['message' => 'Sprint not found'], 404);
            }

            return response()->json([
                'data' => [
                    'sprint' => $sprint,
                    'progress' => $sprint->getProgress(),
                    'tasks' => $sprint->tasks,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $sprint = Sprint::find($id);
            if (!$sprint) {
                return response()->json(['message' => 'Sprint not found'], 404);
            }

            $sprint->update($request->all());

            return response()->json([
                'message' => 'Sprint updated successfully',
                'data' => $sprint
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $sprint = Sprint::find($id);
            if (!$sprint) {
                return response()->json(['message' => 'Sprint not found'], 404);
            }

            if ($sprint->tasks()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete sprint with tasks'
                ], 422);
            }

            $sprint->delete();

            return response()->json(['message' => 'Sprint deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function start($id)
    {
        try {
            $sprint = Sprint::find($id);
            if (!$sprint) {
                return response()->json(['message' => 'Sprint not found'], 404);
            }

            if ($sprint->tasks()->count() === 0) {
                return response()->json([
                    'message' => 'Cannot start sprint with no tasks'
                ], 422);
            }

            $sprint->update(['status' => 'active']);

            return response()->json([
                'message' => 'Sprint started successfully',
                'data' => $sprint
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function complete($id)
    {
        try {
            $sprint = Sprint::find($id);
            if (!$sprint) {
                return response()->json(['message' => 'Sprint not found'], 404);
            }

            $sprint->update(['status' => 'completed']);

            return response()->json([
                'message' => 'Sprint completed successfully',
                'data' => $sprint
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function addTask(Request $request, $id)
    {
        try {
            $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'story_points' => 'nullable|integer|min:0',
            ]);

            $sprint = Sprint::find($id);
            if (!$sprint) {
                return response()->json(['message' => 'Sprint not found'], 404);
            }

            $task = Task::find($request->task_id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }

            if ($task->sprint_id) {
                return response()->json([
                    'message' => 'Task already assigned to a sprint'
                ], 422);
            }

            $task->update([
                'sprint_id' => $id,
                'story_points' => $request->story_points ?? 0,
            ]);

            // Update sprint story points
            $sprint->story_points = $sprint->tasks()->sum('story_points');
            $sprint->save();

            return response()->json([
                'message' => 'Task added to sprint successfully',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function removeTask($sprintId, $taskId)
    {
        try {
            $sprint = Sprint::find($sprintId);
            if (!$sprint) {
                return response()->json(['message' => 'Sprint not found'], 404);
            }

            $task = Task::where('id', $taskId)->where('sprint_id', $sprintId)->first();
            if (!$task) {
                return response()->json(['message' => 'Task not found in sprint'], 404);
            }

            $task->update([
                'sprint_id' => null,
                'story_points' => null,
            ]);

            // Update sprint story points
            $sprint->story_points = $sprint->tasks()->sum('story_points');
            $sprint->save();

            return response()->json(['message' => 'Task removed from sprint successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
