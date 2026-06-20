<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Application
 */
final class ApplicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_listing_id' => $this->job_listing_id,
            'user_id' => $this->user_id,
            'status' => $this->status?->value ?? $this->status,
            'cover_letter' => $this->cover_letter,
            'resume_path' => $this->resume_path,
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'notes' => $this->notes,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'job_listing' => $this->whenLoaded('jobListing', fn () => new JobListingResource($this->jobListing)),
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
        ];
    }
}
