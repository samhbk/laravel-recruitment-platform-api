<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Repositories\SavedJobRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Services\Recommendations\JobRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class RecommendationController extends Controller
{
    public function index(
        Request $request,
        JobRecommendationService $recommendations,
        SavedJobRepositoryInterface $savedJobs,
    ): AnonymousResourceCollection {
        $jobs = $recommendations->forUser($request->user(), 20);
        $savedIds = $savedJobs->savedJobListingIdsForUser($request->user(), $jobs->pluck('id')->all());
        $request->attributes->set('saved_job_ids', $savedIds);
        request()->attributes->set('saved_job_ids', $savedIds);

        return JobListingResource::collection($jobs);
    }
}
