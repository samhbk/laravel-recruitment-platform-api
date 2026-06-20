<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use App\Domain\Enums\EmploymentType;

readonly class CreateJobListingData
{
    /**
     * @param  list<string>|null  $skills
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $companyName,
        public string $location,
        public EmploymentType $employmentType,
        public ?float $salaryMin = null,
        public ?float $salaryMax = null,
        public string $salaryCurrency = 'USD',
        public bool $isPublished = false,
        public ?int $companyId = null,
        public ?array $skills = null,
    ) {}
}
