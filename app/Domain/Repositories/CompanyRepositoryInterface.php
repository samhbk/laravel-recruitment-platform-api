<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Company;
use App\Models\User;

interface CompanyRepositoryInterface
{
    public function findByUserId(int $userId): ?Company;

    public function findBySlug(string $slug): ?Company;

    public function create(User $user, array $data): Company;

    public function update(Company $company, array $data): Company;
}
