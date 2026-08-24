<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    public function index()
    {
        try {
            $teams = DB::table('teams')
                ->leftJoin('users', 'teams.lead_id', '=', 'users.id')
                ->select('teams.*', 'users.name as lead_name')
                ->get();
            return response()->json(['data' => $teams]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'lead_id' => 'required|exists:users,id',
            ]);

            $id = DB::table('teams')->insertGetId([
                'company_id' => Auth::user()->company_id ?? 1,
                'name' => $validated['name'],
                'description' => $request->description ?? null,
                'lead_id' => $validated['lead_id'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $team = DB::table('teams')->where('id', $id)->first();
            return response()->json(['data' => $team, 'message' => 'Team created'], 201);
        } catch (\Exception $e) {
            Log::error('Team store error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $team = DB::table('teams')
                ->leftJoin('users', 'teams.lead_id', '=', 'users.id')
                ->select('teams.*', 'users.name as lead_name')
                ->where('teams.id', $id)
                ->first();

            if (!$team) {
                return response()->json(['message' => 'Team not found'], 404);
            }

            $members = DB::table('team_members')
                ->join('users', 'team_members.user_id', '=', 'users.id')
                ->where('team_members.team_id', $id)
                ->select('users.id', 'users.name', 'users.email')
                ->get();

            $team->members = $members;

            return response()->json(['data' => $team]);
        } catch (\Exception $e) {
            Log::error('Team show error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'lead_id' => 'required|exists:users,id',
                'status' => 'nullable|string|in:active,inactive',
            ]);

            $updated = DB::table('teams')
                ->where('id', $id)
                ->update([
                    'name' => $validated['name'],
                    'description' => $request->description,
                    'lead_id' => $validated['lead_id'],
                    'status' => $validated['status'] ?? 'active',
                    'updated_at' => now(),
                ]);

            if (!$updated) {
                return response()->json(['message' => 'Team not found'], 404);
            }

            $team = DB::table('teams')->where('id', $id)->first();
            return response()->json(['data' => $team, 'message' => 'Team updated']);
        } catch (\Exception $e) {
            Log::error('Team update error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('teams')->where('id', $id)->delete();
            if (!$deleted) {
                return response()->json(['message' => 'Team not found'], 404);
            }
            return response()->json(['message' => 'Team deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addMember(Request $request, $id)
    {
        try {
            $team = DB::table('teams')->where('id', $id)->first();
            if (!$team) {
                return response()->json(['message' => 'Team not found'], 404);
            }

            $userId = $request->user_id;

            $exists = DB::table('team_members')
                ->where('team_id', $id)
                ->where('user_id', $userId)
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'User is already a member'], 400);
            }

            DB::table('team_members')->insert([
                'team_id' => $id,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Member added successfully']);
        } catch (\Exception $e) {
            Log::error('Team add member error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function removeMember($id, $userId)
    {
        try {
            $team = DB::table('teams')->where('id', $id)->first();
            if (!$team) {
                return response()->json(['message' => 'Team not found'], 404);
            }

            DB::table('team_members')
                ->where('team_id', $id)
                ->where('user_id', $userId)
                ->delete();

            return response()->json(['message' => 'Member removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
