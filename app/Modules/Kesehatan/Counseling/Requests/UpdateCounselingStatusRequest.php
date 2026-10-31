<?php

namespace App\Modules\Kesehatan\Counseling\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Kesehatan\Counseling\Enums\CounselingSessionStatus;

class UpdateCounselingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:' . implode(',', CounselingSessionStatus::values()),
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
