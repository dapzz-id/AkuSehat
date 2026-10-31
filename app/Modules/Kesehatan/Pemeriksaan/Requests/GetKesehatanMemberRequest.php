<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetKesehatanMemberRequest extends FormRequest
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
