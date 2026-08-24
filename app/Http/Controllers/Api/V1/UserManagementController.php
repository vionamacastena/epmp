<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('company')->get();
            return response()->json(['data' => $users]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'nullable|string',
                'company_id' => 'nullable|exists:companies,id',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'] ?? 'user',
                'company_id' => $validated['company_id'] ?? 1,
                'is_active' => true,
            ]);

            return response()->json([
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('User store error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('company')->find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            return response()->json(['data' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'role' => 'sometimes|string',
                'is_active' => 'sometimes|boolean',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);
            return response()->json(['data' => $user, 'message' => 'User updated']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->delete();
            return response()->json(['message' => 'User deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function activate($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->update(['is_active' => true]);
            return response()->json(['message' => 'User activated']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deactivate($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->update(['is_active' => false]);
            return response()->json(['message' => 'User deactivated']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
