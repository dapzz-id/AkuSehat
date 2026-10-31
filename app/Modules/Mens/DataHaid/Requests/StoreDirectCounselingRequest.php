<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDirectCounselingRequest extends FormRequest
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
            'notes' => 'nullable|string',
        ];
    }
}
