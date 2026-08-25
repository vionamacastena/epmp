<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KanbanBoard;
use App\Models\KanbanColumn;
use App\Models\KanbanTask;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KanbanController extends Controller
{
    public function getBoard($projectId)
    {
        try {
            $project = Project::find($projectId);
            if (!$project) {
                return response()->json(['message' => 'Project not found'], 404);
            }

            $board = KanbanBoard::firstOrCreate(
                ['project_id' => $projectId],
                ['name' => 'Default Board']
            );

            if ($board->wasRecentlyCreated) {
                $board->initializeDefaultColumns();
            }

            $board->load(['columns.tasks.task']);
            
            return response()->json([
                'data' => [
                    'board' => $board,
                    'columns' => $board->columns->map(function ($column) {
                        return [
                            'id' => $column->id,
                            'name' => $column->name,
                            'color' => $column->color,
                            'wip_limit' => $column->wip_limit,
                            'task_count' => $column->tasks->count(),
                            'tasks' => $column->tasks->map(function ($kanbanTask) {
                                $task = $kanbanTask->task;
                                return $task ? [
                                    'id' => $task->id,
                                    'title' => $task->title,
                                    'priority' => $task->priority,
                                    'status' => $task->status,
                                    'position' => $kanbanTask->position,
                                ] : null;
                            })->filter(),
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

    public function moveTask(Request $request)
    {
        try {
            $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'column_id' => 'required|exists:kanban_columns,id',
                'position' => 'nullable|integer|min:0',
            ]);

            $kanbanTask = KanbanTask::where('task_id', $request->task_id)->first();
            
            if (!$kanbanTask) {
                $kanbanTask = KanbanTask::create([
                    'task_id' => $request->task_id,
                    'kanban_column_id' => $request->column_id,
                    'position' => $request->position ?? 0,
                ]);
            } else {
                $kanbanTask->update([
                    'kanban_column_id' => $request->column_id,
                    'position' => $request->position ?? $kanbanTask->position,
                ]);
            }

            $column = KanbanColumn::find($request->column_id);
            if ($column && $column->status_mapping) {
                $task = Task::find($request->task_id);
                if ($task) {
                    $task->update(['status' => $column->status_mapping]);
                }
            }

            return response()->json([
                'message' => 'Task moved successfully',
                'data' => $kanbanTask
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createColumn(Request $request)
    {
        try {
            $request->validate([
                'board_id' => 'required|exists:kanban_boards,id',
                'name' => 'required|string|max:255',
                'color' => 'nullable|string',
                'wip_limit' => 'nullable|integer|min:0',
                'status_mapping' => 'nullable|string',
            ]);

            $column = KanbanColumn::create([
                'kanban_board_id' => $request->board_id,
                'name' => $request->name,
                'color' => $request->color ?? '#6b7280',
                'position' => KanbanColumn::where('kanban_board_id', $request->board_id)->count(),
                'wip_limit' => $request->wip_limit,
                'status_mapping' => $request->status_mapping,
            ]);

            return response()->json([
                'message' => 'Column created successfully',
                'data' => $column
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateColumn(Request $request, $columnId)
    {
        try {
            $column = KanbanColumn::find($columnId);
            if (!$column) {
                return response()->json(['message' => 'Column not found'], 404);
            }

            $column->update($request->all());

            return response()->json([
                'message' => 'Column updated successfully',
                'data' => $column
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteColumn($columnId)
    {
        try {
            $column = KanbanColumn::find($columnId);
            if (!$column) {
                return response()->json(['message' => 'Column not found'], 404);
            }

            if ($column->tasks()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete column with tasks. Move tasks first.'
                ], 422);
            }

            $column->delete();

            return response()->json([
                'message' => 'Column deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
