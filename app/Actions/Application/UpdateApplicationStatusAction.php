<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Domain\Enums\ApplicationStatus;
use App\Domain\Repositories\ApplicationRepositoryInterface;
use App\Models\Application;
use App\Services\Notifications\ApplicationNotificationService;

final readonly class UpdateApplicationStatusAction
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
        private ApplicationNotificationService $applicationNotifications,
    ) {}

    public function execute(Application $application, ApplicationStatus $status): Application
    {
        $previous = $application->status;
        if ($previous === $status) {
            return $application;
        }

        $updated = $this->applicationRepository->updateStatus($application, $status->value);
        $this->applicationNotifications->notifyCandidateStatusChanged($updated, $previous);

        return $updated;
    }
}
