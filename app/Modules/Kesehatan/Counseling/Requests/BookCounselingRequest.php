<?php

namespace App\Modules\Kesehatan\Counseling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookCounselingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'session_date' => 'required|date',
            'topic' => 'required|string|max:150',
            'notes' => 'nullable|string|max:1000',
            'consultant_id' => 'nullable|exists:users,id',
            'is_confidential' => 'nullable|boolean',
        ];
    }
}
