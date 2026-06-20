<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\JobSeekerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin JobSeekerProfile
 */
final class JobSeekerProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'headline' => $this->headline,
            'bio' => $this->bio,
            'skills' => $this->skills ?? [],
            'preferred_locations' => $this->preferred_locations ?? [],
            'preferred_employment_types' => $this->preferred_employment_types ?? [],
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
