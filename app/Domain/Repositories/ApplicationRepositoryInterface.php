<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ApplicationRepositoryInterface
{
    public function create(JobListing $jobListing, User $user, array $data): Application;

    public function updateStatus(Application $application, string $status): Application;

    public function getByUserIdPaginated(int $userId, int $perPage): LengthAwarePaginator;

    public function getByJobListingIdPaginated(int $jobListingId, int $perPage): LengthAwarePaginator;

    public function userHasApplied(int $jobListingId, int $userId): bool;
}
