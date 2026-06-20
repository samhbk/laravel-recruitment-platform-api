<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Enums\EmploymentType;

readonly class SalaryAnalyticsFilters
{
    public function __construct(
        public ?EmploymentType $employmentType = null,
        public ?string $location = null,
    ) {}

    public static function fromArray(array $input): self
    {
        $type = isset($input['employment_type'])
            ? EmploymentType::tryFrom($input['employment_type'])
            : null;

        return new self(
            employmentType: $type,
            location: $input['location'] ?? null,
        );
    }
}
