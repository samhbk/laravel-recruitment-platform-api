<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Domain\Repositories\JobListingRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetMyListingsAction
{
    public function __construct(
        private JobListingRepositoryInterface $jobListingRepository,
    ) {}

    public function execute(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->jobListingRepository->getByUserIdPaginated($user->id, $perPage);
    }
}
