<?php

namespace App\Modules\Kesehatan\Counseling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveCounselingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => 'nullable|string',
            'ruangan' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
