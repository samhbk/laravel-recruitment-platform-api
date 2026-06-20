<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Application\GetListingApplicationsAction;
use App\Actions\Application\GetMyApplicationsAction;
use App\Application\DTOs\ApplyToJobData;
use App\Domain\Enums\ApplicationStatus;
use App\Domain\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Application\ApplyToJobRequest;
use App\Http\Requests\Application\UpdateApplicationStatusRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\JobListing;
use App\Services\Recruitment\RecruitmentApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ApplicationController extends Controller
{
    private const PER_PAGE = 15;

    public function index(Request $request, GetMyApplicationsAction $action): AnonymousResourceCollection
    {
        $applications = $action->execute($request->user(), self::PER_PAGE);

        return ApplicationResource::collection($applications);
    }

    public function myApplications(Request $request, GetMyApplicationsAction $action): AnonymousResourceCollection
    {
        return $this->index($request, $action);
    }

    public function store(ApplyToJobRequest $request, RecruitmentApplicationService $applications): ApplicationResource|JsonResponse
    {
        try {
            $data = new ApplyToJobData(
                jobListingId: (int) $request->validated('job_listing_id'),
                coverLetter: $request->validated('cover_letter'),
                resumePath: $request->validated('resume_path'),
            );
            $application = $applications->submitApplication($request->user(), $data);
            $application->load('jobListing');
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return (new ApplicationResource($application))->response()->setStatusCode(201);
    }

    public function show(Request $request, Application $application): ApplicationResource|JsonResponse
    {
        $this->authorize('view', $application);

        $application->load(['jobListing', 'user']);

        return new ApplicationResource($application);
    }

    public function updateStatus(
        UpdateApplicationStatusRequest $request,
        Application $application,
        RecruitmentApplicationService $applications,
    ): ApplicationResource|JsonResponse {
        try {
            $status = ApplicationStatus::from($request->validated('status'));
            $application = $applications->changeStatus($application, $status);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new ApplicationResource($application);
    }

    public function listingApplications(
        JobListing $jobListing,
        Request $request,
        GetListingApplicationsAction $action,
    ): AnonymousResourceCollection|JsonResponse {
        $this->authorize('update', $jobListing);
        $applications = $action->execute($jobListing, self::PER_PAGE);

        return ApplicationResource::collection($applications);
    }
}
