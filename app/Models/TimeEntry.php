<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    protected $table = 'time_entries';
    
    protected $fillable = [
        'user_id',
        'task_id',
        'hours',
        'date',
        'description'
    ];
    
    public $timestamps = true;
}
