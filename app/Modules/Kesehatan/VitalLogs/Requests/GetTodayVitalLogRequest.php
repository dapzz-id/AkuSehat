<?php

namespace App\Modules\Kesehatan\VitalLogs\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTodayVitalLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }
}
