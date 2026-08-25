<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
        'tenant_id',
        'avatar',
        'phone',
        'job_title',
        'department_id',
        'is_active',
        'last_login_at',
	'two_factor_secret',
    'two_factor_recovery_codes',
    'two_factor_confirmed_at',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
'two_factor_confirmed_at' => 'datetime',
    ];

    // ==================== CONSTANTS ====================
    
    const ROLES = [
        'super_admin' => 'Super Admin',
        'owner' => 'Company Owner',
        'admin' => 'Admin',
        'project_manager' => 'Project Manager',
        'team_lead' => 'Team Lead',
        'developer' => 'Developer',
        'qa' => 'QA Engineer',
        'designer' => 'Designer',
        'user' => 'Basic User',
        'client' => 'Client',
    ];

    // ==================== RELATIONSHIPS ====================

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'reporter_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    // ==================== ROLE METHODS ====================

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProjectManager(): bool
    {
        return $this->role === 'project_manager';
    }

    public function isTeamLead(): bool
    {
        return $this->role === 'team_lead';
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    public function isQA(): bool
    {
        return $this->role === 'qa';
    }

    public function isDesigner(): bool
    {
        return $this->role === 'designer';
    }

    public function isBasicUser(): bool
    {
        return $this->role === 'user';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdminOrAbove(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'owner']);
    }

    public function canManageCompany(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'owner']);
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'owner']);
    }

    public function canManageProjects(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'owner', 'project_manager', 'team_lead']);
    }

    public function getRoleDisplayName(): string
    {
        return self::ROLES[$this->role] ?? 'User';
    }

    public static function getRoles(): array
    {
        return self::ROLES;
    }
public function hasTwoFactorEnabled(): bool
{
    return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
}

public function getTwoFactorSecret(): ?string
{
    return $this->two_factor_secret;
}

public function setTwoFactorSecret(string $secret): void
{
    $this->two_factor_secret = $secret;
    $this->save();
}

}
