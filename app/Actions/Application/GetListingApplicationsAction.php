<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Domain\Repositories\ApplicationRepositoryInterface;
use App\Models\JobListing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetListingApplicationsAction
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
    ) {}

    public function execute(JobListing $jobListing, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applicationRepository->getByJobListingIdPaginated($jobListing->id, $perPage);
    }
}
