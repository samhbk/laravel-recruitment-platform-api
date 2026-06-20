<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\JobSeekerProfile;
use App\Models\User;

interface JobSeekerProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?JobSeekerProfile;

    public function upsertForUser(User $user, array $data): JobSeekerProfile;
}
