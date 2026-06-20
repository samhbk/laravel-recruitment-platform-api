<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\JobListingFilters;
use App\Domain\Repositories\JobListingRepositoryInterface;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EloquentJobListingRepository implements JobListingRepositoryInterface
{
    private const CACHE_TTL = 3600;

    private const LIST_VERSION_KEY = 'job_listings:list_version';

    private const LIST_CACHE_KEY = 'job_listings:v%s:page:%s:filter:%s';

    public function getPublishedPaginated(int $perPage, JobListingFilters $filters): LengthAwarePaginator
    {
        $page = (int) request()->get('page', 1);
        $version = $this->listCacheVersion();
        $cacheKey = sprintf(
            self::LIST_CACHE_KEY,
            $version,
            $page,
            md5(json_encode([
                $filters->employmentType?->value,
                $filters->location,
                $filters->search,
                $filters->salaryMin,
                $filters->salaryMax,
            ])),
        );

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($perPage, $filters) {
            return $this->queryPublished($filters)->paginate($perPage);
        });
    }

    public function findPublished(int $id): ?JobListing
    {
        $cacheKey = "job_listing:{$id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            $listing = JobListing::query()
                ->where('id', $id)
                ->where('is_published', true)
                ->with(['user:id,name,role', 'company'])
                ->first();

            return $listing;
        });
    }

    public function find(int $id): ?JobListing
    {
        return JobListing::query()->find($id);
    }

    public function create(User $user, array $data): JobListing
    {
        $data['user_id'] = $user->id;
        $data['slug'] = $this->uniqueSlug($data['title'] ?? '');
        $listing = JobListing::create($data);
        $this->bumpListCacheVersion();

        return $listing;
    }

    public function update(JobListing $listing, array $data): JobListing
    {
        if (isset($data['title']) && $data['title'] !== $listing->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $listing->id);
        }
        $listing->update($data);
        Cache::forget("job_listing:{$listing->id}");
        $this->bumpListCacheVersion();

        return $listing->fresh();
    }

    public function delete(JobListing $listing): void
    {
        $listing->delete();
        Cache::forget("job_listing:{$listing->id}");
        $this->bumpListCacheVersion();
    }

    public function getByUserIdPaginated(int $userId, int $perPage): LengthAwarePaginator
    {
        return JobListing::query()
            ->where('user_id', $userId)
            ->with(['company'])
            ->latest()
            ->paginate($perPage);
    }

    private function queryPublished(JobListingFilters $filters): Builder
    {
        $query = JobListing::query()
            ->where('is_published', true)
            ->with(['user:id,name,role', 'company'])
            ->latest('published_at');

        if ($filters->employmentType !== null) {
            $query->where('employment_type', $filters->employmentType->value);
        }
        if ($filters->location !== null && $filters->location !== '') {
            $query->where('location', 'like', '%'.$filters->location.'%');
        }
        if ($filters->search !== null && $filters->search !== '') {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%'.$filters->search.'%')
                    ->orWhere('company_name', 'like', '%'.$filters->search.'%');
            });
        }
        if ($filters->salaryMin !== null) {
            $query->where(function ($q) use ($filters) {
                $q->whereNull('salary_max')
                    ->orWhere('salary_max', '>=', $filters->salaryMin);
            });
        }
        if ($filters->salaryMax !== null) {
            $query->where(function ($q) use ($filters) {
                $q->whereNull('salary_min')
                    ->orWhere('salary_min', '<=', $filters->salaryMax);
            });
        }

        return $query;
    }

    private function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $base = $slug;
        $count = 0;
        while (JobListing::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base.'-'.(++$count);
        }

        return $slug;
    }

    private function listCacheVersion(): int
    {
        return (int) Cache::get(self::LIST_VERSION_KEY, 1);
    }

    private function bumpListCacheVersion(): void
    {
        Cache::increment(self::LIST_VERSION_KEY);
    }
}
