<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\SalaryAnalyticsFilters;
use App\Domain\Repositories\SalaryAnalyticsRepositoryInterface;
use App\Models\JobListing;
use Illuminate\Support\Facades\Cache;

class EloquentSalaryAnalyticsRepository implements SalaryAnalyticsRepositoryInterface
{
    private const CACHE_TTL = 3600;

    private const CACHE_KEY = 'salary_analytics:%s';

    public function getAnalytics(SalaryAnalyticsFilters $filters): array
    {
        $cacheKey = sprintf(self::CACHE_KEY, md5(json_encode([
            $filters->employmentType?->value,
            $filters->location,
        ])));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
            return $this->computeAnalytics($filters);
        });
    }

    /**
     * @return array{overview: array, by_employment_type: array}
     */
    private function computeAnalytics(SalaryAnalyticsFilters $filters): array
    {
        $query = JobListing::query()
            ->where('is_published', true)
            ->whereNotNull('salary_min')
            ->whereNotNull('salary_max');

        if ($filters->employmentType !== null) {
            $query->where('employment_type', $filters->employmentType->value);
        }
        if ($filters->location !== null && $filters->location !== '') {
            $query->where('location', 'like', '%'.$filters->location.'%');
        }

        $stats = $query->clone()
            ->selectRaw('
                COUNT(*) as total_listings,
                AVG(salary_min) as avg_min,
                AVG(salary_max) as avg_max,
                MIN(salary_min) as min_salary,
                MAX(salary_max) as max_salary
            ')
            ->first();

        $byType = JobListing::query()
            ->where('is_published', true)
            ->whereNotNull('salary_min')
            ->whereNotNull('salary_max')
            ->when($filters->location !== null && $filters->location !== '', fn ($q) => $q->where('location', 'like', '%'.$filters->location.'%'))
            ->select('employment_type')
            ->selectRaw('AVG(salary_min) as avg_min, AVG(salary_max) as avg_max, COUNT(*) as count')
            ->groupBy('employment_type')
            ->get()
            ->keyBy('employment_type');

        return [
            'overview' => [
                'total_listings_with_salary' => (int) $stats->total_listings,
                'average_min_salary' => round((float) $stats->avg_min, 2),
                'average_max_salary' => round((float) $stats->avg_max, 2),
                'min_salary_range' => (float) $stats->min_salary,
                'max_salary_range' => (float) $stats->max_salary,
            ],
            'by_employment_type' => $byType->map(fn ($row) => [
                'avg_min_salary' => round((float) $row->avg_min, 2),
                'avg_max_salary' => round((float) $row->avg_max, 2),
                'count' => (int) $row->count,
            ])->all(),
        ];
    }
}
