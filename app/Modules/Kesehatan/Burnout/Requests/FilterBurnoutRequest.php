<?php

namespace App\Modules\Kesehatan\Burnout\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Kesehatan\Burnout\Enums\BurnoutLevel;

class FilterBurnoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'divisi_id' => 'nullable|string',
            'burnout_level' => 'nullable|in:' . implode(',', BurnoutLevel::values()),
            'search' => 'nullable|string|max:100',
        ];
    }
}
