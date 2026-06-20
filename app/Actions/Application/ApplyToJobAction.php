<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Application\DTOs\ApplyToJobData;
use App\Domain\Repositories\ApplicationRepositoryInterface;
use App\Domain\Repositories\JobListingRepositoryInterface;
use App\Models\Application;
use App\Models\User;
use App\Services\Notifications\ApplicationNotificationService;

final readonly class ApplyToJobAction
{
    public function __construct(
        private JobListingRepositoryInterface $jobListingRepository,
        private ApplicationRepositoryInterface $applicationRepository,
        private ApplicationNotificationService $applicationNotifications,
    ) {}

    public function execute(User $user, ApplyToJobData $data): Application
    {
        $jobListing = $this->jobListingRepository->find($data->jobListingId);
        if ($jobListing === null) {
            throw new \InvalidArgumentException('Job listing not found.');
        }

        $application = $this->applicationRepository->create($jobListing, $user, [
            'cover_letter' => $data->coverLetter,
            'resume_path' => $data->resumePath,
        ]);

        $this->applicationNotifications->notifyEmployerNewApplication($application);

        return $application;
    }
}
