<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Enums\ApplicationStatus;
use App\Domain\Exceptions\DomainException;
use App\Domain\Repositories\ApplicationRepositoryInterface;
use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentApplicationRepository implements ApplicationRepositoryInterface
{
    public function create(JobListing $jobListing, User $user, array $data): Application
    {
        if ($this->userHasApplied($jobListing->id, $user->id)) {
            throw new DomainException('You have already applied to this job.');
        }
        if (! $jobListing->is_published) {
            throw new DomainException('This job is not accepting applications.');
        }

        return Application::create([
            'job_listing_id' => $jobListing->id,
            'user_id' => $user->id,
            'status' => ApplicationStatus::Pending->value,
            'cover_letter' => $data['cover_letter'] ?? null,
            'resume_path' => $data['resume_path'] ?? null,
        ]);
    }

    public function updateStatus(Application $application, string $status): Application
    {
        if (ApplicationStatus::tryFrom($status) === null) {
            throw new DomainException('Invalid status.');
        }
        $application->update([
            'status' => $status,
            'reviewed_at' => $application->reviewed_at ?? now(),
        ]);

        return $application->fresh();
    }

    public function getByUserIdPaginated(int $userId, int $perPage): LengthAwarePaginator
    {
        return Application::query()
            ->where('user_id', $userId)
            ->with('jobListing')
            ->latest()
            ->paginate($perPage);
    }

    public function getByJobListingIdPaginated(int $jobListingId, int $perPage): LengthAwarePaginator
    {
        return Application::query()
            ->where('job_listing_id', $jobListingId)
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    public function userHasApplied(int $jobListingId, int $userId): bool
    {
        return Application::query()
            ->where('job_listing_id', $jobListingId)
            ->where('user_id', $userId)
            ->exists();
    }
}
