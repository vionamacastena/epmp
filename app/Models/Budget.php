<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'project_id', 'amount', 'currency', 'allocated_at', 'approved_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'allocated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getSpentAmount(): float
    {
        return $this->expenses()->sum('amount') ?? 0;
    }

    public function getRemainingAmount(): float
    {
        return $this->amount - $this->getSpentAmount();
    }

    public function getUtilization(): float
    {
        if ($this->amount === 0) {
            return 0;
        }
        return round(($this->getSpentAmount() / $this->amount) * 100, 2);
    }
}
