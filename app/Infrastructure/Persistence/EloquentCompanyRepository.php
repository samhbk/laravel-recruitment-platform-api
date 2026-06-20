<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\CompanyRepositoryInterface;
use App\Models\Company;
use App\Models\User;

class EloquentCompanyRepository implements CompanyRepositoryInterface
{
    public function findByUserId(int $userId): ?Company
    {
        return Company::query()->where('user_id', $userId)->first();
    }

    public function findBySlug(string $slug): ?Company
    {
        return Company::query()->where('slug', $slug)->first();
    }

    public function create(User $user, array $data): Company
    {
        $data['user_id'] = $user->id;

        return Company::create($data);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update($data);

        return $company->fresh();
    }
}
