<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserResource;
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
            $user = Auth::user();

            Log::info('UserManagementController@index called', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);

            if ($user->role === 'super_admin') {
                $users = User::with('company')->paginate(15);
            } else if ($user->role === 'admin') {
                $users = User::where('company_id', $user->company_id)->paginate(15);
            } else {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return UserResource::collection($users);
        } catch (\Exception $e) {
            Log::error('UserManagementController@index error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server Error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $authUser = Auth::user();

            if ($authUser->role === 'super_admin' || $authUser->role === 'admin') {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'company_id' => $request->company_id ?? $authUser->company_id,
                    'role' => $request->role ?? 'user',
                    'is_active' => true,
                ]);

                return new UserResource($user);
            }

            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            Log::error('UserManagementController@store error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server Error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user)
    {
        try {
            $authUser = Auth::user();

            if ($authUser->role === 'super_admin' || 
                ($authUser->role === 'admin' && $authUser->company_id === $user->company_id)) {
                return new UserResource($user->load('company'));
            }

            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            Log::error('UserManagementController@show error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server Error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $authUser = Auth::user();

            if ($authUser->role === 'super_admin' || 
                ($authUser->role === 'admin' && $authUser->company_id === $user->company_id)) {
                
                $data = $request->validated();
                
                if (isset($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                }
                
                $user->update($data);
                return new UserResource($user);
            }

            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            Log::error('UserManagementController@update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server Error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $authUser = Auth::user();

            if ($authUser->role === 'super_admin' || 
                ($authUser->role === 'admin' && $authUser->company_id === $user->company_id)) {
                
                $user->delete();
                return response()->json(['message' => 'User deleted successfully']);
            }

            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            Log::error('UserManagementController@destroy error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server Error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function activate(User $user)
    {
        try {
            $authUser = Auth::user();

            if ($authUser->role === 'super_admin' || 
                ($authUser->role === 'admin' && $authUser->company_id === $user->company_id)) {
                
                $user->update(['is_active' => true]);
                return response()->json(['message' => 'User activated successfully']);
            }

            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            Log::error('UserManagementController@activate error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server Error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function deactivate(User $user)
    {
        try {
            $authUser = Auth::user();

            if ($authUser->role === 'super_admin' || 
                ($authUser->role === 'admin' && $authUser->company_id === $user->company_id)) {
                
                $user->update(['is_active' => false]);
                return response()->json(['message' => 'User deactivated successfully']);
            }

            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            Log::error('UserManagementController@deactivate error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server Error',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
