<?php

declare(strict_types=1);

namespace App\Actions\SalaryAnalytics;

use App\Domain\Repositories\SalaryAnalyticsFilters;
use App\Domain\Repositories\SalaryAnalyticsRepositoryInterface;

final readonly class GetSalaryAnalyticsAction
{
    public function __construct(
        private SalaryAnalyticsRepositoryInterface $salaryAnalyticsRepository,
    ) {}

    /**
     * @return array{overview: array, by_employment_type: array}
     */
    public function execute(SalaryAnalyticsFilters $filters): array
    {
        return $this->salaryAnalyticsRepository->getAnalytics($filters);
    }
}
