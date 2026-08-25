<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $fillable = [
        'project_id', 'name', 'goal', 'start_date', 'end_date',
        'status', 'story_points', 'completed_points', 'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getProgress(): float
    {
        if ($this->story_points === 0) {
            return 0;
        }
        return round(($this->completed_points / $this->story_points) * 100, 2);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getBurndownData(): array
    {
        $totalDays = $this->start_date->diffInDays($this->end_date);
        $daysPassed = $this->start_date->diffInDays(now());
        
        $idealBurn = [];
        $actualBurn = [];
        
        for ($i = 0; $i <= $totalDays; $i++) {
            $idealBurn[] = $this->story_points - ($this->story_points / $totalDays) * $i;
        }
        
        // Actual burn (simplified)
        $remaining = $this->story_points - $this->completed_points;
        $actualBurn = array_fill(0, min($daysPassed + 1, $totalDays + 1), $remaining);
        
        return [
            'total_days' => $totalDays,
            'days_passed' => min($daysPassed, $totalDays),
            'total_points' => $this->story_points,
            'completed_points' => $this->completed_points,
            'remaining_points' => $this->story_points - $this->completed_points,
            'ideal_burn' => $idealBurn,
            'actual_burn' => $actualBurn,
            'progress' => $this->getProgress(),
        ];
    }
}
