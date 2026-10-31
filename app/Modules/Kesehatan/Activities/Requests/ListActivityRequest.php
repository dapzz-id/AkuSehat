<?php

namespace App\Modules\Kesehatan\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Kesehatan\Activities\Enums\ActivityType;

class ListActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::in(ActivityType::values())],
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
