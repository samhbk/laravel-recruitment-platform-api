<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\JobSeeker\UpsertJobSeekerProfileAction;
use App\Domain\Repositories\JobSeekerProfileRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobSeeker\UpdateJobSeekerProfileRequest;
use App\Http\Resources\JobSeekerProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class JobSeekerProfileController extends Controller
{
    public function show(Request $request, JobSeekerProfileRepositoryInterface $profiles): JobSeekerProfileResource|JsonResponse
    {
        $profile = $profiles->findByUserId($request->user()->id);
        if ($profile === null) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return new JobSeekerProfileResource($profile);
    }

    public function update(UpdateJobSeekerProfileRequest $request, UpsertJobSeekerProfileAction $action): JobSeekerProfileResource
    {
        $profile = $action->execute($request->user(), $request->validated());

        return new JobSeekerProfileResource($profile);
    }
}
