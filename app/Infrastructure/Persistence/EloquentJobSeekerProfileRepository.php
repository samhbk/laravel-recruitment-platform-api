<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\JobSeekerProfileRepositoryInterface;
use App\Models\JobSeekerProfile;
use App\Models\User;

class EloquentJobSeekerProfileRepository implements JobSeekerProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?JobSeekerProfile
    {
        return JobSeekerProfile::query()->where('user_id', $userId)->first();
    }

    public function upsertForUser(User $user, array $data): JobSeekerProfile
    {
        $profile = $this->findByUserId($user->id);

        if ($profile === null) {
            $data['user_id'] = $user->id;

            return JobSeekerProfile::create($data);
        }

        $profile->update($data);

        return $profile->fresh();
    }
}
