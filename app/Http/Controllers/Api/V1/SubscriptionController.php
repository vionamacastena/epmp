<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function current()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        $subscription = $tenant->subscription;
        
        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription',
                'default_plan' => Plan::where('name', 'free')->first(),
            ]);
        }

        return response()->json([
            'subscription' => $subscription->load('plan'),
            'features' => $subscription->plan->features,
            'is_active' => $subscription->isActive(),
        ]);
    }

    public function plans()
    {
        $plans = Plan::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(function ($plan) {
                        return [
                            'id' => $plan->id,
                            'name' => $plan->name,
                            'display_name' => $plan->display_name,
                            'description' => $plan->description,
                            'price_monthly' => $plan->price_monthly,
                            'price_yearly' => $plan->price_yearly,
                            'features' => $plan->features,
                            'limits' => [
                                'users' => $plan->max_users,
                                'projects' => $plan->max_projects,
                                'teams' => $plan->max_teams,
                                'storage' => $plan->max_storage_gb,
                                'ai_requests' => $plan->max_ai_requests,
                            ],
                        ];
                    });

        return response()->json(['data' => $plans]);
    }
}
