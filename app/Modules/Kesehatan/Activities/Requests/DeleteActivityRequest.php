<?php

namespace App\Modules\Kesehatan\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteActivityRequest extends FormRequest
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
