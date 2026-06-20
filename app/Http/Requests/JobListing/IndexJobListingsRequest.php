<?php

declare(strict_types=1);

namespace App\Http\Requests\JobListing;

use App\Domain\Enums\EmploymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class IndexJobListingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'employment_type' => ['sometimes', 'nullable', 'string', Rule::in(EmploymentType::values())],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'salary_min' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'salary_max' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
