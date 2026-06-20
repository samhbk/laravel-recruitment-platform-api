<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class ApplyToJobData
{
    public function __construct(
        public int $jobListingId,
        public ?string $coverLetter = null,
        public ?string $resumePath = null,
    ) {}
}
