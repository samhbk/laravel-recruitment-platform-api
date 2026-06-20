<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Application\DTOs\CreateJobListingData;
use App\Domain\Repositories\JobListingRepositoryInterface;
use App\Models\JobListing;
use App\Models\User;

final readonly class CreateJobListingAction
{
    public function __construct(
        private JobListingRepositoryInterface $jobListingRepository,
    ) {}

    public function execute(User $user, CreateJobListingData $data): JobListing
    {
        $payload = [
            'title' => $data->title,
            'description' => $data->description,
            'company_name' => $data->companyName,
            'location' => $data->location,
            'employment_type' => $data->employmentType->value,
            'salary_min' => $data->salaryMin,
            'salary_max' => $data->salaryMax,
            'salary_currency' => $data->salaryCurrency,
            'is_published' => $data->isPublished,
            'company_id' => $data->companyId,
            'skills' => $data->skills,
        ];
        if ($data->isPublished) {
            $payload['published_at'] = now();
        }

        return $this->jobListingRepository->create($user, $payload);
    }
}
