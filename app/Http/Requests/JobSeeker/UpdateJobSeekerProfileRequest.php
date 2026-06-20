<?php

declare(strict_types=1);

namespace App\Http\Requests\JobSeeker;

use App\Domain\Enums\EmploymentType;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateJobSeekerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isJobSeeker() || $this->user()->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'headline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:64'],
            'preferred_locations' => ['nullable', 'array'],
            'preferred_locations.*' => ['string', 'max:255'],
            'preferred_employment_types' => ['nullable', 'array'],
            'preferred_employment_types.*' => ['string', 'in:'.implode(',', EmploymentType::values())],
        ];
    }
}
