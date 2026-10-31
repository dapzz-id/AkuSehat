<?php

namespace App\Modules\Kesehatan\Counseling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCounselingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'topic' => 'required|string|max:150',
            'session_date' => 'required|date',
            'consultant_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'is_confidential' => 'nullable|boolean',
        ];
    }
}
