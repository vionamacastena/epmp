<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    public function index($taskId)
    {
        $comments = Comment::where('task_id', $taskId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['data' => $comments]);
    }

    public function store(Request $request, $taskId)
    {
        $comment = Comment::create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return response()->json(['data' => $comment->load('user')], 201);
    }

    public function destroy($taskId, $commentId)
    {
        $comment = Comment::where('task_id', $taskId)->find($commentId);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }
        $comment->delete();
        return response()->json(['message' => 'Comment deleted']);
    }
}
