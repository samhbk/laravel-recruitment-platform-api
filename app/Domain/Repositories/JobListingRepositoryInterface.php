<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JobListingRepositoryInterface
{
    public function getPublishedPaginated(int $perPage, JobListingFilters $filters): LengthAwarePaginator;

    public function findPublished(int $id): ?JobListing;

    public function find(int $id): ?JobListing;

    public function create(User $user, array $data): JobListing;

    public function update(JobListing $listing, array $data): JobListing;

    public function delete(JobListing $listing): void;

    public function getByUserIdPaginated(int $userId, int $perPage): LengthAwarePaginator;
}
