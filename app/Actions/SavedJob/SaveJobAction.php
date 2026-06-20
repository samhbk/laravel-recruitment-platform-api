<?php

declare(strict_types=1);

namespace App\Actions\SavedJob;

use App\Domain\Exceptions\DomainException;
use App\Domain\Repositories\SavedJobRepositoryInterface;
use App\Models\JobListing;
use App\Models\User;

final readonly class SaveJobAction
{
    public function __construct(
        private SavedJobRepositoryInterface $savedJobs,
    ) {}

    public function execute(User $user, JobListing $jobListing): void
    {
        if (! $jobListing->is_published) {
            throw new DomainException('Only published jobs can be saved.');
        }

        $this->savedJobs->save($user, $jobListing);
    }
}
