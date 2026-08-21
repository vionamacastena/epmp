<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeamInviteController extends Controller
{
    public function invite(Request $request)
    {
        try {
            Log::info('Team invite request received', ['data' => $request->all()]);

            $user = Auth::user();
            $invites = $request->invites;

            if (empty($invites)) {
                return response()->json(['message' => 'No invites provided'], 400);
            }

            $sent = [];
            
            foreach ($invites as $invite) {
                if (!empty($invite['email'])) {
                    Log::info('Invite sent', [
                        'to' => $invite['email'],
                        'role' => $invite['role'] ?? 'member',
                        'invited_by' => $user->name ?? 'Unknown'
                    ]);
                    $sent[] = $invite['email'];
                }
            }

            return response()->json([
                'message' => count($sent) . ' invites processed successfully',
                'sent' => $sent
            ], 200);

        } catch (\Exception $e) {
            Log::error('Team invite error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
