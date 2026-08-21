<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'project_id', 'invoice_number', 'client_name', 'client_email',
        'amount', 'tax', 'total', 'issue_date', 'due_date', 
        'paid_date', 'status', 'notes', 'created_by'
    ];
}
