<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FeatureService;

class CheckFeature
{
    protected $featureService;

    public function __construct(FeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $tenant = $user->tenant;
        
        if (!$this->featureService->hasFeature($tenant, $feature)) {
            $requiredPlan = $this->featureService->getRequiredPlan($feature);
            
            return response()->json([
                'message' => "This feature requires the '{$requiredPlan}' plan or higher.",
                'feature' => $feature,
                'required_plan' => $requiredPlan,
                'current_plan' => $tenant->subscription?->plan->name ?? 'free',
            ], 403);
        }

        return $next($request);
    }
}
