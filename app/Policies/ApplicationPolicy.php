<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Application $application): bool
    {
        $application->loadMissing('jobListing');

        if ($application->user_id === $user->id) {
            return true;
        }

        return $this->userManagesListing($user, $application->jobListing);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Application $application): bool
    {
        $application->loadMissing('jobListing');

        return $this->userManagesListing($user, $application->jobListing);
    }

    public function delete(User $user, Application $application): bool
    {
        return false;
    }

    public function restore(User $user, Application $application): bool
    {
        return false;
    }

    public function forceDelete(User $user, Application $application): bool
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
