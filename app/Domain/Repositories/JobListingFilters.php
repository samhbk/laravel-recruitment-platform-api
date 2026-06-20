<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Enums\EmploymentType;

readonly class JobListingFilters
{
    public function __construct(
        public ?EmploymentType $employmentType = null,
        public ?string $location = null,
        public ?string $search = null,
        public ?float $salaryMin = null,
        public ?float $salaryMax = null,
    ) {}

    public static function fromArray(array $input): self
    {
        $type = isset($input['employment_type'])
            ? EmploymentType::tryFrom($input['employment_type'])
            : null;

        $salaryMin = isset($input['salary_min']) && $input['salary_min'] !== '' && $input['salary_min'] !== null
            ? (float) $input['salary_min']
            : null;
        $salaryMax = isset($input['salary_max']) && $input['salary_max'] !== '' && $input['salary_max'] !== null
            ? (float) $input['salary_max']
            : null;

        return new self(
            employmentType: $type,
            location: $input['location'] ?? null,
            search: $input['search'] ?? null,
            salaryMin: $salaryMin,
            salaryMax: $salaryMax,
        );
    }
}
