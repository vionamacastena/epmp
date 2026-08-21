<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'project_id', 'total_budget', 'spent', 'currency', 
        'start_date', 'end_date', 'created_by'
    ];
}
