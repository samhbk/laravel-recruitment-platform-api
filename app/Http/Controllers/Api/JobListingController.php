<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\JobListing\CreateJobListingAction;
use App\Actions\JobListing\DeleteJobListingAction;
use App\Actions\JobListing\GetMyListingsAction;
use App\Actions\JobListing\UpdateJobListingAction;
use App\Application\DTOs\CreateJobListingData;
use App\Application\DTOs\UpdateJobListingData;
use App\Domain\Enums\EmploymentType;
use App\Domain\Repositories\JobListingFilters;
use App\Domain\Repositories\SavedJobRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobListing\IndexJobListingsRequest;
use App\Http\Requests\JobListing\StoreJobListingRequest;
use App\Http\Requests\JobListing\UpdateJobListingRequest;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use App\Services\JobListings\PublishedJobCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class JobListingController extends Controller
{
    private const PER_PAGE = 15;

    public function index(
        IndexJobListingsRequest $request,
        PublishedJobCatalogService $catalog,
        SavedJobRepositoryInterface $savedJobs,
    ): AnonymousResourceCollection {
        $filters = JobListingFilters::fromArray($request->validated());
        $listings = $catalog->paginatePublished(self::PER_PAGE, $filters);

        if ($user = $request->user()) {
            $pageIds = $listings->pluck('id')->all();
            $savedIds = $savedJobs->savedJobListingIdsForUser($user, $pageIds);
            $request->attributes->set('saved_job_ids', $savedIds);
            request()->attributes->set('saved_job_ids', $savedIds);
        }

        return JobListingResource::collection($listings);
    }

    public function show(
        Request $request,
        JobListing $jobListing,
        PublishedJobCatalogService $catalog,
        SavedJobRepositoryInterface $savedJobs,
    ): JobListingResource|JsonResponse {
        $listing = $catalog->findPublished($jobListing->id);

        if ($listing === null) {
            return response()->json(['message' => 'Job listing not found or not published'], 404);
        }

        if ($user = $request->user()) {
            $savedIds = $savedJobs->savedJobListingIdsForUser($user, [$listing->id]);
            $request->attributes->set('saved_job_ids', $savedIds);
            request()->attributes->set('saved_job_ids', $savedIds);
        }

        return new JobListingResource($listing);
    }

    public function store(StoreJobListingRequest $request, CreateJobListingAction $action): JsonResponse
    {
        $validated = $request->validated();
        $employmentType = EmploymentType::from($validated['employment_type']);

        $data = new CreateJobListingData(
            title: $validated['title'],
            description: $validated['description'],
            companyName: $validated['company_name'],
            location: $validated['location'],
            employmentType: $employmentType,
            salaryMin: isset($validated['salary_min']) ? (float) $validated['salary_min'] : null,
            salaryMax: isset($validated['salary_max']) ? (float) $validated['salary_max'] : null,
            salaryCurrency: $validated['salary_currency'] ?? 'USD',
            isPublished: $validated['is_published'] ?? false,
            companyId: isset($validated['company_id']) ? (int) $validated['company_id'] : null,
            skills: $validated['skills'] ?? null,
        );

        $listing = $action->execute($request->user(), $data);
        $listing->load(['user:id,name,role', 'company']);

        return (new JobListingResource($listing))->response()->setStatusCode(201);
    }

    public function update(
        UpdateJobListingRequest $request,
        JobListing $jobListing,
        UpdateJobListingAction $action,
    ): JobListingResource {
        $validated = $request->validated();
        $data = new UpdateJobListingData(
            title: $validated['title'] ?? null,
            description: $validated['description'] ?? null,
            companyName: $validated['company_name'] ?? null,
            location: $validated['location'] ?? null,
            employmentType: isset($validated['employment_type'])
                ? EmploymentType::from($validated['employment_type'])
                : null,
            salaryMin: array_key_exists('salary_min', $validated) ? (float) $validated['salary_min'] : null,
            salaryMax: array_key_exists('salary_max', $validated) ? (float) $validated['salary_max'] : null,
            salaryCurrency: $validated['salary_currency'] ?? null,
            isPublished: array_key_exists('is_published', $validated) ? (bool) $validated['is_published'] : null,
            companyId: array_key_exists('company_id', $validated) && $validated['company_id'] !== null
                ? (int) $validated['company_id']
                : null,
            companyIdTouched: array_key_exists('company_id', $validated),
            skills: array_key_exists('skills', $validated) ? ($validated['skills'] ?? []) : null,
            skillsTouched: array_key_exists('skills', $validated),
        );

        $listing = $action->execute($jobListing, $data);
        $listing->load(['user:id,name,role', 'company']);

        return new JobListingResource($listing);
    }

    public function destroy(JobListing $jobListing, DeleteJobListingAction $action): JsonResponse
    {
        $this->authorize('delete', $jobListing);
        $action->execute($jobListing);

        return response()->json(null, 204);
    }

    public function myListings(Request $request, GetMyListingsAction $action): AnonymousResourceCollection
    {
        $listings = $action->execute($request->user(), self::PER_PAGE);

        return JobListingResource::collection($listings);
    }
}
