<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\SavedJobRepositoryInterface;
use App\Models\JobListing;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSavedJobRepository implements SavedJobRepositoryInterface
{
    public function save(User $user, JobListing $jobListing): void
    {
        SavedJob::query()->firstOrCreate([
            'user_id' => $user->id,
            'job_listing_id' => $jobListing->id,
        ]);
    }

    public function unsave(User $user, JobListing $jobListing): bool
    {
        return SavedJob::query()
            ->where('user_id', $user->id)
            ->where('job_listing_id', $jobListing->id)
            ->delete() > 0;
    }

    public function isSaved(User $user, int $jobListingId): bool
    {
        return SavedJob::query()
            ->where('user_id', $user->id)
            ->where('job_listing_id', $jobListingId)
            ->exists();
    }

    public function paginateForUser(User $user, int $perPage): LengthAwarePaginator
    {
        return SavedJob::query()
            ->where('user_id', $user->id)
            ->with(['jobListing' => fn ($q) => $q->with('user:id,name,role')])
            ->latest()
            ->paginate($perPage);
    }

    public function savedJobListingIdsForUser(User $user, array $jobListingIds): array
    {
        if ($jobListingIds === []) {
            return [];
        }

        return SavedJob::query()
            ->where('user_id', $user->id)
            ->whereIn('job_listing_id', $jobListingIds)
            ->pluck('job_listing_id')
            ->all();
    }
}
