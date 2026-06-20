<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Application\DTOs\UpdateJobListingData;
use App\Domain\Repositories\JobListingRepositoryInterface;
use App\Models\JobListing;

final readonly class UpdateJobListingAction
{
    public function __construct(
        private JobListingRepositoryInterface $jobListingRepository,
    ) {}

    public function execute(JobListing $listing, UpdateJobListingData $data): JobListing
    {
        $payload = $data->toArray();
        if (array_key_exists('is_published', $payload) && $payload['is_published'] === true && ! $listing->is_published) {
            $payload['published_at'] = now();
        }

        return $this->jobListingRepository->update($listing, $payload);
    }
}
