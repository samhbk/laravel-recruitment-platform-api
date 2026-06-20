<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Domain\Repositories\CompanyRepositoryInterface;
use App\Models\Company;
use App\Models\User;

final readonly class CreateCompanyAction
{
    public function __construct(
        private CompanyRepositoryInterface $companies,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(User $user, array $data): Company
    {
        return $this->companies->create($user, $data);
    }
}
