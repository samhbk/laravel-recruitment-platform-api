<?php

declare(strict_types=1);

namespace App\Http\Requests\JobListing;

use App\Domain\Enums\EmploymentType;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreJobListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', JobListing::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('company_id')) {
            $company = Company::query()->find($this->input('company_id'));
            if ($company !== null && (int) $company->user_id === (int) $this->user()->id) {
                $this->mergeIfMissing('company_name', $company->name);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'company_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', 'string', 'in:'.implode(',', EmploymentType::values())],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
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
