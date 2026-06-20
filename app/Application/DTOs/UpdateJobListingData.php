<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use App\Domain\Enums\EmploymentType;

readonly class UpdateJobListingData
{
    /**
     * @param  list<string>|null  $skills
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $companyName = null,
        public ?string $location = null,
        public ?EmploymentType $employmentType = null,
        public ?float $salaryMin = null,
        public ?float $salaryMax = null,
        public ?string $salaryCurrency = null,
        public ?bool $isPublished = null,
        public ?int $companyId = null,
        public bool $companyIdTouched = false,
        public ?array $skills = null,
        public bool $skillsTouched = false,
    ) {}

    public function toArray(): array
    {
        $data = [];
        if ($this->title !== null) {
            $data['title'] = $this->title;
        }
        if ($this->description !== null) {
            $data['description'] = $this->description;
        }
        if ($this->companyName !== null) {
            $data['company_name'] = $this->companyName;
        }
        if ($this->location !== null) {
            $data['location'] = $this->location;
        }
        if ($this->employmentType !== null) {
            $data['employment_type'] = $this->employmentType->value;
        }
        if ($this->salaryMin !== null) {
            $data['salary_min'] = $this->salaryMin;
        }
        if ($this->salaryMax !== null) {
            $data['salary_max'] = $this->salaryMax;
        }
        if ($this->salaryCurrency !== null) {
            $data['salary_currency'] = $this->salaryCurrency;
        }
        if ($this->isPublished !== null) {
            $data['is_published'] = $this->isPublished;
        }
        if ($this->companyIdTouched) {
            $data['company_id'] = $this->companyId;
        }
        if ($this->skillsTouched) {
            $data['skills'] = $this->skills ?? [];
        }

        return $data;
    }
}
