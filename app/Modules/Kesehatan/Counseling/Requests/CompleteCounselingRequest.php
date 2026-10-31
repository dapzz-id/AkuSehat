<?php

namespace App\Modules\Kesehatan\Counseling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteCounselingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'notes' => 'nullable|string',
            'hasil_konseling' => 'nullable|string',
        ];
    }
}
