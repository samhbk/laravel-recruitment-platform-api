<?php

declare(strict_types=1);

namespace App\Http\Requests\JobListing;

use App\Domain\Enums\EmploymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateJobListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('job_listing'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'company_name' => ['sometimes', 'string', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
            'employment_type' => ['sometimes', 'string', 'in:'.implode(',', EmploymentType::values())],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'is_published' => ['boolean'],
            'company_id' => [
                'nullable',
                'integer',
                Rule::exists('companies', 'id')->where(fn ($q) => $q->where('user_id', $this->user()->id)),
            ],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:64'],
        ];
    }
}
