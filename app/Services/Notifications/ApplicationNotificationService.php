<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Domain\Enums\ApplicationStatus;
use App\Models\Application;
use App\Notifications\ApplicationStatusChangedNotification;
use App\Notifications\NewApplicationReceivedNotification;

final class ApplicationNotificationService
{
    public function notifyEmployerNewApplication(Application $application): void
    {
        $application->loadMissing('jobListing.user', 'user');
        $employer = $application->jobListing->user;
        $employer->notify(new NewApplicationReceivedNotification($application));
    }

    public function notifyCandidateStatusChanged(Application $application, ApplicationStatus $previousStatus): void
    {
        if ($application->status === $previousStatus) {
            return;
        }

        $application->loadMissing('user');
        $application->user->notify(new ApplicationStatusChangedNotification($application, $previousStatus));
    }
}
