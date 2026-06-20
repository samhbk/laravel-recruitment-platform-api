<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Domain\Repositories\JobListingFilters;
use App\Domain\Repositories\JobListingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetPublishedListingsAction
{
    public function __construct(
        private JobListingRepositoryInterface $jobListingRepository,
    ) {}

    public function execute(int $perPage, JobListingFilters $filters): LengthAwarePaginator
    {
        return $this->jobListingRepository->getPublishedPaginated($perPage, $filters);
    }
}
