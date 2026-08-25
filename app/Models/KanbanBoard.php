<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanBoard extends Model
{
    protected $fillable = [
        'project_id', 'name', 'is_active', 'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function columns()
    {
        return $this->hasMany(KanbanColumn::class)->orderBy('position');
    }

    public function getDefaultColumns(): array
    {
        return [
            ['name' => 'To Do', 'color' => '#6b7280', 'position' => 0, 'status_mapping' => 'todo'],
            ['name' => 'In Progress', 'color' => '#3b82f6', 'position' => 1, 'status_mapping' => 'in_progress'],
            ['name' => 'Review', 'color' => '#f59e0b', 'position' => 2, 'status_mapping' => 'review'],
            ['name' => 'Testing', 'color' => '#8b5cf6', 'position' => 3, 'status_mapping' => 'testing'],
            ['name' => 'Done', 'color' => '#10b981', 'position' => 4, 'status_mapping' => 'done'],
        ];
    }

    public function initializeDefaultColumns()
    {
        foreach ($this->getDefaultColumns() as $columnData) {
            $this->columns()->create($columnData);
        }
    }
}
