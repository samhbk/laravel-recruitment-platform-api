<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Enums\UserRole;
use App\Models\JobListing;
use App\Models\User;

class JobListingPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, JobListing $jobListing): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Company, UserRole::Admin], true);
    }

    public function update(User $user, JobListing $jobListing): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        if ($jobListing->company_id !== null) {
            $user->loadMissing('company');
        }

        return $this->userManagesListing($user, $jobListing);
    }

    public function delete(User $user, JobListing $jobListing): bool
    {
        return $this->update($user, $jobListing);
    }

    public function restore(User $user, JobListing $jobListing): bool
    {
        return false;
    }

    public function forceDelete(User $user, JobListing $jobListing): bool
    {
        return false;
    }

    private function userManagesListing(User $user, JobListing $jobListing): bool
    {
        if ($jobListing->user_id === $user->id) {
            return true;
        }

        $companyId = $jobListing->company_id;
        if ($companyId !== null && $user->company !== null && $user->company->id === $companyId) {
            return true;
        }

        return false;
    }
}
