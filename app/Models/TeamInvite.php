<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamInvite extends Model
{
    protected $fillable = ['email', 'role', 'invited_by', 'status', 'token'];

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
