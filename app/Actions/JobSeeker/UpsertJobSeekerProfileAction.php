<?php

declare(strict_types=1);

namespace App\Actions\JobSeeker;

use App\Domain\Repositories\JobSeekerProfileRepositoryInterface;
use App\Models\JobSeekerProfile;
use App\Models\User;

final readonly class UpsertJobSeekerProfileAction
{
    public function __construct(
        private JobSeekerProfileRepositoryInterface $profiles,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(User $user, array $data): JobSeekerProfile
    {
        return $this->profiles->upsertForUser($user, $data);
    }
}
