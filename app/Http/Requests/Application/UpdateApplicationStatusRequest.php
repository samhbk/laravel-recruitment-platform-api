<?php

declare(strict_types=1);

namespace App\Http\Requests\Application;

use App\Domain\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('application'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:'.implode(',', ApplicationStatus::values())],
        ];
    }
}
