<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanColumn extends Model
{
    protected $fillable = [
        'kanban_board_id', 'name', 'color', 'position', 'wip_limit', 'status_mapping'
    ];

    public function board()
    {
        return $this->belongsTo(KanbanBoard::class);
    }

    public function tasks()
    {
        return $this->hasMany(KanbanTask::class)->orderBy('position');
    }

    public function getTaskCount(): int
    {
        return $this->tasks()->count();
    }

    public function isWipExceeded(): bool
    {
        if ($this->wip_limit === null) {
            return false;
        }
        return $this->getTaskCount() > $this->wip_limit;
    }
}
