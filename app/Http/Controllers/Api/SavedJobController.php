<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SavedJob\SaveJobAction;
use App\Actions\SavedJob\UnsaveJobAction;
use App\Domain\Exceptions\DomainException;
use App\Domain\Repositories\SavedJobRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

final class SavedJobController extends Controller
{
    private const PER_PAGE = 15;

    public function index(Request $request, SavedJobRepositoryInterface $savedJobs): AnonymousResourceCollection
    {
        $paginator = $savedJobs->paginateForUser($request->user(), self::PER_PAGE);
        $ids = $paginator->getCollection()->map(fn ($row) => $row->job_listing_id)->all();
        $request->attributes->set('saved_job_ids', $ids);
        request()->attributes->set('saved_job_ids', $ids);

        $listings = $paginator->getCollection()->map(fn ($row) => $row->jobListing)->filter()->values();

        $wrapped = new LengthAwarePaginator(
            $listings,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return JobListingResource::collection($wrapped);
    }

    public function store(Request $request, JobListing $jobListing, SaveJobAction $action): JsonResponse
    {
        try {
            $action->execute($request->user(), $jobListing);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'saved' => true,
            'job_listing_id' => $jobListing->id,
        ], 201);
    }

    public function destroy(Request $request, JobListing $jobListing, UnsaveJobAction $action): JsonResponse
    {
        $removed = $action->execute($request->user(), $jobListing);
        if (! $removed) {
            return response()->json(['message' => 'Saved job not found'], 404);
        }

        return response()->json(null, 204);
    }
}
