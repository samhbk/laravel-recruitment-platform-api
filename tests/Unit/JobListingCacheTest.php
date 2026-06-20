<?php

namespace Tests\Unit;

use App\Domain\Repositories\JobListingFilters;
use App\Infrastructure\Persistence\EloquentJobListingRepository;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class JobListingCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_listing_bumps_cache_version_so_catalog_refreshes(): void
    {
        Cache::put('job_listings:list_version', 1);

        $user = User::factory()->company()->create();
        $repository = app(EloquentJobListingRepository::class);

        JobListing::factory()->count(2)->create([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $repository->getPublishedPaginated(15, new JobListingFilters);
        $this->assertSame(1, (int) Cache::get('job_listings:list_version'));

        $repository->create($user, [
            'title' => 'New Cached Listing',
            'description' => 'Cache invalidation test.',
            'company_name' => 'Cache Co',
            'location' => 'Remote',
            'employment_type' => 'remote',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->assertSame(2, (int) Cache::get('job_listings:list_version'));
    }
}
