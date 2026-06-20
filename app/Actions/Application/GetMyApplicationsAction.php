<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Domain\Repositories\ApplicationRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetMyApplicationsAction
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
    ) {}

    public function execute(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applicationRepository->getByUserIdPaginated($user->id, $perPage);
    }
}
