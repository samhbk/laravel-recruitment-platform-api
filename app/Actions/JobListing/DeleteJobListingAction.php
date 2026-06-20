<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Domain\Repositories\JobListingRepositoryInterface;
use App\Models\JobListing;

final readonly class DeleteJobListingAction
{
    public function __construct(
        private JobListingRepositoryInterface $jobListingRepository,
    ) {}

    public function execute(JobListing $listing): void
    {
        $this->jobListingRepository->delete($listing);
    }
}
