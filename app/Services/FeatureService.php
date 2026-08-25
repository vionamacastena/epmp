<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Feature;

class FeatureService
{
    public function hasFeature(Tenant $tenant, string $featureKey): bool
    {
        $subscription = $tenant->subscription;
        
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;
        return $plan->hasFeature($featureKey);
    }
}
