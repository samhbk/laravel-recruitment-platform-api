<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Domain\Repositories\CompanyRepositoryInterface;
use App\Models\Company;

final readonly class UpdateCompanyAction
{
    public function __construct(
        private CompanyRepositoryInterface $companies,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Company $company, array $data): Company
    {
        return $this->companies->update($company, $data);
    }
}
