<?php

namespace App\Modules\SuperAdmin\SystemConfig\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwarePlatform;

class HistoryUpdateApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => ['nullable', 'string', Rule::in(SoftwarePlatform::values())],
        ];
    }
}
