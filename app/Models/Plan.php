<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'display_name', 'description', 'price_monthly', 'price_yearly',
        'max_users', 'max_projects', 'max_teams', 'max_storage_gb',
        'max_ai_requests', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function features()
{
    return $this->belongsToMany(Feature::class, 'plan_features')
                ->withPivot('is_enabled', 'feature_limit')
                ->withTimestamps();
}

    public function hasFeature($featureKey): bool
    {
        return $this->features()->where('key', $featureKey)->exists();
    }
}
