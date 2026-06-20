<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateMyCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->user()->company;

        return $company !== null && $this->user()->can('update', $company);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:2048'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'industry' => ['nullable', 'string', 'max:128'],
            'company_size' => ['nullable', 'string', 'max:64'],
            'headquarters_location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
