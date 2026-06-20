<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

interface SalaryAnalyticsRepositoryInterface
{
    /**
     * @return array{overview: array, by_employment_type: array}
     */
    public function getAnalytics(SalaryAnalyticsFilters $filters): array;
}
