<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanTask extends Model
{
    protected $fillable = [
        'task_id', 'kanban_column_id', 'position'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function column()
    {
        return $this->belongsTo(KanbanColumn::class);
    }
}
