<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Company\CreateCompanyAction;
use App\Actions\Company\UpdateCompanyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateMyCompanyProfileRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CompanyController extends Controller
{
    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company);
    }

    public function store(StoreCompanyRequest $request, CreateCompanyAction $action): JsonResponse
    {
        $company = $action->execute($request->user(), $request->validated());

        return (new CompanyResource($company))->response()->setStatusCode(201);
    }

    public function me(Request $request): CompanyResource|JsonResponse
    {
        $company = $request->user()->company;
        if ($company === null) {
            return response()->json(['message' => 'Company profile not found'], 404);
        }

        return new CompanyResource($company);
    }

    public function updateMe(UpdateMyCompanyProfileRequest $request, UpdateCompanyAction $action): CompanyResource
    {
        $company = $request->user()->company;
        $updated = $action->execute($company, $request->validated());

        return new CompanyResource($updated);
    }
}
