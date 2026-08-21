<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'subdomain',
        'email',
        'phone',
        'address',
        'logo',
        'website',
        'industry',
        'plan',
        'plan_expires_at',
        'status',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
        'plan_expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($company) {
            if (empty($company->uuid)) {
                $company->uuid = (string) Str::uuid();
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isTrial()
    {
        return $this->status === 'trial';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }
}
