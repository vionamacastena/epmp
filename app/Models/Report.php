<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'name', 'type', 'parameters',
        'result_path', 'status', 'generated_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'generated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function isReady(): bool
    {
        return $this->status === 'completed';
    }

    public function isGenerating(): bool
    {
        return $this->status === 'generating';
    }
}
