<?php

namespace App\Modules\Kesehatan\VitalLogs\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetVitalHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'days' => 'nullable|integer|min:1|max:365',
        ];
    }
}
