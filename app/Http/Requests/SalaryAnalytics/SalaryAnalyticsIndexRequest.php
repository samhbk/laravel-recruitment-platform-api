<?php

declare(strict_types=1);

namespace App\Http\Requests\SalaryAnalytics;

use App\Domain\Enums\EmploymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SalaryAnalyticsIndexRequest extends FormRequest
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
        ];
    }
}
