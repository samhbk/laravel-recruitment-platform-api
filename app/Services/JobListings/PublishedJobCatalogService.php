<?php

declare(strict_types=1);

namespace App\Services\JobListings;

use App\Actions\JobListing\GetPublishedListingAction;
use App\Actions\JobListing\GetPublishedListingsAction;
use App\Domain\Repositories\JobListingFilters;
use App\Models\JobListing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Application service for public (published) job catalog reads.
 * Controllers stay thin; use-case Actions remain the core logic.
 */
final readonly class PublishedJobCatalogService
{
    public function __construct(
        private GetPublishedListingsAction $publishedListings,
        private GetPublishedListingAction $publishedListing,
    ) {}

    public function paginatePublished(int $perPage, JobListingFilters $filters): LengthAwarePaginator
    {
        return $this->publishedListings->execute($perPage, $filters);
    }

    public function findPublished(int $id): ?JobListing
    {
        return $this->publishedListing->execute($id);
    }
}
