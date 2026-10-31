<?php

namespace App\Modules\Kesehatan\Counseling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelCounselingRequest extends FormRequest
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
