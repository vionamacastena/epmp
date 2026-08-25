<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isOwner();
    }

    public function view(User $user, User $targetUser): bool
    {
        return $user->isSuperAdmin() || 
               ($user->isAdmin() && $user->company_id === $targetUser->company_id) ||
               ($user->isOwner() && $user->company_id === $targetUser->company_id);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->isSuperAdmin() || 
               ($user->isAdmin() && $user->company_id === $targetUser->company_id) ||
               ($user->isOwner() && $user->company_id === $targetUser->company_id);
    }

    public function delete(User $user, User $targetUser): bool
    {
        return $user->isSuperAdmin() || 
               ($user->isAdmin() && $user->company_id === $targetUser->company_id && $user->id !== $targetUser->id) ||
               ($user->isOwner() && $user->company_id === $targetUser->company_id && $user->id !== $targetUser->id);
    }

    public function assignRole(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isOwner();
    }
}
