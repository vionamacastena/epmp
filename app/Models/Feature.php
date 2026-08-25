<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'key', 'name', 'description', 'category', 'is_enterprise_only'
    ];

    protected $casts = [
        'is_enterprise_only' => 'boolean',
    ];

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_features')
                    ->withPivot('is_enabled', 'limit')
                    ->withTimestamps();
    }
}
