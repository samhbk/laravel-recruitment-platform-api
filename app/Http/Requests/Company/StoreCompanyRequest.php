<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

final class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Company::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:2048'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'industry' => ['nullable', 'string', 'max:128'],
            'company_size' => ['nullable', 'string', 'max:64'],
            'headquarters_location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
