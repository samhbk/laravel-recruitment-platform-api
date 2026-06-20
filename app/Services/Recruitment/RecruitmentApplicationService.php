<?php

declare(strict_types=1);

namespace App\Services\Recruitment;

use App\Actions\Application\ApplyToJobAction;
use App\Actions\Application\UpdateApplicationStatusAction;
use App\Application\DTOs\ApplyToJobData;
use App\Domain\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;

/**
 * Coordinates candidate application flows (submit + employer-side status changes).
 */
final readonly class RecruitmentApplicationService
{
    public function __construct(
        private ApplyToJobAction $applyToJob,
        private UpdateApplicationStatusAction $updateApplicationStatus,
    ) {}

    public function submitApplication(User $candidate, ApplyToJobData $data): Application
    {
        return $this->applyToJob->execute($candidate, $data);
    }

    public function changeStatus(Application $application, ApplicationStatus $newStatus): Application
    {
        return $this->updateApplicationStatus->execute($application, $newStatus);
    }
}
