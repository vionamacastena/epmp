<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'project_id', 'user_id', 'category', 'description', 
        'amount', 'date', 'receipt', 'status', 'approved_by', 'approved_at'
    ];
}
