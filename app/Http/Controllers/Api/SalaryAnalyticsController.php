<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SalaryAnalytics\GetSalaryAnalyticsAction;
use App\Domain\Repositories\SalaryAnalyticsFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\SalaryAnalytics\SalaryAnalyticsIndexRequest;
use Illuminate\Http\JsonResponse;

final class SalaryAnalyticsController extends Controller
{
    public function index(SalaryAnalyticsIndexRequest $request, GetSalaryAnalyticsAction $action): JsonResponse
    {
        $filters = SalaryAnalyticsFilters::fromArray($request->validated());
        $analytics = $action->execute($filters);

        return response()->json($analytics);
    }
}
