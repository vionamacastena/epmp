<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Të gjithë userat mund të shohin projektet e tyre
    }

    public function view(User $user, Project $project): bool
    {
        // Client sheh vetëm projektet ku është i lidhur
        if ($user->isClient()) {
            return $project->clients()->where('user_id', $user->id)->exists();
        }

        // Owner, Admin, PM, Team Lead shohin të gjitha projektet e kompanisë
        if ($user->isAdmin() || $user->isOwner() || $user->role === 'project_manager' || $user->role === 'team_lead') {
            return $user->company_id === $project->company_id;
        }

        // Developer, QA, Designer, User shohin vetëm projektet ku janë pjesë
        return $project->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return !$user->isClient() && !$user->role === 'user';
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->isClient()) {
            return false;
        }

        return $user->isSuperAdmin() || 
               ($user->isAdmin() && $user->company_id === $project->company_id) ||
               ($user->isOwner() && $user->company_id === $project->company_id) ||
               ($user->role === 'project_manager' && $user->company_id === $project->company_id);
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->isClient()) {
            return false;
        }

        return $user->isSuperAdmin() || 
               ($user->isAdmin() && $user->company_id === $project->company_id) ||
               ($user->isOwner() && $user->company_id === $project->company_id);
    }

    public function viewAsClient(User $user, Project $project): bool
    {
        return $user->isClient() && $project->clients()->where('user_id', $user->id)->exists();
    }
}
