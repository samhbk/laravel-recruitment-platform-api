<?php

declare(strict_types=1);

namespace App\Actions\SavedJob;

use App\Domain\Repositories\SavedJobRepositoryInterface;
use App\Models\JobListing;
use App\Models\User;

final readonly class UnsaveJobAction
{
    public function __construct(
        private SavedJobRepositoryInterface $savedJobs,
    ) {}

    public function execute(User $user, JobListing $jobListing): bool
    {
        return $this->savedJobs->unsave($user, $jobListing);
    }
}
