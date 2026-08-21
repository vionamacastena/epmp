<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskChecklistController extends Controller
{
    public function index($taskId)
    {
        $checklist = Checklist::firstOrCreate(
            ['task_id' => $taskId],
            ['name' => 'Checklist']
        );
        
        $items = ChecklistItem::where('checklist_id', $checklist->id)->get();
        return response()->json(['data' => [['id' => $checklist->id, 'items' => $items]]]);
    }

    public function store(Request $request, $taskId)
    {
        $checklist = Checklist::firstOrCreate(
            ['task_id' => $taskId],
            ['name' => 'Checklist']
        );

        $item = ChecklistItem::create([
            'checklist_id' => $checklist->id,
            'content' => $request->content,
            'is_completed' => false,
        ]);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, $taskId, $itemId)
    {
        $item = ChecklistItem::find($itemId);
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        $item->update(['is_completed' => $request->is_completed]);
        return response()->json(['data' => $item]);
    }

    public function destroy($taskId, $itemId)
    {
        $item = ChecklistItem::find($itemId);
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        $item->delete();
        return response()->json(['message' => 'Item deleted']);
    }
}
