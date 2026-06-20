<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SavedJobRepositoryInterface
{
    public function save(User $user, JobListing $jobListing): void;

    public function unsave(User $user, JobListing $jobListing): bool;

    public function isSaved(User $user, int $jobListingId): bool;

    public function paginateForUser(User $user, int $perPage): LengthAwarePaginator;

    /**
     * @param  list<int>  $jobListingIds
     * @return list<int>
     */
    public function savedJobListingIdsForUser(User $user, array $jobListingIds): array;
}
