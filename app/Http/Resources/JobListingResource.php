<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin JobListing
 */
final class JobListingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'skills' => $this->skills ?? [],
            'company_id' => $this->company_id,
            'company_name' => $this->company_name,
            'location' => $this->location,
            'employment_type' => $this->employment_type?->value ?? $this->employment_type,
            'salary_min' => $this->salary_min !== null ? (float) $this->salary_min : null,
            'salary_max' => $this->salary_max !== null ? (float) $this->salary_max : null,
            'salary_currency' => $this->salary_currency,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'company' => $this->whenLoaded('company', fn () => new CompanyResource($this->company)),
            'is_saved' => $this->when(
                $request->user() !== null,
                fn () => $request->attributes->has('saved_job_ids')
                    && in_array($this->id, $request->attributes->get('saved_job_ids', []), true),
            ),
        ];
    }
}
