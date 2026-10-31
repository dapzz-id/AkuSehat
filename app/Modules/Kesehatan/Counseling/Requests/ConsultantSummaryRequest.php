<?php

namespace App\Modules\Kesehatan\Counseling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultantSummaryRequest extends FormRequest
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
