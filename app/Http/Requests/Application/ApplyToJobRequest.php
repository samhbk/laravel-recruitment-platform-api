<?php

declare(strict_types=1);

namespace App\Http\Requests\Application;

use App\Domain\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class ApplyToJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role, [UserRole::JobSeeker, UserRole::Admin], true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'job_listing_id' => ['required', 'integer', 'exists:job_listings,id'],
            'cover_letter' => ['nullable', 'string'],
            'resume_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
