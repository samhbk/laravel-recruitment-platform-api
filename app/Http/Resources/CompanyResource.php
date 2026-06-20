<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Company
 */
final class CompanyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'website' => $this->website,
            'logo_url' => $this->logo_url,
            'industry' => $this->industry,
            'company_size' => $this->company_size,
            'headquarters_location' => $this->headquarters_location,
            'is_verified' => $this->is_verified,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
