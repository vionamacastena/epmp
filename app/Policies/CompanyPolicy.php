<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Company;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function view(User $user, Company $company): bool
    {
        return $user->isSuperAdmin() || 
               ($user->isAdmin() && $user->company_id === $company->id) ||
               ($user->isOwner() && $user->company_id === $company->id);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Company $company): bool
    {
        return $user->isSuperAdmin() || 
               ($user->isOwner() && $user->company_id === $company->id) ||
               ($user->isAdmin() && $user->company_id === $company->id);
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->isSuperAdmin();
    }

    public function manageSubscription(User $user, Company $company): bool
    {
        return $user->isSuperAdmin() || 
               ($user->isOwner() && $user->company_id === $company->id);
    }
}
