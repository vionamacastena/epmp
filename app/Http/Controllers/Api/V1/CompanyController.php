<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function index()
    {
        try {
            // Check if user is admin or super_admin
            if (Auth::user()->role !== 'super_admin' && Auth::user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized - Admin only'], 403);
            }

            $companies = Company::with('creator')->paginate(15);
            return CompanyResource::collection($companies);
        } catch (\Exception $e) {
            Log::error('CompanyController@index error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Check if user is admin or super_admin
            if (Auth::user()->role !== 'super_admin' && Auth::user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized - Admin only'], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:companies,email',
                'subdomain' => 'nullable|string|max:100|unique:companies,subdomain',
                'phone' => 'nullable|string|max:30',
                'address' => 'nullable|string',
                'website' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:50',
                'plan' => 'nullable|in:free,pro,enterprise',
                'status' => 'nullable|in:active,suspended,trial',
            ]);

            $company = Company::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subdomain' => $validated['subdomain'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'website' => $validated['website'] ?? null,
                'industry' => $validated['industry'] ?? null,
                'plan' => $validated['plan'] ?? 'free',
                'status' => $validated['status'] ?? 'active',
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'data' => new CompanyResource($company),
                'message' => 'Company created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('CompanyController@store error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Company $company)
    {
        try {
            if (Auth::user()->role !== 'super_admin' && Auth::user()->role !== 'admin' && Auth::user()->company_id !== $company->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return new CompanyResource($company->load('creator'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function update(Request $request, Company $company)
    {
        try {
            if (Auth::user()->role !== 'super_admin' && Auth::user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized - Admin only'], 403);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:companies,email,' . $company->id,
                'subdomain' => 'sometimes|string|max:100|unique:companies,subdomain,' . $company->id,
                'phone' => 'nullable|string|max:30',
                'address' => 'nullable|string',
                'website' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:50',
                'plan' => 'nullable|in:free,pro,enterprise',
                'status' => 'nullable|in:active,suspended,trial',
            ]);

            $company->update($validated);
            return new CompanyResource($company);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function destroy(Company $company)
    {
        try {
            if (Auth::user()->role !== 'super_admin') {
                return response()->json(['message' => 'Unauthorized - Super Admin only'], 403);
            }

            $company->delete();
            return response()->json(['message' => 'Company deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function activate(Company $company)
    {
        try {
            if (Auth::user()->role !== 'super_admin') {
                return response()->json(['message' => 'Unauthorized - Super Admin only'], 403);
            }

            $company->update(['status' => 'active']);
            return response()->json(['message' => 'Company activated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function suspend(Company $company)
    {
        try {
            if (Auth::user()->role !== 'super_admin') {
                return response()->json(['message' => 'Unauthorized - Super Admin only'], 403);
            }

            $company->update(['status' => 'suspended']);
            return response()->json(['message' => 'Company suspended successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function users(Company $company)
    {
        try {
            if (Auth::user()->role !== 'super_admin' && Auth::user()->role !== 'admin' && Auth::user()->company_id !== $company->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return response()->json($company->users);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }
}
