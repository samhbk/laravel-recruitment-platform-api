<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Enums\UserRole;
use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function create(User $user): bool
    {
        return $user->role === UserRole::Company && ! $user->company()->exists();
    }

    public function update(User $user, Company $company): bool
    {
        return $user->role === UserRole::Admin || $company->user_id === $user->id;
    }

    public function view(?User $user, Company $company): bool
    {
        return true;
    }
}
