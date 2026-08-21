<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            if ($user->role === 'super_admin' || $user->role === 'admin') {
                $teams = Team::with(['lead', 'company'])->paginate(15);
            } else {
                $teams = Team::where('company_id', $user->company_id)
                    ->with(['lead', 'company'])
                    ->paginate(15);
            }

            return response()->json([
                'data' => $teams->items(),
                'meta' => [
                    'current_page' => $teams->currentPage(),
                    'last_page' => $teams->lastPage(),
                    'per_page' => $teams->perPage(),
                    'total' => $teams->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('TeamController@index error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'lead_id' => 'required|exists:users,id',
            ]);

            $team = Team::create([
                'company_id' => $user->company_id ?? 1,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'lead_id' => $validated['lead_id'],
                'status' => 'active',
            ]);

            return response()->json([
                'message' => 'Team created successfully',
                'data' => $team->load(['lead', 'company'])
            ], 201);

        } catch (\Exception $e) {
            Log::error('TeamController@store error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function show(Team $team)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'super_admin' && $user->role !== 'admin' && 
                $user->company_id !== $team->company_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return response()->json([
                'data' => $team->load(['lead', 'company', 'members'])
            ]);

        } catch (\Exception $e) {
            Log::error('TeamController@show error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function update(Request $request, Team $team)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'super_admin' && $user->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'lead_id' => 'sometimes|exists:users,id',
                'status' => 'nullable|string|in:active,inactive',
            ]);

            $team->update($validated);

            return response()->json([
                'message' => 'Team updated successfully',
                'data' => $team->load(['lead', 'company'])
            ]);

        } catch (\Exception $e) {
            Log::error('TeamController@update error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function destroy(Team $team)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'super_admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $team->delete();

            return response()->json(['message' => 'Team deleted successfully']);

        } catch (\Exception $e) {
            Log::error('TeamController@destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function addMember(Request $request, Team $team)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'super_admin' && $user->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $team->members()->attach($validated['user_id']);

            return response()->json([
                'message' => 'Member added successfully',
                'data' => $team->load(['members'])
            ]);

        } catch (\Exception $e) {
            Log::error('TeamController@addMember error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function removeMember(Team $team, $userId)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'super_admin' && $user->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $team->members()->detach($userId);

            return response()->json([
                'message' => 'Member removed successfully',
                'data' => $team->load(['members'])
            ]);

        } catch (\Exception $e) {
            Log::error('TeamController@removeMember error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }
}
